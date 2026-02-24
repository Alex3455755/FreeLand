<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('login')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->enum('role', ['admin', 'freelancer', 'customer'])->default('customer');
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
