<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_requests', function (Blueprint $table): void {
            $table->boolean('prioritized')->default(false)->index()->after('status');
            $table->string('prioritize_reason', 500)->nullable()->after('prioritized');
            $table->foreignId('prioritized_by')->nullable()->after('prioritize_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('prioritized_at')->nullable()->after('prioritized_by');
        });
    }

    public function down(): void
    {
        Schema::table('customer_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('prioritized_by');
            $table->dropColumn(['prioritized', 'prioritize_reason', 'prioritized_at']);
        });
    }
};
