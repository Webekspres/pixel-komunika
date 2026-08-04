<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles');
            $table->string('phone', 32)->nullable()->unique()->after('email');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });

        $customerRoleId = DB::table('roles')
            ->where('code', Role::CUSTOMER)
            ->value('id');

        DB::table('users')
            ->whereNull('role_id')
            ->update(['role_id' => $customerRoleId]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropUnique('users_phone_unique');
            $table->dropColumn(['phone', 'last_login_at']);
        });
    }
};
