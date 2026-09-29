<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('customer_quota_usages');
        Schema::dropIfExists('customer_quotas');
    }

    public function down(): void
    {
        // Legacy quota tables are intentionally not restored.
    }
};
