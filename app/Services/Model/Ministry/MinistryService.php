<?php

namespace App\Services\Model\Ministry;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\Ministry;
use App\Http\Resources\Model\MinistryResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Basic\BasicRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\MinistryAccount;

class MinistryService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = Ministry::class
        );

        $this->resource = MinistryResource::class;
    }



    public function create(BasicRequest $request): mixed
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {


            $ministry = Ministry::create([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'address' => $data['address'] ?? null,
                'location' => $data['location'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lon' => $data['lon'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'main_image' => $data['main_image'] ?? null,
                'images' => $data['images'] ?? null,
                'website' => $data['website'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'twitter' => $data['twitter'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'youtube' => $data['youtube'] ?? null,
                'linkedin' => $data['linkedin'] ?? null,
                'manager' => $data['manager'] ?? null,
            ]);


            $createdAccount = null;
            $generatedPassword = null;

            if (!empty($data['account_email'])) {


                $exist = MinistryAccount::where('email', $data['account_email'])->first();
                if ($exist) {
                    throw ValidationException::withMessages([
                        'account_email' => ['An account with this email already exists.']
                    ]);
                }


                $plainPassword = $data['account_password'] ?? Str::random(12);
                if (!isset($data['account_password'])) {
                    $generatedPassword = $plainPassword;
                }


                $role = $data['role'] ?? $data['account_role'] ?? 'min_admin';

                $createdAccount = MinistryAccount::create([
                    'ministry_id' => $ministry->id,
                    'name' => $data['account_name'] ?? null,
                    'email' => $data['account_email'],
                    'password' => Hash::make($plainPassword),
                    'role' => $role,
                ]);
            }

            if (!empty($this->relations) && is_array($this->relations)) {
                $ministry->loadMissing($this->relations);
            }


            if ($this->resource) {
                $ministryArray = $this->resource::make($ministry)->resolve();
            } else {
                $ministryArray = $ministry->toArray();
            }

            if ($createdAccount) {
                $ministryArray['account'] = $createdAccount->toArray();
            }

            if ($generatedPassword) {
                $ministryArray['generated_password'] = $generatedPassword;
            }


            return $ministryArray;
        });
    }



    public function getByAuth(Request $request)
    {
        $authMin = auth('ministry')->user();
        $authAdmin = auth('admin')->user();

        if ($authMin) {
            // ministry account → ارجع الوزارة الخاصة به
            $ministry = $authMin->ministry()->first();

            if (!$ministry) {
                throw ValidationException::withMessages(['ministry' => 'Ministry not found for this account.']);
            }

            return $this->resource ? $this->resource::make($ministry) : $ministry;
        }

        if ($authAdmin) {
            // Admin: يتطلب id (أو يمكنك تعديل ليُرجع الكل إذا رغبت)
            $id = $request->input('id');
            if (!$id) {
                throw ValidationException::withMessages(['id' => 'id is required when called by Admin.']);
            }

            $ministry = $this->model::findOrFail($id);
            return $this->resource ? $this->resource::make($ministry) : $ministry;
        }

        throw ValidationException::withMessages(['auth' => 'Unauthenticated.']);
    }

    /**
     * 2) Override update:
     * - Admin → يسمح فوراً بأي تعديل.
     * - MinistryAccount → يسمح فقط إذا كانت الوزارة المراد تعديلها هي نفس الوزارة الخاصة بالحساب (auth).
     */
    public function update(BasicRequest $request): mixed
    {
        $data = $request->validated();


        $authAdmin = auth('admin')->user();
        $authMin   = auth('ministry')->user();


        $idToUpdate = $request->input('id') ?? null;


        if ($authMin && !$authAdmin) {
            $ministryOfAccount = $authMin->ministry_id ?? null;
            if ($ministryOfAccount === null) {
                throw ValidationException::withMessages(['auth' => 'Authenticated ministry account has no ministry assigned.']);
            }
            $idToUpdate = (int) $ministryOfAccount;

            if (array_key_exists('ministry_id', $data)) {
                unset($data['ministry_id']);
            }
        } else {

            if (empty($idToUpdate) && !$authAdmin) {
                throw ValidationException::withMessages(['id' => 'id is required.']);
            }
        }


        $this->object = $this->model::findOrFail($idToUpdate);


        if ($authMin && !$authAdmin) {
            if ((int)$this->object->id !== (int)$authMin->ministry_id) {
                throw ValidationException::withMessages(['auth' => 'You can only update your own ministry.']);
            }
        }


        $this->object->update($data);


        $this->object->load($this->relations);

        return $this->resource ? $this->resource::make($this->object) : $this->object;
    }

}
