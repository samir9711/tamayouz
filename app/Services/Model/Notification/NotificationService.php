<?php

namespace App\Services\Model\Notification;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\Notification;
use App\Http\Resources\Model\NotificationResource;
use App\Models\NotificationTarget;
use App\Models\NotificationUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class NotificationService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = Notification::class
        );

        $this->resource = NotificationResource::class;
    }








    public static function sendFromMinistry($ministryAccount, array $payload): Notification
    {
        if (!$ministryAccount) {
            throw ValidationException::withMessages(['auth' => 'Unauthenticated sender.']);
        }

        $now = Carbon::now();
        $sentAt = isset($payload['sent_at']) ? Carbon::parse($payload['sent_at']) : $now;

        $target = $payload['target'] ?? null;
        if (!is_array($target) || empty($target['type'])) {
            throw ValidationException::withMessages(['target' => 'Invalid target.']);
        }

        return DB::transaction(function () use ($ministryAccount, $payload, $target, $sentAt, $now) {
            $notif = Notification::create([
                'title' => $payload['title'] ?? null,
                'body' => $payload['body'] ?? null,
                'kind' => $payload['kind'] ?? 'notification',
                'sender_type' => get_class($ministryAccount),
                'sender_id' => $ministryAccount->id,
                'sent_at' => $sentAt,
            ]);

            $targetType = $target['type'];

            $insertUsers = function(array $userIds) use ($notif, $now) {
                $userIds = array_values(array_unique(array_map('intval', $userIds)));
                if (count($userIds) === 0) return;

                $existing = NotificationUser::where('notification_id', $notif->id)
                    ->whereIn('user_id', $userIds)
                    ->pluck('user_id')
                    ->toArray();

                $toInsert = array_diff($userIds, $existing);
                if (empty($toInsert)) return;

                $rows = [];
                foreach ($toInsert as $uid) {
                    $rows[] = [
                        'notification_id' => $notif->id,
                        'user_id' => $uid,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $chunks = array_chunk($rows, 1000);
                foreach ($chunks as $chunk) {
                    DB::table((new NotificationUser)->getTable())->insert($chunk);
                }
            };

            if ($targetType === 'user') {
                if (empty($target['id'])) {
                    throw ValidationException::withMessages(['target' => 'target.id is required for type=user']);
                }
                $userId = (int) $target['id'];

                $user = User::find($userId);
                if (!$user) {
                    throw ValidationException::withMessages(['target' => 'User not found.']);
                }
                $userMinistryId = $user->directorate?->ministry_id;
                if ((int)$userMinistryId !== (int)$ministryAccount->ministry_id) {
                    throw ValidationException::withMessages(['target' => 'User does not belong to your ministry.']);
                }

                NotificationTarget::create([
                    'notification_id' => $notif->id,
                    'target_type' => 'user',
                    'target_id' => $userId,
                ]);
                $insertUsers([$userId]);

            } elseif ($targetType === 'users') {
                if (empty($target['ids']) || !is_array($target['ids'])) {
                    throw ValidationException::withMessages(['target' => 'target.ids array required for type=users']);
                }
                $ids = array_map('intval', $target['ids']);

                $validIds = User::whereIn('id', $ids)
                    ->whereHas('directorate', function($q) use ($ministryAccount) {
                        $q->where('ministry_id', (int)$ministryAccount->ministry_id);
                    })->pluck('id')->toArray();

                if (empty($validIds)) {
                    throw ValidationException::withMessages(['target' => 'No valid users found in your ministry.']);
                }

                NotificationTarget::create([
                    'notification_id' => $notif->id,
                    'target_type' => 'users',
                    'target_id' => null,
                ]);
                $insertUsers($validIds);

            } elseif ($targetType === 'ministry') {
                // هنا: تجاهل أي target.id مرسَل واستخدم ministry_id من التوكين
                $minId = (int) $ministryAccount->ministry_id;

                NotificationTarget::create([
                    'notification_id' => $notif->id,
                    'target_type' => 'ministry',
                    'target_id' => $minId,
                ]);

                User::whereHas('directorate', function($q) use ($minId) {
                    $q->where('ministry_id', $minId);
                })->select('id')->chunkById(1000, function($users) use ($insertUsers) {
                    $ids = $users->pluck('id')->toArray();
                    $insertUsers($ids);
                });
            } else {
                throw ValidationException::withMessages(['target' => 'Unsupported target.type']);
            }

            return $notif;
        });
    }



    public static function getForUser($user, int $perPage = 15, bool $onlyUnread = false): array
    {
        $query = NotificationUser::with(['notification.sender']) // preload notification + sender
            ->where('user_id', (int)$user->id);

        if ($onlyUnread) {
            $query->whereNull('read_at');
        }

        $p = $query->orderByDesc('created_at')->paginate($perPage);

        // Transform items into plain arrays with notification details
        $items = array_map(function ($row) {
            // $row is NotificationUser model
            $notif = $row->notification;
            $notifArr = $notif ? $notif->toArray() : null;

            // include sender minimal info if exists
            if ($notif && $notif->relationLoaded('sender') && $notif->sender) {
                // convert sender to an object/array with type + id + optional name/email
                $sender = $notif->sender;
                $notifArr['sender'] = [
                    'type' => $notif->sender_type,
                    'id'   => $notif->sender_id,
                    // try include common fields if exist (name/email)
                    'name' => $sender->name ?? ($sender->email ?? null),
                ];
            }

            return [
                'pivot_id'       => $row->id, // id in notification_users
                'notification_id'=> $row->notification_id,
                'read_at'        => $row->read_at ? $row->read_at->toDateTimeString() : null,
                'created_at'     => $row->created_at?->toDateTimeString(),
                'notification'   => $notifArr,
                'is_read'        => $row->read_at !== null,
            ];
        }, $p->items());

        $unreadCount = NotificationUser::where('user_id', (int)$user->id)->whereNull('read_at')->count();

        return [
            'notifications' => array_values($items),
            'meta' => [
                'current_page' => $p->currentPage(),
                'per_page' => $p->perPage(),
                'last_page' => $p->lastPage(),
                'total' => $p->total(),
                'next_page_url' => $p->nextPageUrl(),
                'prev_page_url' => $p->previousPageUrl(),
            ],
            'unread_count' => $unreadCount,
        ];
    }

}
