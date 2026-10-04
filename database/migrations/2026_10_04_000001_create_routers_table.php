<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('name', 100);
            $table->string('network_driver', 30)->default('manual');
            $table->string('host', 150);
            $table->unsignedInteger('api_port')->default(8728);
            $table->text('api_username_enc')->nullable();
            $table->text('api_password_enc')->nullable();
            $table->string('status', 20)->default('unknown');
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE routers ADD CONSTRAINT ck_routers_driver CHECK (network_driver IN ('manual','mikrotik_pppoe'))");
        DB::statement("ALTER TABLE routers ADD CONSTRAINT ck_routers_status CHECK (status IN ('unknown','online','offline'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
