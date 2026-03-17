<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationTargetRequest extends BasicRequest
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
            'target_type' => 'required|in:user,directorate,ministry,all',
            'target_id' => 'nullable|integer',
        ];
    }

}
