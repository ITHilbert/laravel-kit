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
        Schema::connection('ai_sqlite')->table('ai_tasks', function (Blueprint $table) {
            $table->text('rueckfrage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('ai_sqlite')->table('ai_tasks', function (Blueprint $table) {
            $table->dropColumn('rueckfrage');
        });
    }
};
