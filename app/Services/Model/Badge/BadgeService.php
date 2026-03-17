<?php

namespace App\Services\Model\Badge;

use App\Http\Resources\Model\BadgeDiscountResource;
use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\Badge;
use App\Http\Resources\Model\BadgeResource;
use Illuminate\Validation\ValidationException;
use App\Models\BadgeDiscount;
use App\Models\User;
use App\Models\Admin;
use App\Models\MinistryAccount;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Basic\BasicRequest;
use Carbon\Carbon;

class BadgeService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = Badge::class
        );

        $this->resource = BadgeResource::class;
        $this->relations = ['discounts', 'user', 'ministry','discounts.establishment'];
    }



    public function create(BasicRequest $request): mixed
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {


            $user = User::with('directorate')->findOrFail($data['user_id']);


            $auth = auth()->user();
            if (!$auth) {
                throw ValidationException::withMessages(['auth' => 'Unauthenticated.']);
            }


            if (!($auth instanceof Admin) && !($auth instanceof MinistryAccount)) {
                throw ValidationException::withMessages(['auth' => 'Not authorized to assign badges.']);
            }


            $ministryId = null;
            if ($auth instanceof MinistryAccount) {

                $ministryId = (int) $auth->ministry_id;


                $userMinistryId = $user->directorate?->ministry_id;
                if ($userMinistryId === null || $userMinistryId !== $ministryId) {
                    throw ValidationException::withMessages([
                        'auth' => 'You can only assign badges to employees of your ministry.'
                    ]);
                }
            } elseif ($auth instanceof Admin) {

                if (empty($data['ministry_id'])) {
                    throw ValidationException::withMessages([
                        'ministry_id' => 'ministry_id is required when performed by an Admin.'
                    ]);
                }
                $ministryId = (int) $data['ministry_id'];
            }


            $badge = Badge::where('user_id', $user->id)->first();

            if (!$badge) {
                $badgePayload = [
                    'user_id' => $user->id,

                    'ministry_id' => $ministryId ?? $user->directorate?->ministry_id,
                    'code' => $data['code'] ?? (string) Str::uuid(),
                    'title' => $data['title'] ?? null,
                    'description' => $data['description'] ?? null,
                ];
                $badge = Badge::create(array_filter($badgePayload, fn($v) => $v !== null));
            } else {

                // if ($auth instanceof Admin && isset($data['ministry_id']) && $badge->ministry_id !== (int)$data['ministry_id']) {
                //     $badge->update(['ministry_id' => (int)$data['ministry_id']]);
                // }
                //

                // $badge->fill(array_filter([
                //     'title' => $data['title'] ?? null,
                //     'description' => $data['description'] ?? null,
                // ], fn($v) => $v !== null))->save();
            }


            $discountPayload = [
                'badge_id' => $badge->id,
                'establishment_id' => $data['establishment_id'] ?? null,
                'title' => $data['discount_title'] ?? $data['title'] ?? null,
                'description' => $data['discount_description'] ?? null,
                'note' => $data['note'] ?? null,
                'discount_percent' => $data['discount_percent'] ?? 0,
                'valid_from' => $data['valid_from'] ?? null,
                'valid_until' => $data['valid_until'] ?? null,
                'status' => $data['status'] ?? 1,
                'categories' => $data['categories'] ?? null,
            ];

            $badge->discounts()->create(array_filter($discountPayload, fn($v) => $v !== null));


            $badge->load('discounts', 'user', 'ministry','discounts.establishment');

            return $this->resource::make($badge);
        });
    }


     protected function allQuery() : object
    {
        $query = $this->model::withFilters()
            ->with($this->relations)
            ->orderBy('created_at', 'desc');

        $auth = auth()->user();

        if (!$auth) {
            // unauthenticated — let controller/middleware normally block this,
            // but keep consistent error if called directly.
            throw ValidationException::withMessages(['auth' => 'Unauthenticated.']);
        }

        if ($auth instanceof User) {
            // employee -> only their own badge(s)
            $query->where('user_id', $auth->id);
        } elseif ($auth instanceof MinistryAccount) {
            // ministry account -> only badges belonging to this ministry
            // (we store ministry_id on badges when created by ministry accounts)
            $query->where('ministry_id', (int)$auth->ministry_id);
        } elseif ($auth instanceof Admin) {
            // admin -> no extra restriction
        } else {
            // other auth types: deny
            throw ValidationException::withMessages(['auth' => 'Not authorized to view badges.']);
        }

        return $query;
    }

    /**
     * Override show to enforce per-item access rules.
     */
    public function show($request): mixed
    {
        // load including trashed/counts as original
        $this->object = $this->model::with($this->relations)
            ->withTrashed()
            ->withCount($this->countRelations)
            ->findOrFail($request->id);

        $auth = auth()->user();
        if (!$auth) {
            throw ValidationException::withMessages(['auth' => 'Unauthenticated.']);
        }

        // owner user can only view their own badge
        if ($auth instanceof User && (int)$this->object->user_id !== (int)$auth->id) {
            throw ValidationException::withMessages(['auth' => 'You do not have permission to view this badge.']);
        }

        // ministry account can view only badges of their ministry
        if ($auth instanceof MinistryAccount && (int)$this->object->ministry_id !== (int)$auth->ministry_id) {
            throw ValidationException::withMessages(['auth' => 'You can only view badges for your ministry.']);
        }

        // admin can view everything

        return $this->resource::make($this->object);
    }




    public function scanQr(string $code, ?int $establishmentId = null, $scanner = null)
    {

        return DB::transaction(function () use ($code, $establishmentId, $scanner) {

            $now = Carbon::now();


            $badge = Badge::where('code', $code)->first();
            if (!$badge) {
                throw ValidationException::withMessages(['code' => 'Badge not found for provided code.']);
            }


            if ($establishmentId === null && function_exists('auth')) {

                if (auth('establishment')->check()) {
                    $establishmentId = auth('establishment')->user()->id;
                }
            }

            if ($establishmentId === null) {
                throw ValidationException::withMessages(['establishment_id' => 'establishment_id is required (or use an establishment auth).']);
            }


            $discount = $badge->discounts()
                ->where('establishment_id', $establishmentId)
                ->where(function($q){
                    $q->where('status', 'active')
                    ->orWhere('status', 1)
                    ->orWhere('status', '1');
                })
                ->whereNull('scanned_at')
                ->lockForUpdate()
                ->first();

            if (!$discount) {

                $maybe = $badge->discounts()->where('establishment_id', $establishmentId)->first();
                if (!$maybe) {
                    throw ValidationException::withMessages(['establishment' => 'This badge has no discount for the provided establishment.']);
                }


                if ($maybe->scanned_at !== null) {
                    throw ValidationException::withMessages(['discount' => 'This discount was already scanned.']);
                }


                $now = Carbon::now();
                if (($maybe->valid_from && $now->lt($maybe->valid_from)) ||
                    ($maybe->valid_until && $now->gt($maybe->valid_until))) {
                    throw ValidationException::withMessages(['discount' => 'This discount is not valid at the current time.']);
                }


                throw ValidationException::withMessages(['discount' => 'Discount is not active.']);
            }


            if ($discount->valid_from && $now->lt($discount->valid_from)) {
                throw ValidationException::withMessages(['discount' => 'Discount validity has not started yet.']);
            }
            if ($discount->valid_until && $now->gt($discount->valid_until)) {
                throw ValidationException::withMessages(['discount' => 'Discount validity has expired.']);
            }


            $discount->scanned_at = $now;
            $discount->status = 'expired';
            $discount->save();


            return [
                'message' => 'scan_ok',
                'scanned_at' => $now->toDateTimeString(),
                'badge_id' => $badge->id,
                'badge_code' => $badge->code,
                'title' => $discount->title,
                'description' => $discount->description,
                'note' => $discount->note,
                'discount_id' => $discount->id,
                'establishment_id' => $discount->establishment_id,
                'discount_percent' => $discount->discount_percent,
                'valid_from' => $discount->valid_from?->toDateTimeString(),
                'valid_until' => $discount->valid_until?->toDateTimeString(),
            ];
        });
    }


    public function getScannedByEstablishment(int $establishmentId, int $perPage = 15): array
    {
        $query = BadgeDiscount::with(['badge.user', 'badge.ministry', 'establishment'])
        ->where('establishment_id', $establishmentId)
        ->whereNotNull('scanned_at')
        ->orderByDesc('scanned_at');

        $paginator = $query->paginate($perPage);


        $items = array_map(function (BadgeDiscount $d) {

            $d->loadMissing(['badge.user', 'badge.ministry', 'establishment']);

            $badge = $d->badge;
            $est   = $d->establishment;

            $badgeArr = null;
            if ($badge) {
                $badgeArr = $badge->toArray();


                if ($badge->relationLoaded('user') && $badge->user) {
                    $badgeArr['user'] = $badge->user->toArray();
                } elseif ($badge->user) {
                    $badgeArr['user'] = $badge->user->toArray();
                } else {
                    $badgeArr['user'] = null;
                }


                if ($badge->relationLoaded('ministry') && $badge->ministry) {
                    $badgeArr['ministry'] = $badge->ministry->toArray();
                } elseif ($badge->ministry) {
                    $badgeArr['ministry'] = $badge->ministry->toArray();
                } else {
                    $badgeArr['ministry'] = null;
                }
            }

            $estArr = $est ? $est->toArray() : null;


            $discountArr = $d->toArray();


            if (isset($discountArr['valid_from']) && $discountArr['valid_from'] instanceof \DateTime) {
                $discountArr['valid_from'] = $discountArr['valid_from']->format('Y-m-d H:i:s');
            }
            if (isset($discountArr['valid_until']) && $discountArr['valid_until'] instanceof \DateTime) {
                $discountArr['valid_until'] = $discountArr['valid_until']->format('Y-m-d H:i:s');
            }
            if (isset($discountArr['scanned_at']) && $discountArr['scanned_at'] instanceof \DateTime) {
                $discountArr['scanned_at'] = $discountArr['scanned_at']->format('Y-m-d H:i:s');
            }


            $discountArr['establishment'] = $estArr;
            $discountArr['badge'] = $badgeArr;

            return $discountArr;
        }, $paginator->items());

        return [
            'discounts'     => array_values($items),
            'current_page'  => $paginator->currentPage(),
            'next_page'     => $paginator->nextPageUrl(),
            'previous_page' => $paginator->previousPageUrl(),
            'total_pages'   => $paginator->lastPage(),
            'total_items'   => $paginator->total(),
        ];
    }

}
