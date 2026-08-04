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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            [
                'code' => Role::ADMIN,
                'name' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => Role::CUSTOMER,
                'name' => 'Customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
