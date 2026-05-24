<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'telegram')) {
                $table->string('telegram')->nullable()->after('avatar');
            }
            if (! Schema::hasColumn('users', 'github')) {
                $table->string('github')->nullable()->after('telegram');
            }
            if (! Schema::hasColumn('users', 'portfolio_url')) {
                $table->string('portfolio_url')->nullable()->after('github');
            }
            if (! Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('portfolio_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['telegram', 'github', 'portfolio_url', 'website'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
