<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function showAssignCategory()
    {
        $users = User::all();

        return view('users.assign_category', compact('users'));
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'categoria' => ['required', Rule::in(['admin', 'superAdmin', 'user'])] 
        ]);
        
        $user = User::findOrFail($id);
        $user->categoria = $request->input('categoria');
        $user->save();

        return redirect()->route('adminPanel')->with('success', 'Categoria de ' . $user->name . ' atualizada para ' . $user->categoria . ' com sucesso!');
    }
}