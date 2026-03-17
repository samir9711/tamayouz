<?php

namespace App\Services\Model\Admin;

use App\Http\Requests\Model\UpdateAdminRequest;
use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\Admin;
use App\Http\Resources\Model\AdminResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = Admin::class
        );

        $this->resource = AdminResource::class;
    }


    public function updateAuthenticatedAdmin(UpdateAdminRequest $request)
    {
        $data = $request->validated();

        $admin = auth('admin')->user();
        if (!$admin) {
            throw ValidationException::withMessages(['auth' => 'Unauthenticated.']);
        }

        return DB::transaction(function () use ($admin, $data) {


            $update = [];

            if (array_key_exists('name', $data)) {
                $update['name'] = $data['name'];
            }

            if (array_key_exists('email', $data)) {
                $update['email'] = $data['email'];
            }

            if (!empty($data['password'])) {

                $update['password'] = Hash::needsRehash($data['password']) ? Hash::make($data['password']) : $data['password'];
            }


            if (array_key_exists('role', $data) && $data['role'] !== null) {


                $update['role'] = $data['role'];
            }

            if (!empty($update)) {
                $admin->fill($update);
                $admin->save();
            }

            $admin->refresh();

            return $admin->toArray();
        });
    }
}
