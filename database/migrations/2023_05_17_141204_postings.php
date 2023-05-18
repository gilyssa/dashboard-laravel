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
        Schema::create('postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')->nullable(false)->constrained('enterprises');
            $table->foreignId('deliverer_id')->nullable(false)->constrained('deliverers');
            $table->foreignId('user_id')->nullable(false)->constrained('users');
            $table->boolean('removed')->nullable(false)->default(true);
            $table->integer('quantity')->nullable(false);
            $table->enum('type', ['carregamento', 'insucesso'])->nullable(false);
            $table->foreignId('updated_id')->constrained('users');
            $table->foreignId('removed_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postings');
    }
};
