<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignRfidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageInventory() ?? false;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ['rfid_number' => ['required', 'regex:/^\d{1,18}$/']];
    }
}
