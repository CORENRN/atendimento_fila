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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document')->nullable();
            $table->enum('stage', ['triagem', 'atendimento'])->default('triagem');
            $table->enum('status', ['aguardando', 'triagem', 'atendimento', 'finalizado', 'cancelado'])->default('aguardando');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('attendant_id')->nullable()->constrained('attendants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
