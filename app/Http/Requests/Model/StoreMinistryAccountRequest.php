<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreMinistryAccountRequest extends BasicRequest
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
            'ministry_id' => 'prohibited',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:ministry_accounts,email',
            'password' => 'required|string|max:255',
            'role' => 'required|in:min_subadmin,min_admin',
        ];
    }

}
