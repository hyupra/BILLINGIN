<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('google_id', 150)->nullable()->after('phone');
            $table->boolean('is_superadmin')->default(false)->after('google_id');
            $table->boolean('is_active')->default(true)->after('is_superadmin');
        });

        // SQL Server treats NULL as distinct for a plain unique index already,
        // but a filtered index documents the intent per spec §5.8 and keeps
        // the index small (most rows have no google_id).
        DB::statement('CREATE UNIQUE INDEX uq_users_google_id ON users (google_id) WHERE google_id IS NOT NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS uq_users_google_id ON users');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn(['phone', 'google_id', 'is_superadmin', 'is_active']);
        });
    }
};
