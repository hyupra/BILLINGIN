<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('action', 60);
            $table->string('subject_type', 150)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('before_json')->nullable();
            $table->text('after_json')->nullable();
            $table->string('ip', 45)->nullable();
            $table->dateTime('created_at');
        });

        DB::statement("ALTER TABLE audit_logs ADD CONSTRAINT ck_audit_logs_before_json CHECK (before_json IS NULL OR ISJSON(before_json) = 1)");
        DB::statement("ALTER TABLE audit_logs ADD CONSTRAINT ck_audit_logs_after_json CHECK (after_json IS NULL OR ISJSON(after_json) = 1)");
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
