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
use \App\Http\Requests\Basic\BasicRequest;
use Illuminate\Http\Request;
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


    public function create(BasicRequest $request): mixed
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


    public function update(BasicRequest $request): mixed
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data) {

            $authAdmin = auth('admin')->user();
            $authEst   = auth('establishment')->user();

            $idToUpdate = $request->input('id') ?? null;

            // If authenticated as establishment-account (non-admin) → force id to the account's establishment_id
            if ($authEst && !$authAdmin) {
                $establishmentOfAccount = $authEst->establishment_id ?? null;
                if ($establishmentOfAccount === null) {
                    throw ValidationException::withMessages(['auth' => 'Authenticated establishment account has no establishment assigned.']);
                }
                $idToUpdate = (int) $establishmentOfAccount;

                // prevent changing establishment_id via payload from establishment-account
                if (array_key_exists('establishment_id', $data)) {
                    unset($data['establishment_id']);
                }
            } else {
                // Not admin and no id supplied → error
                if (empty($idToUpdate) && !$authAdmin) {
                    throw ValidationException::withMessages(['id' => 'id is required.']);
                }
            }

            // Load object
            $this->object = $this->model::findOrFail($idToUpdate);

            // Ensure establishment-account only updates its own establishment
            if ($authEst && !$authAdmin) {
                if ((int)$this->object->id !== (int)$authEst->establishment_id) {
                    throw ValidationException::withMessages(['auth' => 'You can only update your own establishment.']);
                }
            }

            // Update establishment fields safely using fill/save (respects $fillable)
            $this->object->fill($data);
            $this->object->save();

            // --- handle establishment account create/update if relevant payload provided ---
            $accountPrepared = false;
            $accountResult = null;

            $accountId = $request->input('account_id') ?? null;
            $accountEmail = $request->input('account_email') ?? null;
            $accountName = $request->input('account_name') ?? null;
            $accountPassword = $request->input('account_password') ?? null;
            $accountRole = $request->input('account_role') ?? $request->input('role') ?? null;

            $hasAccountPayload = $accountId || $accountEmail || $accountName || ($accountPassword !== null) || $accountRole;

            if ($hasAccountPayload) {
                // find target account
                $account = null;
                if ($accountId) {
                    $account = EstablishmentAccount::where('id', (int)$accountId)
                                ->where('establishment_id', $this->object->id)
                                ->first();
                    if (!$account) {
                        throw ValidationException::withMessages(['account_id' => 'Account not found for this establishment.']);
                    }
                } else {
                    if ($accountEmail) {
                        $account = EstablishmentAccount::where('email', $accountEmail)
                                    ->where('establishment_id', $this->object->id)
                                    ->first();
                    }
                    if (!$account) {
                        $account = EstablishmentAccount::where('establishment_id', $this->object->id)->first();
                    }
                }

                // If no account exists for this establishment and email provided → create new
                if (!$account && $accountEmail) {
                    // check uniqueness across establishment_accounts
                    $exists = EstablishmentAccount::where('email', $accountEmail)->first();
                    if ($exists) {
                        throw ValidationException::withMessages(['account_email' => 'Email already in use by another establishment account.']);
                    }

                    $plain = $accountPassword ?? Str::random(12);
                    $roleToUse = $accountRole ?? 'establishment_admin';

                    $account = EstablishmentAccount::create([
                        'establishment_id' => $this->object->id,
                        'name' => $accountName ?? null,
                        'email' => $accountEmail,
                        'password' => Hash::make($plain),
                        'role' => $roleToUse,
                    ]);

                    // do not return password in response (in prod consider emailing $plain instead)
                    $accountPrepared = true;
                    $accountResult = $account->toArray();
                    unset($accountResult['password']);
                } elseif ($account) {
                    // update existing account fields
                    $update = [];

                    if ($accountName !== null) $update['name'] = $accountName;

                    if ($accountEmail !== null && (string)$accountEmail !== (string)$account->email) {
                        // prevent collision
                        $exists = EstablishmentAccount::where('email', $accountEmail)
                                    ->where('id', '!=', $account->id)
                                    ->first();
                        if ($exists) {
                            throw ValidationException::withMessages(['account_email' => 'Email already in use by another account.']);
                        }
                        $update['email'] = $accountEmail;
                    }

                    if ($accountPassword !== null && $accountPassword !== '') {
                        // always hash plaintext password
                        $update['password'] = Hash::make($accountPassword);
                    }

                    if ($accountRole !== null) {
                        $update['role'] = $accountRole;
                    }

                    if (!empty($update)) {
                        $account->fill($update);
                        $account->save();
                        $accountPrepared = true;
                    }

                    $accountResult = $account->toArray();
                    unset($accountResult['password']);
                } else {
                    // no account found and no email provided to create
                    throw ValidationException::withMessages(['account' => 'No account found and no email provided to create one.']);
                }
            }

            // reload relations if any
            $this->object->loadMissing($this->relations);

            // prepare response
            if ($this->resource) {
                $establishmentArr = $this->resource::make($this->object)->resolve();
            } else {
                $establishmentArr = $this->object->toArray();
            }

            if ($accountPrepared && $accountResult) {
                $establishmentArr['account'] = $accountResult;
            }

            return $establishmentArr;
        });
    }

    /**
     * Paginated list of establishments with their accounts included.
     */
    public function allWithAccounts(Request $request): array
    {
        $perPage = (int) $request->input('per_page', 10);

        // reuse allQuery() to keep existing filters & auth restrictions
        $query = $this->allQuery();

        // ensure accounts relation is loaded
        $query->with('accounts');

        $paginator = $query->paginate($perPage);

        $items = [];
        foreach ($paginator->items() as $est) {
            $est->loadMissing('accounts');

            if ($this->resource) {
                $estArr = $this->resource::make($est)->resolve();
            } else {
                $estArr = $est->toArray();
            }

            // attach accounts (hide password)
            $estArr['accounts'] = $est->accounts->map(function ($a) {
                $arr = $a->toArray();
                if (array_key_exists('password', $arr)) unset($arr['password']);
                return $arr;
            })->all();

            $items[] = $estArr;
        }

        return [
            Str::plural(strtolower(class_basename($this->model))) => $items,
            'current_page' => $paginator->currentPage(),
            'next_page' => $paginator->nextPageUrl(),
            'previous_page' => $paginator->previousPageUrl(),
            'total_pages' => $paginator->lastPage(),
            'total_items' => $paginator->total(),
        ];
    }
}
