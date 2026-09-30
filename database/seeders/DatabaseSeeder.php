<?php

namespace Database\Seeders;

use App\Models\Ficha;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $team = $user->currentTeam;

        (new CursoSeeder($team))->run();

        $cursos = $team->cursos()
            ->ordered()
            ->limit(12)
            ->get();

        foreach ($cursos as $curso) {
            Ficha::factory()
                ->for($team)
                ->for($curso, 'curso')
                ->create();
        }
    }
}
