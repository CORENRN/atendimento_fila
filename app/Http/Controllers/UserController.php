<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function showAssignCategory()
    {
        // Proteção: apenas superAdmin pode acessar
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acesso negado. Apenas Super Administradores podem gerenciar categorias.');
        }

        $allUsers = User::all();

        return view('users.assign_category', compact('allUsers'));
    }

    public function updateCategory(Request $request, $id)
    {   
         // Proteção: apenas superAdmin pode atualizar categorias
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acesso negado. Apenas Super Administradores podem alterar categorias.');
        }

        $request->validate([
            'categoria' => ['required', Rule::in(['supervisor', 'superAdmin', 'user', 'renovacao'])] 
        ]);
        
        $user = User::findOrFail($id);
        $user->categoria = $request->input('categoria');
        $user->save();
        $usuarioCat = '';
        switch($user->categoria) {
            case 'supervisor':
                $usuarioCat = 'Supervisor';
                break;
            case 'superAdmin':
                $usuarioCat = 'Super Administrador';
                break;
            case 'user':
                $usuarioCat = 'Atendente';
                break;
            case 'renovacao':
                $usuarioCat = 'Renovação';
                break;

        };

        return redirect()->route('adminPanel')->with('success', 'Categoria de ' . $user->name . ' atualizada para ' . $usuarioCat . ' com sucesso!');
    }
}