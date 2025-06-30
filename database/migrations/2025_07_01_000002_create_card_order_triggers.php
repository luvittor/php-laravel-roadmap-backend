<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TRIGGER before_insert_card
BEFORE INSERT ON cards
FOR EACH ROW
BEGIN
    IF NEW.`order` IS NULL THEN
        SET NEW.`order` = COALESCE((SELECT MAX(`order`) FROM cards WHERE column_id = NEW.column_id), 0) + 1;
    ELSE
        UPDATE cards
        SET `order` = `order` + 1
        WHERE column_id = NEW.column_id
          AND `order` >= NEW.`order`;
    END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER before_update_card_order
BEFORE UPDATE ON cards
FOR EACH ROW
BEGIN
    IF NEW.`order` <> OLD.`order` THEN
        IF NEW.`order` > OLD.`order` THEN
            UPDATE cards
            SET `order` = `order` - 1
            WHERE column_id = NEW.column_id
              AND `order` <= NEW.`order`
              AND `order` > OLD.`order`
              AND id <> OLD.id;
        ELSE
            UPDATE cards
            SET `order` = `order` + 1
            WHERE column_id = NEW.column_id
              AND `order` >= NEW.`order`
              AND `order` < OLD.`order`
              AND id <> OLD.id;
        END IF;
    END IF;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS before_insert_card');
        DB::unprepared('DROP TRIGGER IF EXISTS before_update_card_order');
    }
};
