<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class StoreCustomerRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'institution_name' => ['required', 'string', 'max:160'],
            'crm_number' => ['required', 'string', 'regex:/^[A-Za-z0-9]{1,13}$/'],
            'request_type' => ['required', Rule::in(['new_card', 'damaged_card', 'system_error_card', 'lost_card'])],
            'requested_quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'request_date' => ['required', 'date'],
            'application_file' => ['required', 'file', 'mimes:xlsx', 'extensions:xlsx', 'max:3072'],
            'payment_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
            'city' => ['prohibited'],
            'area' => ['prohibited'],
            'pic_name' => ['prohibited'],
            'phone' => ['prohibited'],
            'email' => ['prohibited'],
            'deadline' => ['prohibited'],
            'items' => ['prohibited'],
            'evidence' => ['prohibited'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach (['application_file' => ['PK'], 'payment_proof' => ['%PDF', "\xFF\xD8\xFF", "\x89PNG"]] as $field => $signatures) {
                $file = $this->file($field);
                if (! $file || ! is_readable($file->getRealPath())) {
                    continue;
                }
                $header = (string) file_get_contents($file->getRealPath(), false, null, 0, 8);
                if (! collect($signatures)->contains(fn (string $signature) => str_starts_with($header, $signature))) {
                    $validator->errors()->add($field, 'validation.invalid_file_signature');
                }
            }
        });
    }

    protected function prepareForValidation(): void {}
}
