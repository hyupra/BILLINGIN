<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('customer_code', 20);
            $table->string('name', 150);
            $table->string('phone', 20);
            $table->text('id_number_enc')->nullable();
            $table->string('address', 255)->nullable();
            $table->foreignId('package_id')->constrained('packages');
            $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete();
            $table->string('ppp_username', 80)->nullable();
            $table->text('ppp_password_enc')->nullable();
            $table->string('billing_type', 10)->default('postpaid');
            $table->unsignedTinyInteger('due_day')->nullable();
            $table->bigInteger('extra_amount')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->string('status', 12)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE customers ADD CONSTRAINT ck_customers_billing_type CHECK (billing_type IN ('postpaid','prepaid'))");
        DB::statement("ALTER TABLE customers ADD CONSTRAINT ck_customers_status CHECK (status IN ('active','suspended','stopped'))");

        DB::statement('CREATE UNIQUE INDEX uq_customers_code ON customers (tenant_id, customer_code) WHERE deleted_at IS NULL');
        DB::statement('CREATE UNIQUE INDEX uq_customers_ppp ON customers (tenant_id, router_id, ppp_username) WHERE deleted_at IS NULL AND ppp_username IS NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
