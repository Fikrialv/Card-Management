<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreProcurementNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'request_date' => ['required', 'date'],
            'pic_name' => ['required', 'string', 'max:160'],
            'requested_quantity' => ['required', 'integer', 'min:1', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
