<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('columns', function (Blueprint $table) {
            $table->unique(['year', 'month', 'user_id'], 'columns_year_month_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('columns', function (Blueprint $table) {
            $table->dropUnique('columns_year_month_user_unique');
        });
    }
};
