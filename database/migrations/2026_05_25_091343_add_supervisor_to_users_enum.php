<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adicionando 'supervisor' à lista que o banco aceita
            $table->enum('categoria', ['superAdmin', 'admin', 'user', 'renovacao', 'supervisor'])
                ->default('user')
                ->change();
        });
    }
};
