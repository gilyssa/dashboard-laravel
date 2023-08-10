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
        Schema::table('postings', function (Blueprint $table) {
            // Remover a coluna enterprise_id
            $table->dropForeign(['enterprise_id']);
            $table->dropColumn('enterprise_id');

            // Adicionar a coluna enterprise_price_range_id
            $table->foreignId('enterprise_price_range_id')->nullable(false)->constrained('enterprise_price_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postings', function (Blueprint $table) {
            $table->foreignId('enterprise_id')->nullable(false)->constrained('enterprises');
            $table->dropForeign(['enterprise_price_range_id']);
            $table->dropColumn('enterprise_price_range_id');
        });
    }
};
