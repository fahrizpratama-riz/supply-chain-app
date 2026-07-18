<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');         // admin, analyst, user
            $table->string('description')->nullable();
            $table->json('permissions')->nullable(); // json array of permission keys
            $table->timestamps();
        });

        // Insert default roles
        \DB::table('user_roles')->insert([
            ['name' => 'admin',   'description' => 'Full system access',        'permissions' => json_encode(['all']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'analyst', 'description' => 'View and analyze data',     'permissions' => json_encode(['read', 'export']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'user',    'description' => 'Basic access to dashboard', 'permissions' => json_encode(['read']), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
