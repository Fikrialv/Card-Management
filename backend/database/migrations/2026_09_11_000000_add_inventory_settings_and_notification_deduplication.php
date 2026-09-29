<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 100)->unique();
            $table->json('value');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('user_notifications', function (Blueprint $table): void {
            $table->string('dedupe_key', 160)->nullable()->unique()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table): void {
            $table->dropUnique(['dedupe_key']);
            $table->dropColumn('dedupe_key');
        });
        Schema::dropIfExists('system_settings');
    }
};
