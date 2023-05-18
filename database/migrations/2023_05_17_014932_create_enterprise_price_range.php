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
        Schema::create('enterprise_price_range', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')->nullable(false)->constrained('enterprises');
            $table->foreignId('price_band_id')->nullable(false)->constrained('price_bands');
            $table->foreignId('city_id')->nullable(false)->constrained('cities');
            $table->boolean('status')->nullable(false)->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprise_price_range');
    }
};
