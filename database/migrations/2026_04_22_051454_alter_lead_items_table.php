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
        DB::statement("ALTER TABLE `lead_items` DROP FOREIGN KEY `lead_items_item_id_foreign`");
        DB::statement("ALTER TABLE `lead_items` ADD CONSTRAINT `lead_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        DB::statement("ALTER TABLE `lead_items` DROP FOREIGN KEY `lead_items_lead_id_foreign`");
        DB::statement("ALTER TABLE `lead_items` ADD CONSTRAINT `lead_items_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
