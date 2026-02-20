<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('login')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'freelancer', 'customer'])->default('customer');
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->timestamps();
            
            $table->index('role');
            $table->index('login');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
