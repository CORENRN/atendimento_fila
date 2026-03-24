<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy($id)
    {
        // Check if user is superAdmin
        if (auth()->user()->categoria !== 'superAdmin') {
            return back()->with('error', 'Ação não autorizada.');
        }

        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir sua própria conta.');
        }

        try {
            DB::beginTransaction();

            // 1. Delete relations in user_guiche
            DB::table('user_guiche')->where('user_id', $user->id)->delete();

            // 2. Delete linked tickets
            DB::table('tickets')->where('attendant_id', $user->id)->delete();

            // 3. Delete user
            $user->delete();

            DB::commit();

            return back()->with('success', 'Usuário e seus vínculos foram excluídos com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir usuário: ' . $e->getMessage());
            return back()->with('error', 'Erro ao excluir usuário: ' . $e->getMessage());
        }
    }
}
