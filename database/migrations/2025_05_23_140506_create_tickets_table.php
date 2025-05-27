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

            // Tipo de atendimento: regular ou preferencial
            $table->enum('type', ['regular', 'preferencial'])->default('regular');

            // Serviço solicitado
            $table->enum('service', [
                'financeiro',      // Questões financeiras, pagamentos, boletos
                'documentacao',    // Entrega ou retirada de documentos
                'informacoes',     // Suporte de informações gerais
                'cadastro',        // Atualização ou criação de cadastros
                'suporte'          // Suporte técnico ou específico
            ])->nullable();
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
