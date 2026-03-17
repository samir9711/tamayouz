<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreBadgeDiscountRequest extends BasicRequest
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
            'badge_id' => 'required|integer|exists:badges,id',
            'establishment_id' => 'nullable|integer|exists:establishments,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'note' => 'nullable|string|max:255',
            'discount_percent' => 'nullable|numeric',
            'valid_from' => 'nullable|date_format:Y-m-d H:i:s',
            'valid_until' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => 'required|in:active,revoked,expired',
            'categories' => 'nullable|array',
            'scanned_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

}
