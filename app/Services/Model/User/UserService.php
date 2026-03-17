<?php

namespace App\Services\Model\User;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\User;
use App\Http\Resources\Model\UserResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Basic\BasicRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;


class UserService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = User::class
        );

        $this->resource = UserResource::class;
        $this->key = 'user';
        $this->relations = ['directorate','directorate.ministry'];
    }



    public function create(BasicRequest $request): mixed
    {
        $data = $request->validated();


        $data['email_status'] = $data['email_status'] ?? true;
        $data['activated_at'] = $data['activated_at'] ?? now();
        $data['status'] = $data['status'] ?? true;
        $data['email_verified_at'] = $data['email_verified_at'] ?? now();
        $data['otp_delivery_method'] = $data['otp_delivery_method'] ?? 'email';


        if (isset($data['password']) && $data['password'] !== null) {
            $data['password'] = Hash::make($data['password']);
        }


        $this->object = $this->model::create($data);
        $this->object->load($this->relations);


        return $this->resource::make($this->object);
    }



     public function all($request): mixed
    {
        // auth guard decision
        if (!auth('admin')->check() && !auth('ministry')->check()) {
            throw new HttpResponseException(response()->json([
                'data' => null,
                'status' => false,
                'error' => 'Authentication required (admin or ministry).',
            ], 401));
        }

        $query = $this->model::withFilters()

            ->with($this->relations);

        if (auth('ministry')->check() && !auth('admin')->check()) {
            $ministryAccount = auth('ministry')->user();
            $ministryId = $ministryAccount?->ministry_id;
            if (!$ministryId) {
                return $this->resource::collection(collect());
            }
            $query->whereHas('directorate', function ($q) use ($ministryId) {
                $q->where('ministry_id', $ministryId);
            });
        }

        $data = $query->get();
        return $this->resource::collection($data);
    }

   
    public function allPaginated($request): mixed
    {
        if (!auth('admin')->check() && !auth('ministry')->check()) {
            throw new HttpResponseException(response()->json([
                'data' => null,
                'status' => false,
                'error' => 'Authentication required (admin or ministry).',
            ], 401));
        }

        $query = $this->model::withFilters()

            ->with($this->relations);

        if (auth('ministry')->check() && !auth('admin')->check()) {
            $ministryAccount = auth('ministry')->user();
            $ministryId = $ministryAccount?->ministry_id;
            if (!$ministryId) {
                $empty = $this->model::whereRaw('0 = 1')->paginate(
                    $request->input('per_page', 10),
                    ['*'],
                    'page',
                    $request->input('page', 1)
                );
                return [
                    Str::plural(strtolower(class_basename($this->model))) => $this->resource::collection($empty),
                    'current_page' => $empty->currentPage(),
                    'next_page' => $empty->nextPageUrl(),
                    'previous_page' => $empty->previousPageUrl(),
                    'total_pages' => $empty->lastPage(),
                ];
            }
            $query->whereHas('directorate', function ($q) use ($ministryId) {
                $q->where('ministry_id', $ministryId);
            });
        }

        $data = $query->paginate(
            $request->input('per_page', 10),
            ['*'],
            'page',
            $request->input('page', 1)
        );

        return [
            Str::plural(strtolower(class_basename($this->model))) => $this->resource::collection($data),
            'current_page' => $data->currentPage(),
            'next_page' => $data->nextPageUrl(),
            'previous_page' => $data->previousPageUrl(),
            'total_pages' => $data->lastPage(),
        ];
    }

    /**
     * show: admin -> الكل | ministry -> فقط موظف الوزارة المطابق
     */
    public function show( $request): mixed
    {
        if (!auth('admin')->check() && !auth('ministry')->check()) {
            throw new HttpResponseException(response()->json([
                'data' => null,
                'status' => false,
                'error' => 'Authentication required (admin or ministry).',
            ], 401));
        }

        $this->object = $this->model::with($this->relations)
            ->withTrashed()
            ->withCount($this->countRelations)
            ->findOrFail($request->id);

        if (auth('ministry')->check() && !auth('admin')->check()) {
            $ministryAccount = auth('ministry')->user();
            $ministryId = $ministryAccount?->ministry_id;
            if (!$ministryId || !$this->object->directorate || $this->object->directorate->ministry_id != $ministryId) {
                throw new HttpResponseException(response()->json([
                    'data' => null,
                    'status' => false,
                    'error' => __('messages.unauthorized') ?? 'Unauthorized',
                ], 403));
            }
        }

        return $this->resource::make($this->object);
    }

    /**
     * update: تحقق صلاحية، تشفير كلمة المرور عند وجودها، ثم التحديث
     */
    public function update(BasicRequest  $request): mixed
    {
        if (!auth('admin')->check() && !auth('ministry')->check()) {
            throw new HttpResponseException(response()->json([
                'data' => null,
                'status' => false,
                'error' => 'Authentication required (admin or ministry).',
            ], 401));
        }

        $this->object = $this->model::with($this->relations)->findOrFail($request->id);

        if (auth('ministry')->check() && !auth('admin')->check()) {
            $ministryAccount = auth('ministry')->user();
            $ministryId = $ministryAccount?->ministry_id;
            if (!$ministryId || !$this->object->directorate || $this->object->directorate->ministry_id != $ministryId) {
                throw new HttpResponseException(response()->json([
                    'data' => null,
                    'status' => false,
                    'error' => __('messages.unauthorized') ?? 'Unauthorized',
                ], 403));
            }
        }

        $data = $request->validated();

        if (isset($data['password']) && $data['password'] !== null && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            // لا نريد أن نمرر password فارغ ويُلغى في DB — احذف المفتاح إن لم يُرسل
            unset($data['password']);
        }

        $this->object->update($data);
        $this->object->load($this->relations);

        return $this->resource::make($this->object);
    }



    /**
     * delete (forceDelete): admin أو ministry (متحقق من الانتماء)
     */
    public function delete( $request): bool
    {
        if (!auth('admin')->check() && !auth('ministry')->check()) {
            throw new HttpResponseException(response()->json([
                'data' => null,
                'status' => false,
                'error' => 'Authentication required (admin or ministry).',
            ], 401));
        }

        $this->object = $this->model::findOrFail($request->id);

        if (auth('admin')->check()) {
            return $this->object->forceDelete();
        }

        // ministry
        $ministryAccount = auth('ministry')->user();
        $ministryId = $ministryAccount?->ministry_id;
        if ($ministryId && $this->object->directorate && $this->object->directorate->ministry_id == $ministryId) {
            return $this->object->forceDelete();
        }

        throw new HttpResponseException(response()->json([
            'data' => null,
            'status' => false,
            'error' => __('messages.unauthorized') ?? 'Unauthorized',
        ], 403));
    }
}
