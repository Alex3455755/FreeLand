<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clothes', function (Blueprint $table) {
            $table->id();
            $table->string('title',50);
            $table->string('size',5);
            $table->float('price',15,2);
            $table->foreignId('brand_id')->constrained()
                    ->onDelete('cascade');
            $table->string('color',50);
            $table->timestamps();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clouthe');
    }
};
