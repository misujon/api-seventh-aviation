<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'from' => 'required|string|exists:airports,code',
            'to' => 'required|string|exists:airports,code',
            'departure' => 'required|date|date_format:Y-m-d',
            'return' => 'nullable|required_if:trip_type,roundtrip|date|date_format:Y-m-d|after:departure',
            'adult' => 'nullable|integer|gt:0|lt:10',
            'child' => 'nullable|integer|gt:0|lt:10',
            'infant' => 'nullable|integer|gt:0|lt:10',
            'trip_type' => 'required|string|in:oneway,roundtrip'
        ];
    }
}
