<?php

namespace App\Services\Model\Directorate;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\Directorate;
use App\Http\Resources\Model\DirectorateResource;
use App\Http\Requests\Basic\BasicRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;



class DirectorateService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = Directorate::class
        );

        $this->resource = DirectorateResource::class;
        $this->relations = ['ministry'];
    }




    public function create( $request): mixed
    {
        $data = $request->validated();

        // حالة: مستخدم ministry (guard 'ministry')
        if (auth('ministry')->check()) {
            $ministryAccount = auth('ministry')->user();
            if (!$ministryAccount || empty($ministryAccount->ministry_id)) {
                throw new HttpResponseException(response()->json([
                    'data' => null,
                    'status' => false,
                    'error' => 'Authenticated ministry account does not have ministry_id',
                ], 422));
            }

            // اضبط ministry_id مدفوعاً من التوكن (تجاوز أي قيمة مرسلة)
            $data['ministry_id'] = $ministryAccount->ministry_id;
        } else {
            // حالة: admin أو مستخدم آخر
            // إذا لم يكن admin وتمرير ministry_id غير موجود فافشل
            if (!auth('admin')->check() && empty($data['ministry_id'])) {
                throw new HttpResponseException(response()->json([
                    'data' => null,
                    'status' => false,
                    'error' => 'ministry_id is required for non-ministry users',
                ], 422));
            }
            // إذا كان admin فاترك ministry_id كما مرَر (StoreDirectorateRequest يتحقق من وجوده)
        }

        // انشاء المديرية
        $this->object = $this->model::create($data);
        $this->object->load($this->relations);

        return $this->resource::make($this->object);
    }



    public function all(Request $request): mixed
    {
        $onlyMine = $request->boolean('only_mine', false);

        // إذا طُلب التصفية حسب الوزارة، فتأكد من وجود مصادقة ministry
        if ($onlyMine && !auth('ministry')->check()) {
            throw new HttpResponseException(response()->json([
                'data' => null,
                'status' => false,
                'error' => 'Authentication (ministry) required when using only_mine filter',
            ], 401));
        }

        $query = $this->model::withFilters()

            ->with($this->relations)
            ->orderBy('created_at', 'desc');

        if ($onlyMine) {
            $ministryAccount = auth('ministry')->user();
            $ministryId = $ministryAccount?->ministry_id;
            if (!$ministryId) {
                // لا توجد وزارة في الحساب -> لا نُرجع شيئاً
                return $this->resource::collection(collect());
            }
            $query->where('ministry_id', $ministryId);
        }

        $data = $query->get();
        return $this->resource::collection($data);
    }

    /**
     * allPaginated: عام مع pagination — يدعم نفس باراميتر only_mine
     */
    public function allPaginated(Request $request): mixed
    {
        $onlyMine = $request->boolean('only_mine', false);

        if ($onlyMine && !auth('ministry')->check()) {
            throw new HttpResponseException(response()->json([
                'data' => null,
                'status' => false,
                'error' => 'Authentication (ministry) required when using only_mine filter',
            ], 401));
        }

        $query = $this->model::withFilters()

            ->with($this->relations)
            ->orderBy('created_at', 'desc');

        if ($onlyMine) {
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
            $query->where('ministry_id', $ministryId);
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



    public function delete(Request $request): bool
    {
        $this->object = $this->model::findOrFail($request->id);

        // admin يسمح بـ force delete
        if (auth('admin')->check()) {
            return $this->object->forceDelete();
        }

        // ministry: فقط إذا كانت المديرية تابعة لوزارة صاحب التوكن
        if (auth('ministry')->check()) {
            $ministryAccount = auth('ministry')->user();
            $ministryId = $ministryAccount?->ministry_id;
            if ($ministryId && $this->object->ministry_id == $ministryId) {
                return $this->object->forceDelete();
            }
        }

        throw new HttpResponseException(response()->json([
            'data' => null,
            'status' => false,
            'error' => __('messages.unauthorized') ?? 'Unauthorized',
        ], 403));
    }

}
