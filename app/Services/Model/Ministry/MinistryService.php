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

        return DB::transaction(function () use ($request, $data) {

            $authAdmin = auth('admin')->user();
            $authMin   = auth('ministry')->user();

            $idToUpdate = $request->input('id') ?? null;

            // إذا المستخدم هو وزارة (account) فقط — حدِّد id الوزارة من صلاحيات الحساب
            if ($authMin && !$authAdmin) {
                $ministryOfAccount = $authMin->ministry_id ?? null;
                if ($ministryOfAccount === null) {
                    throw ValidationException::withMessages(['auth' => 'Authenticated ministry account has no ministry assigned.']);
                }
                $idToUpdate = (int) $ministryOfAccount;

                // امنع تغيير ministry_id عبر payload من حساب الوزارة
                if (array_key_exists('ministry_id', $data)) {
                    unset($data['ministry_id']);
                }
            } else {
                // إذا ليس admin ولم يصل id → خطأ
                if (empty($idToUpdate) && !$authAdmin) {
                    throw ValidationException::withMessages(['id' => 'id is required.']);
                }
            }

            // جلب الكائن
            $this->object = $this->model::findOrFail($idToUpdate);

            // تحقق أن حساب الوزارة لا يقوم بتعديل وزارة غير تابع لها
            if ($authMin && !$authAdmin) {
                if ((int)$this->object->id !== (int)$authMin->ministry_id) {
                    throw ValidationException::withMessages(['auth' => 'You can only update your own ministry.']);
                }
            }

            // حدّث حقول الوزارة (استخدم fill لحماية fillable)
            $this->object->fill($data);
            $this->object->save();

            // --- معالجة حساب الوزارة (إن وُجدت حقول متعلّقة بالحساب) ---
            $accountPrepared = false;
            $accountResult = null;

            $accountId = $request->input('account_id') ?? null;
            $accountEmail = $request->input('account_email') ?? null;
            $accountName = $request->input('account_name') ?? null;
            $accountPassword = $request->input('account_password') ?? null;
            $accountRole = $request->input('account_role') ?? $request->input('role') ?? null;

            $hasAccountPayload = $accountId || $accountEmail || $accountName || ($accountPassword !== null) || $accountRole;

            if ($hasAccountPayload) {
                // حاول إيجاد الحساب المستهدف
                $account = null;
                if ($accountId) {
                    $account = MinistryAccount::where('id', (int)$accountId)
                                ->where('ministry_id', $this->object->id)
                                ->first();
                    if (!$account) {
                        throw ValidationException::withMessages(['account_id' => 'Account not found for this ministry.']);
                    }
                } else {
                    if ($accountEmail) {
                        $account = MinistryAccount::where('email', $accountEmail)
                                    ->where('ministry_id', $this->object->id)
                                    ->first();
                    }
                    if (!$account) {
                        $account = MinistryAccount::where('ministry_id', $this->object->id)->first();
                    }
                }

                // لا يوجد حساب للمينستري ولكن تم تقديم email -> أنشئ حساباً جديداً
                if (!$account && $accountEmail) {
                    // تحقّق تفرد الإيميل عبر قاعدة البيانات
                    $exists = MinistryAccount::where('email', $accountEmail)->first();
                    if ($exists) {
                        throw ValidationException::withMessages(['account_email' => 'Email already in use by another ministry account.']);
                    }

                    $plain = $accountPassword ?? Str::random(12);
                    $roleToUse = $accountRole ?? 'min_admin';

                    $account = MinistryAccount::create([
                        'ministry_id' => $this->object->id,
                        'name' => $accountName ?? null,
                        'email' => $accountEmail,
                        'password' => Hash::make($plain),
                        'role' => $roleToUse,
                    ]);

                    // لا تُرجِع كلمة المرور في الاستجابة في بيئة الإنتاج.
                    $accountPrepared = true;
                    $accountResult = $account->toArray();
                    unset($accountResult['password']); // تأكد من عدم إعادة الحقل
                } elseif ($account) {
                    // تحديث الحساب الموجود
                    $update = [];

                    if ($accountName !== null) $update['name'] = $accountName;

                    if ($accountEmail !== null && (string)$accountEmail !== (string)$account->email) {
                        $exists = MinistryAccount::where('email', $accountEmail)
                                    ->where('id', '!=', $account->id)
                                    ->first();
                        if ($exists) {
                            throw ValidationException::withMessages(['account_email' => 'Email already in use by another account.']);
                        }
                        $update['email'] = $accountEmail;
                    }

                    if ($accountPassword !== null && $accountPassword !== '') {
                        // دوِّن دائماً كلمة المرور المهشّرة
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
                    // لم يُعثر على حساب ولَمْ يُقدم email للإنشاء
                    throw ValidationException::withMessages(['account' => 'No account found and no email provided to create one.']);
                }
            }

            // أعِد تحميل العلاقات المطلوبة
            $this->object->loadMissing($this->relations);

            // جهّز المخرجات (Resource أو toArray)
            if ($this->resource) {
                $ministryArr = $this->resource::make($this->object)->resolve();
            } else {
                $ministryArr = $this->object->toArray();
            }

            if ($accountPrepared && $accountResult) {
                $ministryArr['account'] = $accountResult;
            }

            return $ministryArr;
        });
    }

    /**
     * Paginated list of ministries with their accounts included.
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
        foreach ($paginator->items() as $min) {
            $min->loadMissing('accounts');

            if ($this->resource) {
                $minArr = $this->resource::make($min)->resolve();
            } else {
                $minArr = $min->toArray();
            }

            // attach accounts
            $minArr['accounts'] = $min->accounts->map(function ($a) {
                return $a->toArray();
            })->all();

            $items[] = $minArr;
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

    /**
     * Return all ministries (no pagination) with accounts included.
     */
    public function pureAllWithAccounts(Request $request): array
    {
        $query = $this->allQuery()->with('accounts');

        $collection = $query->get();

        $items = $collection->map(function ($min) {
            if ($this->resource) {
                $minArr = $this->resource::make($min)->resolve();
            } else {
                $minArr = $min->toArray();
            }
            $min->loadMissing('accounts');
            $minArr['accounts'] = $min->accounts->map->toArray()->all();
            return $minArr;
        })->all();

        return [
            Str::plural(strtolower(class_basename($this->model))) => $items,
        ];
    }

}
