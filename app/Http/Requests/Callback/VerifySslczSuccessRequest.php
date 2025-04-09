<?php

namespace App\Http\Requests\Callback;

use Illuminate\Foundation\Http\FormRequest;

class VerifySslczSuccessRequest extends FormRequest
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
            'tran_id' => 'required|string|exists:flight_bookings,booking_id',
            'val_id' => 'required|string',
            'currency_amount' => 'required|numeric',
            'currency_type' => 'required|string',
            'status' => 'required|string|in:VALID,FAILED,CANCELLED,UNATTEMPTED,EXPIRED',
        ];
    }
}
