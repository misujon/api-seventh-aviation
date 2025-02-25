<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            'passengers' => 'required|array',
            'passengers.*.id' => 'required|string',
            'passengers.*.name' => 'required|array',
            'passengers.*.name.firstName' => 'required|string|max:100',
            'passengers.*.name.lastName' => 'required|string|max:100',
            'passengers.*.gender' => 'required|string|in:MALE,FEMALE,OTHERS',
            'passengers.*.contact' => 'required|array',
            'passengers.*.contact.emailAddress' => 'required|string|email',
            'passengers.*.contact.phones' => 'required|array',
            'passengers.*.contact.phones.*.deviceType' => 'required|string|in:MOBILE,TELEPHONE',
            'passengers.*.contact.phones.*.countryCallingCode' => 'required|string|max:3',
            'passengers.*.contact.phones.*.number' => 'required|string|max:15',
            'passengers.*.documents' => 'required|array',
            'passengers.*.documents.*.documentType' => 'required|string|in:PASSPORT',
            'passengers.*.documents.*.birthPlace' => 'required|string|max:100',
            'passengers.*.documents.*.issuanceLocation' => 'required|string|max:100',
            'passengers.*.documents.*.issuanceDate' => 'required|string|max:20',
            'passengers.*.documents.*.number' => 'required|string|max:20',
            'passengers.*.documents.*.expiryDate' => 'required|string|max:20',
            'passengers.*.documents.*.issuanceCountry' => 'required|string|max:10',
            'passengers.*.documents.*.validityCountry' => 'required|string|max:10',
            'passengers.*.documents.*.nationality' => 'required|string|max:10',
            'passengers.*.documents.*.holder' => 'required|boolean',
        ];
    }
}
