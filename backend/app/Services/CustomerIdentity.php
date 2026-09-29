<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Str;

final class CustomerIdentity
{
    public function normalizeName(string $name): string
    {
        $normalized = Str::upper(trim($name));
        $normalized = preg_replace('/[^A-Z0-9]+/u', ' ', $normalized) ?: '';

        return trim((string) preg_replace('/\s+/', ' ', $normalized));
    }

    public function hasDuplicateName(string $name, ?string $crmNumber = null): bool
    {
        $normalized = $this->normalizeName($name);

        return Customer::query()
            ->where('normalized_institution_name', $normalized)
            ->when($crmNumber, fn ($query) => $query->where('crm_number', '!=', Str::upper(trim($crmNumber))))
            ->exists();
    }
}
