<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->string('item_id', 12)->primary();      // CI + 10 digits = 12 chars
            $table->string('checklist_id', 12);            // MUST match checklists.checklist_id exactly
            $table->foreign('checklist_id')
                  ->references('checklist_id')
                  ->on('checklists')
                  ->onDelete('cascade');

            $table->string('text');
            $table->boolean('is_checked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};