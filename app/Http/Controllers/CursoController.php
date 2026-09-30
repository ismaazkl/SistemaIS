<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCursoRequest;
use App\Models\Curso;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CursoController extends Controller
{
    /**
     * Display a listing of the courses of the current team.
     */
    public function index(Request $request): Response
    {
        $team = $this->team($request);
        $search = $request->string('search')->trim()->value();

        $cursos = Curso::query()
            ->withCount('fichas')
            ->forTeam($team)
            ->search($search)
            ->ordered()
            ->get()
            ->map(fn (Curso $curso): array => [
                'id' => $curso->id,
                'curso' => $curso->curso,
                'fichasCount' => $curso->fichas_count,
            ]);

        return Inertia::render('Cursos/Index', [
            'cursos' => $cursos,
            'filters' => ['search' => $search],
            'canManage' => Gate::allows('create', Curso::class),
        ]);
    }

    /**
     * Store a newly created course.
     */
    public function store(SaveCursoRequest $request): RedirectResponse
    {
        Gate::authorize('create', Curso::class);

        $curso = $this->team($request)->cursos()->create($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Curso \"{$curso->curso}\" creado.",
        ]);

        return back();
    }

    /**
     * Update the given course.
     */
    public function update(SaveCursoRequest $request): RedirectResponse
    {
        $model = $this->findCurso($request);

        Gate::authorize('update', $model);

        $model->update($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Curso actualizado.',
        ]);

        return back();
    }

    /**
     * Remove the given course.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $model = $this->findCurso($request);

        Gate::authorize('delete', $model);

        if ($model->fichas()->exists()) {
            return back()->withErrors([
                'curso' => 'No puedes eliminar un curso que tiene fichas registradas.',
            ]);
        }

        $model->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Curso eliminado.',
        ]);

        return back();
    }

    /**
     * Resolve the current team from the request.
     */
    protected function team(Request $request): Team
    {
        return $request->user()->currentTeam;
    }

    /**
     * Find the course of the current request, limited to the current team.
     *
     * The identifier is read from the route instead of the method signature
     * because the team slug also occupies a route parameter position.
     */
    protected function findCurso(Request $request): Curso
    {
        $curso = Curso::forTeam($this->team($request))
            ->whereKey($request->route('curso'))
            ->first();

        abort_if($curso === null, 404);

        return $curso;
    }
}
