<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS before_insert_card');
        DB::unprepared('DROP TRIGGER IF EXISTS before_update_card_order');
    }

    public function down(): void
    {
        // No-op: triggers were dropped intentionally
    }
};
