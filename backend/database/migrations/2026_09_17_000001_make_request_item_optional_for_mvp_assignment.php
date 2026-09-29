<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rfid_assignments', function (Blueprint $table): void {
            $table->dropForeign(['request_item_id']);
            $table->dropUnique(['request_item_id']);
            $table->unsignedBigInteger('request_item_id')->nullable()->change();
            $table->foreign('request_item_id')->references('id')->on('request_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rfid_assignments', function (Blueprint $table): void {
            $table->dropForeign(['request_item_id']);
            $table->unsignedBigInteger('request_item_id')->nullable(false)->change();
            $table->foreign('request_item_id')->references('id')->on('request_items')->restrictOnDelete();
            $table->unique('request_item_id');
        });
    }
};
