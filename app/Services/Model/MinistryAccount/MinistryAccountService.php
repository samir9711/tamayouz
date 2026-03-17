<?php

namespace App\Services\Model\MinistryAccount;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\MinistryAccount;
use App\Http\Resources\Model\MinistryAccountResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Basic\BasicRequest;

class MinistryAccountService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = MinistryAccount::class
        );

        $this->resource = MinistryAccountResource::class;
         $this->relations = ['ministry'];
    }



    protected function allQuery(): object
    {
        $query = $this->model::withFilters()
            ->orderBy('created_at', 'desc');

        $authAdmin = auth('admin')->user();
        $authMin   = auth('ministry')->user();

        if ($authAdmin) {
            return $query;
        }

        if ($authMin) {

            return $query->where('ministry_id', (int)$authMin->ministry_id);
        }

        abort(401, 'Unauthenticated.');
    }



    public function show($request): mixed
    {
        $this->object = $this->model::findOrFail($request->id);

        $authAdmin = auth('admin')->user();
        $authMin   = auth('ministry')->user();

        if ($authAdmin) {
            return $this->resource::make($this->object);
        }

        if ($authMin) {
            if ((int)$this->object->ministry_id !== (int)$authMin->ministry_id) {
                throw ValidationException::withMessages(['auth' => 'You may only view accounts for your ministry.']);
            }
            return $this->resource::make($this->object);
        }

        throw ValidationException::withMessages(['auth' => 'Unauthenticated.']);
    }



    public function create(BasicRequest $request): mixed
    {
        $data = $request->validated();

        $authAdmin = auth('admin')->user();
        $authMin   = auth('ministry')->user();

        if (!$authAdmin && !$authMin) {
            throw ValidationException::withMessages(['auth' => 'Unauthenticated.']);
        }

        // Admin يمكنه تمرير ministry_id (إن أردت)، أما ministry account فلن يمرّر ويُستخدم ministry_id الخاص به
        if ($authMin) {
            $data['ministry_id'] = $authMin->ministry_id;
        } else {
            // admin: تأكد أن ministry_id موجود (أو ضع قواعد request تدقق ذلك)
            if (empty($data['ministry_id'])) {
                throw ValidationException::withMessages(['ministry_id' => 'ministry_id is required for Admin.']);
            }
        }

        // create using BasicCrudService logic (مثال)
        $this->object = $this->model::create($data);
        return $this->resource::make($this->object->load($this->relations));
    }



    public function update(BasicRequest $request): mixed
    {
        $data = $request->validated();


        if (array_key_exists('ministry_id', $data)) {
            unset($data['ministry_id']);
        }

        $this->object = $this->model::findOrFail($request->id);

       
        $authAdmin = auth('admin')->user();
        $authMin   = auth('ministry')->user();
        if ($authMin && (int)$this->object->ministry_id !== (int)$authMin->ministry_id) {
            throw ValidationException::withMessages(['auth' => 'You can only update accounts of your ministry.']);
        }

        $this->object->update($data);
        return $this->resource::make($this->object);
    }




}
