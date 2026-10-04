<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete();
            $table->string('name', 100);
            $table->string('speed_label', 40);
            $table->bigInteger('base_price');
            $table->decimal('ppn_percent', 5, 2)->default(11.00);
            $table->string('default_profile', 80)->nullable();
            $table->boolean('allow_online_registration')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE packages ADD CONSTRAINT ck_packages_base_price CHECK (base_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
