<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ReceiveProcurementNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'received_on' => ['required', 'date'],
            'received_quantity' => ['required', 'integer', 'min:1', 'max:5000'],
        ];
    }
}
