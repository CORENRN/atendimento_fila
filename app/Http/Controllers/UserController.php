<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            'categoria' => 'required|in:admin,superAdmin'
        ]);

        $user = User::findOrFail($id);
        $user->categoria = $request->input('categoria');
        $user->save();

        return redirect()->route('users.assignCategory')->with('success', 'Categoria atualizada com sucesso!');
    }
}
