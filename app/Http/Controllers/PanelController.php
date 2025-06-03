<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{

    private function formatYoutubeUrl($url)
    {
        // Extrai o ID do vídeo, suportando links comuns e links encurtados
        preg_match(
            '%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
            $url,
            $matches
        );
        return isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : null;
    }

    public function index()
    {
        $videoUrl = \App\Models\Setting::get('video_url', 'https://www.youtube.com/embed/42s7LKG5GQE');
        return view('panel.index', compact('videoUrl'));
    }

    public function updateVideo(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->categoria !== 'superAdmin') {
            abort(403, 'Acesso permitido apenas para super administradores');
        }

        $request->validate([
            'video_url' => 'required|url',
        ]);

        \App\Models\Setting::set('video_url', $request->input('video_url'));

        return redirect()->back()->with('success', 'Vídeo atualizado com sucesso.');
    }

    public function data()
    {
        $triagem = Ticket::where('stage', 'triagem')
            ->where('status', 'triagem')
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->latest('called_at')
            ->get()
            ->map(fn($t) => [
                'id' => sprintf('%04d', $t->id),
                'called_at' => $t->called_at->format('H:i:s'),
                'guiche' => $this->getGuicheName($t->attendant_id),
            ]);

        $atendimento = Ticket::where('stage', 'atendimento')
            ->where('status', 'atendimento')
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->latest('called_at')
            ->get()
            ->map(fn($t) => [
                'id' => sprintf('%04d', $t->id),
                'called_at' => $t->called_at->format('H:i:s'),
                'guiche' => $this->getGuicheName($t->attendant_id),
            ]);

        return response()->json([
            'triagem' => $triagem,
            'atendimento' => $atendimento,
        ]);
    }

    private function getGuicheName($userId)
    {
        if (!$userId) return null;

        $guiche = DB::table('user_guiche')
            ->join('guiches', 'user_guiche.guiche_id', '=', 'guiches.id')  // corrigido aqui
            ->where('user_guiche.user_id', $userId)
            ->where('user_guiche.created_at', '>=', now()->subHours(12))
            ->select('guiches.name')
            ->orderByDesc('user_guiche.created_at')
            ->first();

        return $guiche?->name;
    }
}
