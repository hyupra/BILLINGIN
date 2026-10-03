<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('business_name', 150);
            $table->string('owner_name', 120)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('timezone', 40)->default('Asia/Jakarta');
            $table->decimal('ppn_percent', 5, 2)->default(0);
            $table->integer('rounding_step')->default(1);
            $table->integer('due_day_default')->default(10);
            $table->integer('grace_days')->default(3);
            $table->string('first_invoice_mode', 10)->default('full');
            $table->text('reminder_rules_json')->nullable();
            $table->string('status', 20)->default('trial');
            $table->dateTime('trial_ends_at')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE tenants ADD CONSTRAINT ck_tenants_status CHECK (status IN ('trial','active','suspended','archived'))");
        DB::statement("ALTER TABLE tenants ADD CONSTRAINT ck_tenants_first_invoice_mode CHECK (first_invoice_mode IN ('full','prorata'))");
        DB::statement("ALTER TABLE tenants ADD CONSTRAINT ck_tenants_reminder_rules_json CHECK (reminder_rules_json IS NULL OR ISJSON(reminder_rules_json) = 1)");
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
