<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_enrichments', function (Blueprint $table) {
            $table->string('display_name', 255)->nullable()->index()->after('product_id');
            $table->string('seo_title', 191)->nullable()->after('description');
            $table->string('seo_description', 320)->nullable()->after('seo_title');
        });

        Schema::table('category_tax_rules', function (Blueprint $table) {
            $table->string('calculation_basis', 48)->default('TRIGGERED_SUBTOTAL_DIV_1_11')->after('rate_percent');
            $table->timestamp('effective_from')->nullable()->after('is_active');
            $table->timestamp('effective_until')->nullable()->after('effective_from');
            $table->foreignId('updated_by')->nullable()->after('effective_until');
            $table->foreign('updated_by', 'fk_tax_rules_updated_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->string('reseller_account_number', 64)->nullable()->unique()->after('business_name');
        });

        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->index();
            $table->foreign('product_id', 'fk_product_media_product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->string('media_type', 16);
            $table->string('object_key', 500)->unique();
            $table->string('alt_text', 255)->nullable();
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('store_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('store_name', 191);
            $table->text('address');
            $table->string('contact_number', 32);
            $table->string('company_name', 191)->nullable();
            $table->string('company_npwp', 32);
            $table->unsignedInteger('partai_minimum_quantity')->default(5);
            $table->string('origin_biteship_area_id', 191)->nullable()->index();
            $table->string('origin_postal_code', 16)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name', 100);
            $table->string('account_number', 64)->unique();
            $table->string('account_holder', 191);
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('idempotency_key', 191)->nullable()->unique()->after('order_number');
            $table->date('order_date_local')->nullable()->index()->after('status');
            $table->string('cancellation_source', 16)->nullable()->index()->after('expires_at');
            $table->foreignId('cancelled_by_user_id')->nullable()->after('cancellation_source');
            $table->foreign('cancelled_by_user_id', 'fk_orders_cancelled_by')->references('id')->on('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable()->after('cancelled_by_user_id');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->string('completion_source', 24)->nullable()->index()->after('cancelled_at');
            $table->timestamp('receipt_confirmed_at')->nullable()->after('completion_source');
            $table->string('receipt_token_hash', 255)->nullable()->unique()->after('receipt_confirmed_at');
            $table->timestamp('receipt_token_expires_at')->nullable()->index()->after('receipt_token_hash');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id_snapshot')->nullable()->index()->after('product_id');
            $table->string('category_name_snapshot', 191)->nullable()->after('category_id_snapshot');
            $table->string('price_type', 16)->nullable()->after('sku');
        });

        Schema::create('order_charge_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index();
            $table->foreign('order_id', 'fk_charge_components_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('category_tax_rule_id')->nullable()->index();
            $table->foreign('category_tax_rule_id', 'fk_charge_components_tax_rule')->references('id')->on('category_tax_rules')->nullOnDelete();
            $table->string('component_code', 32)->index();
            $table->string('label_snapshot', 100);
            $table->decimal('basis_amount', 19, 2);
            $table->decimal('divisor', 8, 4)->default(1.1100);
            $table->decimal('rate_percent', 8, 4);
            $table->decimal('amount', 19, 2);
            $table->json('config_snapshot');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('store_profile_id')->nullable()->after('order_id');
            $table->foreign('store_profile_id', 'fk_invoices_store_profile')->references('id')->on('store_profiles')->nullOnDelete();
            $table->string('company_name_snapshot', 191)->nullable()->after('store_npwp');
            $table->string('reseller_account_number_snapshot', 64)->nullable()->after('company_name_snapshot');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique();
            $table->foreign('order_id', 'fk_payments_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('bank_account_id');
            $table->foreign('bank_account_id', 'fk_payments_bank_account')->references('id')->on('bank_accounts');
            $table->string('status', 24)->default('NOT_SUBMITTED')->index();
            $table->decimal('amount', 19, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('verified_by')->nullable();
            $table->foreign('verified_by', 'fk_payments_verified_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->index()->after('id');
            $table->foreign('payment_id', 'fk_proofs_payment_id')->references('id')->on('payments')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('status');
            $table->string('checksum', 128)->nullable()->index()->after('proof_path');
            $table->timestamp('retain_until')->nullable()->index()->after('checksum');
        });

        Schema::create('store_courier_rates', function (Blueprint $table) {
            $table->id();
            $table->string('area_code', 32)->nullable()->index();
            $table->string('area_name', 191)->index();
            $table->decimal('rate_amount', 19, 2);
            $table->string('eta_text', 100)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique();
            $table->foreign('order_id', 'fk_shipments_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('store_courier_rate_id')->nullable();
            $table->foreign('store_courier_rate_id', 'fk_shipments_store_rate')->references('id')->on('store_courier_rates')->nullOnDelete();
            $table->string('rate_provider', 32)->index();
            $table->string('courier_code', 100)->nullable();
            $table->string('courier_name_snapshot', 191)->nullable();
            $table->string('service_code', 100)->nullable();
            $table->string('service_name_snapshot', 191);
            $table->string('eta_snapshot', 100)->nullable();
            $table->string('origin_biteship_area_id_snapshot', 191)->nullable();
            $table->string('destination_biteship_area_id_snapshot', 191)->nullable();
            $table->string('recipient_name_snapshot', 150);
            $table->string('recipient_phone_snapshot', 32);
            $table->text('address_snapshot');
            $table->string('province_snapshot', 100);
            $table->string('city_snapshot', 100);
            $table->string('district_snapshot', 100)->index();
            $table->string('postal_code_snapshot', 16)->nullable();
            $table->char('currency', 3)->default('IDR');
            $table->decimal('shipping_amount', 19, 2);
            $table->string('rate_request_hash', 128)->nullable()->index();
            $table->timestamp('quoted_at')->nullable();
            $table->string('tracking_number', 191)->nullable()->index();
            $table->string('shipment_group_code', 64)->nullable()->index();
            $table->string('status', 32)->default('PROCESSING')->index();
            $table->string('issue_status', 24)->default('NONE')->index();
            $table->text('issue_reason')->nullable();
            $table->timestamp('issue_reported_at')->nullable();
            $table->timestamp('issue_resolved_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sync_runs', function (Blueprint $table) {
            $table->id();
            $table->string('sync_type', 32)->index();
            $table->string('source', 16);
            $table->string('status', 24)->index();
            $table->timestamp('started_at')->index();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->string('correlation_id', 191)->nullable()->unique();
            $table->json('summary')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('sync_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sync_run_id')->index();
            $table->foreign('sync_run_id', 'fk_sync_errors_run')->references('id')->on('sync_runs')->cascadeOnDelete();
            $table->string('entity_type', 64)->index();
            $table->string('external_id', 191)->nullable()->index();
            $table->string('error_code', 100)->index();
            $table->text('error_message');
            $table->json('payload_excerpt_redacted')->nullable();
            $table->boolean('retryable')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique();
            $table->foreign('order_id', 'fk_sales_returns_order')->references('id')->on('orders')->cascadeOnDelete();
            $table->string('return_number', 64)->unique();
            $table->text('reason');
            $table->string('reporting_status', 24)->index();
            $table->string('pos_ack_reference', 191)->nullable()->index();
            $table->timestamp('returned_at')->index();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_integration_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->index();
            $table->foreign('order_id', 'fk_pos_ops_order')->references('id')->on('orders')->nullOnDelete();
            $table->foreignId('sync_run_id')->nullable()->index();
            $table->foreign('sync_run_id', 'fk_pos_ops_sync_run')->references('id')->on('sync_runs')->nullOnDelete();
            $table->string('operation', 64)->index();
            $table->string('external_reference', 191)->unique();
            $table->string('request_hash', 128)->index();
            $table->string('status', 32)->index();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->string('correlation_id', 191)->nullable()->index();
            $table->string('response_reference', 191)->nullable()->index();
            $table->json('request_payload_redacted')->nullable();
            $table->json('response_payload_redacted')->nullable();
            $table->string('error_code', 100)->nullable()->index();
            $table->text('error_message')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
        });

        Schema::table('inventory_ledger', function (Blueprint $table) {
            $table->foreignId('sync_run_id')->nullable()->index()->after('product_id');
            $table->foreign('sync_run_id', 'fk_ledger_sync_run')->references('id')->on('sync_runs')->nullOnDelete();
            $table->foreignId('sales_return_id')->nullable()->index()->after('sync_run_id');
            $table->foreign('sales_return_id', 'fk_ledger_sales_return')->references('id')->on('sales_returns')->nullOnDelete();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreign('user_id', 'fk_notifications_user')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('order_id')->index();
            $table->foreign('order_id', 'fk_notifications_order')->references('id')->on('orders')->cascadeOnDelete();
            $table->string('channel', 16)->index();
            $table->string('type', 64)->index();
            $table->json('data');
            $table->string('status', 24)->index();
            $table->string('external_message_id', 191)->nullable()->index();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->index();
            $table->foreign('actor_user_id', 'fk_audit_actor')->references('id')->on('users')->nullOnDelete();
            $table->string('action', 100)->index();
            $table->string('auditable_type', 191);
            $table->unsignedBigInteger('auditable_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('request_id', 191)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');

        Schema::table('inventory_ledger', function (Blueprint $table) {
            $table->dropForeign('fk_ledger_sales_return');
            $table->dropForeign('fk_ledger_sync_run');
            $table->dropColumn(['sync_run_id', 'sales_return_id']);
        });

        Schema::dropIfExists('pos_integration_operations');
        Schema::dropIfExists('sales_returns');
        Schema::dropIfExists('sync_errors');
        Schema::dropIfExists('sync_runs');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('store_courier_rates');

        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->dropForeign('fk_proofs_payment_id');
            $table->dropColumn(['payment_id', 'is_active', 'checksum', 'retain_until']);
        });

        Schema::dropIfExists('payments');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('fk_invoices_store_profile');
            $table->dropColumn(['store_profile_id', 'company_name_snapshot', 'reseller_account_number_snapshot']);
        });

        Schema::dropIfExists('order_charge_components');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['category_id_snapshot', 'category_name_snapshot', 'price_type']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('fk_orders_cancelled_by');
            $table->dropColumn([
                'idempotency_key',
                'order_date_local',
                'cancellation_source',
                'cancelled_by_user_id',
                'cancellation_reason',
                'cancelled_at',
                'completion_source',
                'receipt_confirmed_at',
                'receipt_token_hash',
                'receipt_token_expires_at',
            ]);
        });

        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('store_profiles');
        Schema::dropIfExists('product_media');

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropColumn('reseller_account_number');
        });

        Schema::table('category_tax_rules', function (Blueprint $table) {
            $table->dropForeign('fk_tax_rules_updated_by');
            $table->dropColumn(['calculation_basis', 'effective_from', 'effective_until', 'updated_by']);
        });

        Schema::table('product_enrichments', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'seo_title', 'seo_description']);
        });
    }
};
