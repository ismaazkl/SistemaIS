<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Ficha;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $team = $user->currentTeam;

        $email = strtolower($user->email);

        $pendingInvitations = TeamInvitation::query()
            ->with(['inviter', 'team'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn (TeamInvitation $invitation) => [
                'code' => $invitation->code,
                'inviterName' => $invitation->inviter->name,
                'team' => [
                    'name' => $invitation->team->name,
                    'slug' => $invitation->team->slug,
                ],
            ]);

        return Inertia::render('dashboard', [
            'pendingInvitations' => $pendingInvitations,
            'stats' => [
                'totalFichas' => Ficha::forTeam($team)->count(),
                'totalCursos' => Curso::forTeam($team)->count(),
                'fichasDelMes' => Ficha::forTeam($team)
                    ->whereYear('fecha', now()->year)
                    ->whereMonth('fecha', now()->month)
                    ->count(),
            ],
            'recentFichas' => Ficha::query()
                ->with('curso')
                ->forTeam($team)
                ->latestFirst()
                ->limit(8)
                ->get()
                ->map(fn (Ficha $ficha) => [
                    'id' => $ficha->id,
                    'estudiante' => $ficha->estudiante,
                    'curso' => $ficha->curso->curso,
                    'fecha' => $ficha->fecha->toDateString(),
                ]),
        ]);
    }
}
