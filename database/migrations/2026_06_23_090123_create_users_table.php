<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');          
            $table->string('full_name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 15)->nullable();
            $table->string('password_hash', 255);
            $table->string('address', 255)->nullable();
            $table->string('city', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};