<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class CursoSeeder extends Seeder
{
    /**
     * Create a new seeder instance.
     */
    public function __construct(protected ?Team $team = null) {}

    /**
     * Seed the catalogue of courses for the given team.
     */
    public function run(): void
    {
        $team = $this->team ??= User::factory()->create()->currentTeam;

        if ($team === null) {
            return;
        }
        $cursos = [
            'INICIAL 1 A',
            'INICIAL 2 A',
            'INICIAL 2 B',

            '1ERO EGB A',
            '1ERO EGB B',

            '2DO EGB A',
            '2DO EGB B',

            '3ERO EGB A',
            '3ERO EGB B',

            '4TO EGB A',
            '4TO EGB B',

            '5TO EGB A',
            '5TO EGB B',

            '6TO EGB A',
            '6TO EGB B',

            '7MO EGB A',
            '7MO EGB B',

            '1ERO INSTALACIONES ELÉCTRICAS Y AUTOMATIZACIÓN A',
            '1ERO INSTALACIONES ELÉCTRICAS Y AUTOMATIZACIÓN B',
            '1ERO INSTALACIONES ELÉCTRICAS Y AUTOMATIZACIÓN C',

            '1ERO ELECTROMECANICA AUTOMOTRIZ A',
            '1ERO ELECTROMECANICA AUTOMOTRIZ B',
            '1ERO ELECTROMECANICA AUTOMOTRIZ C',

            '1ERO ELECTRONICA A',

            '1ERO DESARROLLO DE SOFTWARE A',

            '2DO INSTALACIONES EQUIPOS Y MAQUINAS ELECTRICAS A',
            '2DO INSTALACIONES EQUIPOS Y MAQUINAS ELECTRICAS B',
            '2DO INSTALACIONES EQUIPOS Y MAQUINAS ELECTRICAS C',

            '2DO ELECTROMECANICA AUTOMOTRIZ A',
            '2DO ELECTROMECANICA AUTOMOTRIZ B',
            '2DO ELECTROMECANICA AUTOMOTRIZ C',

            '2DO ELECTRONICA DE CONSUMO A',

            '2DO INFORMATICA A',

            '3ERO INSTALACIONES EQUIPOS Y MAQUINAS ELECTRICAS A',
            '3ERO INSTALACIONES EQUIPOS Y MAQUINAS ELECTRICAS B',
            '3ERO INSTALACIONES EQUIPOS Y MAQUINAS ELECTRICAS C',

            '3ERO ELECTROMECANICA AUTOMOTRIZ A',
            '3ERO ELECTROMECANICA AUTOMOTRIZ B',
            '3ERO ELECTROMECANICA AUTOMOTRIZ C',

            '3ERO ELECTRONICA DE CONSUMO A',

            '3ERO INFORMATICA A',
        ];

        foreach ($cursos as $curso) {
            Curso::firstOrCreate(
                ['curso' => $curso, 'team_id' => $team->id],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }
    }
}
