<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends BasicRequest
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
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'kind' => 'required|in:news,notification,alert,other',
            'sender_type' => 'nullable|string|max:255',
            'sender_id' => 'nullable|integer',
            'sent_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

}
