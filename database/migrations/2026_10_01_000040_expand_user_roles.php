<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN — add a new column, copy, drop old
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_new', 20)->default('user')->after('role');
            });
            DB::table('users')->update([
                'role_new' => DB::raw('role'),
            ]);
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_new', 'role');
            });
        }
        // MySQL: just a comment/no-op — string column already accepts new values
        // Allowed values: admin | guruji | vendor | user
    }

    public function down(): void
    {
        // Intentional no-op: downgrading role values is a data operation, not schema.
    }
};
