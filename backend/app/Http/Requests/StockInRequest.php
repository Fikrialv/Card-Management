<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageInventory() ?? false;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'start_number' => ['required', 'regex:/^\d{1,18}$/'],
            'end_number' => ['required', 'regex:/^\d{1,18}$/'],
            'source' => ['required', 'string', 'max:160'],
            'occurred_on' => ['required', 'date'],
        ];
    }
}
