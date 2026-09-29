<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class TrackCustomerRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ['request_number' => ['required', 'string', 'max:32'], 'tracking_code' => ['required', 'string', 'max:128']];
    }
}
