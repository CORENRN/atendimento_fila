<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['Regular', 'Preferencial'])->default('regular');
            $table->enum('service', [
                'financeiro',
                'documentacao',
                'informacoes',
                'cadastro',
                'suporte'
            ])->nullable();
            $table->enum('stage', ['triagem', 'atendimento'])->default('triagem');
            $table->enum('status', ['aguardando', 'triagem', 'atendimento', 'finalizado', 'cancelado'])->default('aguardando');

            $table->timestamp('called_at')->nullable();
            $table->timestamp('called_tri_at')->nullable();       // ⬅️ adicionado
            $table->timestamp('last_called_at')->nullable();      // ⬅️ adicionado
            $table->timestamp('finished_at')->nullable();

            $table->foreignId('attendant_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
