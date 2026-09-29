<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('normalized_institution_name', 160)->nullable()->after('institution_name')->index();
        });

        Schema::table('customer_requests', function (Blueprint $table): void {
            $table->unsignedInteger('requested_quantity')->default(1)->after('request_type');
        });

    }

    public function down(): void
    {
        Schema::table('customer_requests', fn (Blueprint $table) => $table->dropColumn('requested_quantity'));
        Schema::table('customers', fn (Blueprint $table) => $table->dropColumn('normalized_institution_name'));
    }
};
