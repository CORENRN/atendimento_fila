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
        Schema::table('tickets', function(Blueprint $table){
            $table->enum('type', ['Regular', 'Preferencial', 'Carteira'])->default('regular')->change();  
        });
         
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
