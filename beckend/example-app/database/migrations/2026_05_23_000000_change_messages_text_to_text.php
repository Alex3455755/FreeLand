<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Зашифрованный текст длиннее исходного, VARCHAR(255) не вмещает.
        DB::statement('ALTER TABLE messages MODIFY text TEXT NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE messages MODIFY text VARCHAR(255) NOT NULL');
    }
};
