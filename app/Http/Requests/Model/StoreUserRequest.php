<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends BasicRequest
{
    protected bool $isUpdate = false;
    protected $currentId = null;
    public function authorize(): bool
    {
        // detect id same way as prepareForValidation (prioritize body id)
        $id = $this->input('id') ?? $this->route('id') ?? $this->route('user') ?? $this->input('user.id') ?? null;
        if (is_object($id) && isset($id->id)) {
            $id = $id->id;
        }
        if (is_numeric($id)) {
            $id = (int) $id;
        } else {
            $id = null;
        }

        // not an update -> allow (creation handled by rules)
        if (!$id) {
            return true;
        }

        // if admin -> allow
        if (auth('admin')->check()) {
            return true;
        }

        // if ministry guard -> ensure the target user belongs to same ministry
        if (auth('ministry')->check()) {
            $ministryAccount = auth('ministry')->user();
            $ministryId = $ministryAccount?->ministry_id;

            // if ministryAccount missing or ministry_id missing -> deny
            if (!$ministryId) {
                throw new HttpResponseException(response()->json([
                    'data' => null,
                    'status' => false,
                    'error' => 'Authentication (ministry) required or invalid ministry account',
                ], 401));
            }

            $target = User::with('directorate')->find($id);
            if (!$target) {
                throw new HttpResponseException(response()->json([
                    'data' => null,
                    'status' => false,
                    'error' => 'Target user not found',
                ], 404));
            }

            $targetMinistryId = $target->directorate?->ministry_id;
            if ($targetMinistryId !== $ministryId) {
                // This is the key: stop request early with a clear message
                throw new HttpResponseException(response()->json([
                    'data' => null,
                    'status' => false,
                    'error' => 'Unauthorized to modify this user (different ministry)',
                ], 403));
            }

            return true;
        }

        // other guards: deny by default
        throw new HttpResponseException(response()->json([
            'data' => null,
            'status' => false,
            'error' => 'Authentication required (admin or ministry).',
        ], 401));
    }

    protected function prepareForValidation(): void
    {

        $id = $this->input('id') ?? $this->route('id') ?? $this->route('user') ?? $this->input('user.id') ?? null;


        if (is_object($id) && isset($id->id)) {
            $id = $id->id;
        }

        if (is_numeric($id)) {
            $id = (int) $id;
        } else {
            $id = null;
        }

        $this->currentId = $id;
        $this->isUpdate  = !empty($this->currentId);

        if ($this->isUpdate && !$this->has('id')) {
            $this->merge(['id' => $this->currentId]);
        }
    }

    public function rules(): array
    {

        $emailUniqueRule = Rule::unique('users', 'email');
        $phoneUniqueRule = Rule::unique('users', 'phone');

        if ($this->currentId) {
            $emailUniqueRule = $emailUniqueRule->ignore($this->currentId);
            $phoneUniqueRule = $phoneUniqueRule->ignore($this->currentId);
        }

        if ($this->isUpdate) {
            return [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:255',
                'gender' => 'nullable|in:male,female',
                'birth_date' => 'nullable|date',
                'residence' => 'nullable|string|max:255',
                'email' => ['sometimes', 'required', 'string', 'email', 'max:255', $emailUniqueRule],
                'phone' => ['sometimes', 'required', 'string', 'max:255', $phoneUniqueRule],
                'email_verified_at' => 'nullable|date_format:Y-m-d H:i:s',
                'email_status' => 'nullable|boolean',
                'activated_at' => 'nullable|date_format:Y-m-d H:i:s',
                // عند التحديث: كلمة المرور اختيارية
                'password' => 'nullable|string|max:255',
                'status' => 'nullable|boolean',
                'otp_delivery_method' => 'nullable|in:sms,whatsapp,email',
                'remember_token' => 'nullable|string|max:100',
                'national_number'=>'nullable|string',
                'directorate_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('directorates', 'id')->where(function ($query) {
                        $ministryAccount = $this->user('ministry') ?? auth('ministry')->user();
                        $ministryId = $ministryAccount?->ministry_id;

                        if ($ministryId) {
                            $query->where('ministry_id', $ministryId);
                        } else {
                            $query->whereRaw('1 = 0');
                        }
                    }),
                ],
            ];
        }

        // إنشاء جديد
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'residence' => 'nullable|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', $emailUniqueRule],
            'phone' => ['nullable', 'string', 'max:255', $phoneUniqueRule],
            'email_verified_at' => 'nullable|date_format:Y-m-d H:i:s',
            'email_status' => 'nullable|boolean',
            'activated_at' => 'nullable|date_format:Y-m-d H:i:s',
            'password' => 'required|string|max:255',
            'status' => 'nullable|boolean',
            'otp_delivery_method' => 'nullable|in:sms,whatsapp,email',
            'remember_token' => 'nullable|string|max:100',
            'national_number'=>'nullable|string',
            'directorate_id' => [
                'nullable',
                'integer',
                Rule::exists('directorates', 'id')->where(function ($query) {
                    $ministryAccount = $this->user('ministry') ?? auth('ministry')->user();
                    $ministryId = $ministryAccount?->ministry_id;

                    if ($ministryId) {
                        $query->where('ministry_id', $ministryId);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                }),
            ],
        ];
    }
}
