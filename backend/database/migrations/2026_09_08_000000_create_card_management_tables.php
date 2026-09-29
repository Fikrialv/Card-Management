<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)->default('viewer')->index();
            $table->string('locale', 2)->default('id');
        });

        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('institution_name', 160);
            $table->string('city', 100);
            $table->string('crm_number', 64)->unique();
            $table->string('area', 100)->nullable();
            $table->string('pic_name', 160);
            $table->string('phone', 32);
            $table->string('email', 160);
            $table->timestamps();
        });

        Schema::create('customer_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('request_number', 32)->unique();
            $table->string('idempotency_key', 64)->nullable()->unique();
            $table->string('tracking_code_hash');
            $table->text('tracking_code_ciphertext')->nullable();
            $table->json('submitted_customer');
            $table->string('request_type', 32)->index();
            $table->string('status', 24)->default('NEW')->index();
            $table->date('deadline')->nullable()->index();
            $table->boolean('urgency')->default(false)->index();
            $table->string('calculated_priority', 16)->default('NORMAL');
            $table->string('override_priority', 16)->nullable()->index();
            $table->string('override_reason', 500)->nullable();
            $table->foreignId('override_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('override_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'deadline', 'created_at'], 'request_queue_idx');
        });

        Schema::create('request_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_request_id')->constrained()->cascadeOnDelete();
            $table->string('rfid_number', 32)->nullable()->index();
            $table->string('license_plate', 20)->nullable()->index();
            $table->string('user_name', 160)->nullable();
            $table->string('vehicle_type', 80)->nullable();
            $table->string('fuel_type', 40)->nullable();
            $table->decimal('quota_amount', 14, 2)->nullable();
            $table->string('quota_unit', 16)->nullable();
            $table->string('quota_period', 16)->nullable();
            $table->decimal('balance_transfer_amount', 14, 2)->nullable();
            $table->string('destination_code', 64)->nullable();
            $table->boolean('activation_requested')->default(false);
            $table->string('change_type', 80)->nullable();
            $table->timestamps();
        });

        Schema::create('request_evidence', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('request_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });

        Schema::create('request_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status', 24)->nullable();
            $table->string('to_status', 24);
            $table->string('reason', 500)->nullable();
            $table->boolean('customer_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('priority_overrides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users')->restrictOnDelete();
            $table->string('previous_priority', 16);
            $table->string('priority', 16);
            $table->string('reason', 500);
            $table->timestamp('removed_at')->nullable();
            $table->foreignId('removed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('rfid_ranges', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('start_number');
            $table->unsignedBigInteger('end_number');
            $table->unsignedInteger('quantity');
            $table->string('source', 160);
            $table->date('occurred_on');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('stock_request_id')->nullable();
            $table->timestamps();
            $table->unique(['start_number', 'end_number']);
        });

        Schema::create('rfid_cards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rfid_range_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('number')->unique();
            $table->string('status', 20)->default('available')->index();
            $table->timestamps();
        });

        Schema::create('stock_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('request_number', 32)->unique();
            $table->unsignedInteger('quantity');
            $table->string('reason', 500);
            $table->string('region', 100);
            $table->text('shipping_address');
            $table->string('receiver_pic', 160);
            $table->string('contact_person', 160);
            $table->text('notes')->nullable();
            $table->string('status', 24)->default('DRAFT')->index();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });

        Schema::table('rfid_ranges', function (Blueprint $table): void {
            $table->foreign('stock_request_id')->references('id')->on('stock_requests')->nullOnDelete();
        });

        Schema::create('stock_request_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('stock_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status', 24)->nullable();
            $table->string('to_status', 24);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 3)->index();
            $table->unsignedInteger('quantity');
            $table->foreignId('rfid_range_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('request_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stock_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description', 255)->nullable();
            $table->string('crm_number', 64)->nullable();
            $table->date('occurred_on');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('rfid_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rfid_card_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('customer_request_id')->constrained()->restrictOnDelete();
            $table->foreignId('request_item_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamps();
        });

        Schema::create('document_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 160);
            $table->unsignedInteger('version');
            $table->string('locale', 2)->default('id');
            $table->string('status', 20)->default('draft');
            $table->json('schema');
            $table->text('content');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['name', 'version', 'locale']);
        });

        Schema::create('generated_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_template_id')->constrained()->restrictOnDelete();
            $table->string('document_number', 64)->nullable()->unique();
            $table->string('locale', 2);
            $table->json('input');
            $table->string('path');
            $table->char('sha256', 64);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('user_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 80);
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100)->index();
            $table->string('auditable_type', 100);
            $table->unsignedBigInteger('auditable_id');
            $table->json('metadata')->nullable();
            $table->uuid('correlation_id')->index();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('generated_documents');
        Schema::dropIfExists('document_templates');
        Schema::dropIfExists('rfid_assignments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_request_status_histories');
        Schema::table('rfid_ranges', fn (Blueprint $table) => $table->dropForeign(['stock_request_id']));
        Schema::dropIfExists('stock_requests');
        Schema::dropIfExists('rfid_cards');
        Schema::dropIfExists('rfid_ranges');
        Schema::dropIfExists('priority_overrides');
        Schema::dropIfExists('request_status_histories');
        Schema::dropIfExists('request_evidence');
        Schema::dropIfExists('request_items');
        Schema::dropIfExists('customer_requests');
        Schema::dropIfExists('customers');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'locale']);
        });
    }
};
