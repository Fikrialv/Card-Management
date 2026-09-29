<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn(['city', 'area', 'pic_name', 'phone', 'email']);
        });

        Schema::table('customer_requests', function (Blueprint $table): void {
            $table->date('request_date')->nullable()->after('request_type')->index();
        });

        Schema::table('request_evidence', function (Blueprint $table): void {
            $table->string('artifact_type', 32)->default('legacy')->after('request_item_id')->index();
        });

        Schema::create('procurement_notes', function (Blueprint $table): void {
            $table->id();
            $table->string('reference_number', 40)->unique();
            $table->date('request_date')->index();
            $table->string('pic_name', 160);
            $table->unsignedInteger('requested_quantity');
            $table->text('notes')->nullable();
            $table->date('received_on')->nullable()->index();
            $table->unsignedInteger('received_quantity')->nullable();
            $table->string('status', 24)->default('DRAFT')->index();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('rfid_ranges', function (Blueprint $table): void {
            $table->foreignId('procurement_note_id')->nullable()->after('stock_request_id')->constrained()->nullOnDelete();
        });

        Schema::table('stock_movements', function (Blueprint $table): void {
            $table->foreignId('procurement_note_id')->nullable()->after('stock_request_id')->constrained()->nullOnDelete();
        });

        Schema::create('customer_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_request_id')->constrained()->cascadeOnDelete();
            $table->string('type', 80);
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_notifications');
        Schema::table('stock_movements', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('procurement_note_id');
        });
        Schema::table('rfid_ranges', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('procurement_note_id');
        });
        Schema::dropIfExists('procurement_notes');
        Schema::table('request_evidence', function (Blueprint $table): void {
            $table->dropColumn('artifact_type');
        });
        Schema::table('customer_requests', function (Blueprint $table): void {
            $table->dropColumn('request_date');
        });
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('city', 100)->nullable();
            $table->string('area', 100)->nullable();
            $table->string('pic_name', 160)->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('email', 160)->nullable();
        });
    }
};
