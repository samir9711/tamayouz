<?php

namespace App\Services\Model\Establishment;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\Establishment;
use App\Http\Resources\Model\EstablishmentResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\EstablishmentAccount;

class EstablishmentService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = Establishment::class
        );

        $this->resource = EstablishmentResource::class;
    }


    public function create(\App\Http\Requests\Basic\BasicRequest $request): mixed
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | 1) إنشاء المنشأة
            |--------------------------------------------------------------------------
            */

            $establishment = Establishment::create([
                'name' => $data['name'] ?? null,
                'type' => $data['type'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'location' => $data['location'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lon' => $data['lon'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'main_image' => $data['main_image'] ?? null,
                'images' => $data['images'] ?? null,
                'conditions' => $data['conditions'] ?? null,
                'website' => $data['website'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'twitter' => $data['twitter'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'youtube' => $data['youtube'] ?? null,
                'linkedin' => $data['linkedin'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2) إنشاء حساب المنشأة
            |--------------------------------------------------------------------------
            */

            $createdAccount = null;
            $generatedPassword = null;

            if (!empty($data['account_email'])) {

                $exist = EstablishmentAccount::where('email', $data['account_email'])->first();

                if ($exist) {
                    throw ValidationException::withMessages([
                        'account_email' => ['Account with this email already exists.']
                    ]);
                }

                $plainPassword = $data['account_password'] ?? Str::random(10);

                if (!isset($data['account_password'])) {
                    $generatedPassword = $plainPassword;
                }

                $role = $data['role'] ?? $data['account_role'] ?? 'establishment_admin';

                $createdAccount = EstablishmentAccount::create([
                    'establishment_id' => $establishment->id,
                    'name' => $data['account_name'] ?? null,
                    'email' => $data['account_email'],
                    'password' => Hash::make($plainPassword),
                    'role' => $role,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3) تحميل العلاقات إن وجدت
            |--------------------------------------------------------------------------
            */

            if (!empty($this->relations) && is_array($this->relations)) {
                $establishment->loadMissing($this->relations);
            }

            /*
            |--------------------------------------------------------------------------
            | 4) تحويل الناتج إلى Array
            |--------------------------------------------------------------------------
            */

            if ($this->resource) {
                $establishmentArray = $this->resource::make($establishment)->resolve();
            } else {
                $establishmentArray = $establishment->toArray();
            }

            /*
            |--------------------------------------------------------------------------
            | 5) إضافة الحساب داخل المنشأة
            |--------------------------------------------------------------------------
            */

            if ($createdAccount) {
                $establishmentArray['account'] = $createdAccount->toArray();
            }

            if ($generatedPassword) {
                $establishmentArray['generated_password'] = $generatedPassword;
            }


            return $establishmentArray;
        });
    }
}
