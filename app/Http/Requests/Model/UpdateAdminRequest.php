<?php
namespace App\Http\Requests\Model;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // فقط الأدمن المصادق يستطيع تعديل حسابه هنا
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $adminId = auth('admin')->id();

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('admins', 'email')->ignore($adminId),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            // إذا تريد السماح بتعديل الدور (بالحذر)، ضع تحققًا إضافيًا في الخدمة/controller
            'role' => ['nullable', 'string', 'max:100'],
        ];
    }
}
