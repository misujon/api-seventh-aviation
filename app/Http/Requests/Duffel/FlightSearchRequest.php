<?php

namespace App\Http\Requests\Duffel;

use Illuminate\Foundation\Http\FormRequest;

class FlightSearchRequest extends FormRequest
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
            'from' => 'nullable|required_unless:trip_type,multistop|string|exists:airports,code',
            'to' => 'nullable|required_unless:trip_type,multistop|string|exists:airports,code',
            'departure' => 'nullable|required_unless:trip_type,multistop|date|date_format:Y-m-d',
            'return' => 'nullable|required_if:trip_type,roundtrip|date|date_format:Y-m-d|after:departure',
            'adult' => 'nullable|integer|gt:0|lt:10',
            'child' => 'nullable|integer|gt:0|lt:10',
            'trip_type' => 'required|string|in:oneway,roundtrip,multistop',
            'multistops' => 'nullable|required_if:trip_type,multistop|array',
            'multistops.*.from' => 'required|string|exists:airports,code',
            'multistops.*.to' => 'required|string|exists:airports,code',
            'multistops.*.departure' => 'required|date|date_format:Y-m-d',
            'cabin_class' => 'nullable|in:first,business,premium_economy,economy|string',
        ];
    }
}
