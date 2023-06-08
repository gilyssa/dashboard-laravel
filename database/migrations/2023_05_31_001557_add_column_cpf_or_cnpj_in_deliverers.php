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
        Schema::table('deliverers', function (Blueprint $table) {
            if (!Schema::hasColumn('deliverers', 'cpf_or_cnpj')) {
                $table->string('cpf_or_cnpj')->after('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliverers', function (Blueprint $table) {
            //
        });
    }
};
