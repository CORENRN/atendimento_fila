<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\PrinterM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    private function formatYoutubeUrl($url)
    {
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

        $lastAtendimentos = Ticket::where('status', 'finalizado')
            ->latest('finished_at')
            ->limit(3)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id, 
                    'finished_at' => $t->finished_at ? $t->finished_at->format('d/m/Y H:i:s') : '-',
                    'guiche' => $this->getGuicheName($t->attendant_id),
                ];
            });

        return view('panel.index', compact('videoUrl', 'lastAtendimentos'));
    }

    public function data()
    {
        // Triagem
        $triagem = Ticket::where('stage', 'triagem')
            ->where('status', 'triagem')
            ->whereNotNull('called_tri_at')
            ->whereNull('finished_at')
            // Ordena pelo último chamado ou rechamado
            ->orderByRaw('COALESCE(last_called_at, called_tri_at) DESC')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'called_at' => $t->called_tri_at->format('H:i:s'),
                // Passamos os campos brutos para o JS comparar a chave
                'last_called_at' => $t->last_called_at ? $t->last_called_at->toDateTimeString() : null,
            ]);
        // Carteira
        $carteira = Ticket::where('stage', 'carteira')
            ->where('status', 'carteira')
            ->whereNotNull('last_called_at')
            ->whereNull('finished_at')
            ->orderBy('last_called_at', 'DESC')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                // Usamos o format padrão primeiro para garantir que não quebre
                'called_at' => $t->last_called_at->format('H:i:s'),
                'guiche' => 'Retirada', 
                'last_called_at' => $t->last_called_at->toDateTimeString(),
            ]);
        // Atendimento
        $atendimento = Ticket::where('stage', 'atendimento')
            ->where('status', 'atendimento')
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->orderByRaw('COALESCE(last_called_at, called_at) DESC')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'called_at' => $t->called_at->format('H:i:s'),
                'guiche' => $this->getGuicheName($t->attendant_id),
                'last_called_at' => $t->last_called_at ? $t->last_called_at->toDateTimeString() : null,
                'updated_at' => $t->updated_at->toDateTimeString(),
            ]);

        $lastAtendimentos = Ticket::where('status', 'finalizado')
            ->latest('finished_at')
            ->limit(3)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'finished_at' => $t->finished_at ? $t->finished_at->format('d/m/Y H:i:s') : '-',
                'guiche' => $this->getGuicheName($t->attendant_id),
            ]);

        return response()->json([
            'carteira' => $carteira,
            'triagem' => $triagem,
            'atendimento' => $atendimento,
            'lastAtendimentos' => $lastAtendimentos,
        ]);
    }

    public function updateVideo(Request $request)
    {
        $request->validate([
            'video_url' => 'required|url'
        ]);

        $formattedUrl = $this->formatYoutubeUrl($request->video_url);

        if (!$formattedUrl) {
            return back()->with('error', 'URL do YouTube inválida.');
        }

        \App\Models\Setting::updateOrCreate(
            ['key' => 'video_url'],
            ['value' => $formattedUrl]
        );

        return back()->with('success', 'Vídeo atualizado com sucesso!');
    }


    public function adminPanel()
    {
        $videoUrl = \App\Models\Setting::get('video_url', 'https://www.youtube.com/embed/42s7LKG5GQE');
        
        // Todos os usuários para listar na tela
        $allUsers = User::all(); 

        return view('panel.admin', compact('videoUrl', 'allUsers'));
    }

    private function getGuicheName($userId)
    {
        if (!$userId) return null;
        $guiche = DB::table('user_guiche')
            ->join('guiches', 'user_guiche.guiche_id', '=', 'guiches.id')
            ->where('user_guiche.user_id', $userId)
            ->where('user_guiche.created_at', '>=', now()->subHours(12))
            ->select('guiches.name')
            ->orderByDesc('user_guiche.created_at')
            ->first();
        return $guiche?->name;
    }
}