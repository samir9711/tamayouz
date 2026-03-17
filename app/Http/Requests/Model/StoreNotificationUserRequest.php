<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationUserRequest extends BasicRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notification_id' => 'required|integer|exists:notifications,id',
            'user_id' => 'required|integer|exists:users,id',
            'read_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

}
