<?php

use App\Enums\TeamRole;
use App\Models\Curso;
use App\Models\Ficha;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('cursos.index', ['current_team' => 'inexistente']))
        ->assertRedirect(route('login'));
});

test('authenticated users can view the courses of their team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    Curso::factory()->for($team)->create(['curso' => '1ERO EGB A']);
    $other = Curso::factory()->create(['curso' => 'CURSO AJENO']);

    $this->actingAs($user)
        ->get(route('cursos.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Cursos/Index')
            ->has('cursos', 1)
            ->where('cursos.0.curso', '1ERO EGB A')
        );

    expect($other->fresh())->not->toBeNull();
});

test('courses can be filtered by search term', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    Curso::factory()->for($team)->create(['curso' => '1ERO EGB A']);
    Curso::factory()->for($team)->create(['curso' => '2DO INFORMATICA A']);

    $this->actingAs($user)
        ->get(route('cursos.index', ['search' => 'informatica']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('cursos', 1)
            ->where('cursos.0.curso', '2DO INFORMATICA A')
            ->where('filters.search', 'informatica')
        );
});

test('the course list reports the number of fichas per course', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $curso = Curso::factory()->for($team)->create();

    Ficha::factory()->count(3)->for($team)->for($curso, 'curso')->create();

    $this->actingAs($user)
        ->get(route('cursos.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('cursos.0.fichasCount', 3)
        );
});

test('members cannot manage courses', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $team->members()->updateExistingPivot($user->id, ['role' => TeamRole::Member->value]);

    $this->actingAs($user)
        ->post(route('cursos.store'), ['curso' => '1ERO EGB A'])
        ->assertForbidden();

    expect(Curso::where('curso', '1ERO EGB A')->exists())->toBeFalse();
});

test('owners can create courses', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('cursos.store'), ['curso' => '1ERO EGB A'])
        ->assertRedirect();

    expect($team->cursos()->where('curso', '1ERO EGB A')->exists())->toBeTrue();
});

test('the course name is required', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('cursos.store'), ['curso' => ''])
        ->assertSessionHasErrors('curso');

    expect(Curso::count())->toBe(0);
});

test('course names must be unique within the team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    Curso::factory()->for($team)->create(['curso' => '1ERO EGB A']);

    $this->actingAs($user)
        ->post(route('cursos.store'), ['curso' => '1ERO EGB A'])
        ->assertSessionHasErrors(['curso' => 'Este curso ya está registrado.']);

    expect($team->cursos()->count())->toBe(1);
});

test('the same course name can exist in a different team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    Curso::factory()->create(['curso' => '1ERO EGB A']);

    $this->actingAs($user)
        ->post(route('cursos.store'), ['curso' => '1ERO EGB A'])
        ->assertRedirect();

    expect($team->cursos()->where('curso', '1ERO EGB A')->exists())->toBeTrue();
});

test('owners can rename a course of their team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $curso = Curso::factory()->for($team)->create(['curso' => '1ERO EGB A']);

    $this->actingAs($user)
        ->from(route('cursos.index'))
        ->patch(route('cursos.update', $curso), ['curso' => '1ERO EGB B'])
        ->assertRedirect(route('cursos.index'))
        ->assertSessionHasNoErrors();

    expect($curso->fresh()->curso)->toBe('1ERO EGB B');
});

test('a course name can be kept unchanged when updating', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $curso = Curso::factory()->for($team)->create(['curso' => '1ERO EGB A']);

    $this->actingAs($user)
        ->patch(route('cursos.update', $curso), ['curso' => '1ERO EGB A'])
        ->assertSessionHasNoErrors();

    expect($curso->fresh()->curso)->toBe('1ERO EGB A');
});

test('owners can delete a course without fichas', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $curso = Curso::factory()->for($team)->create();

    $this->actingAs($user)
        ->from(route('cursos.index'))
        ->delete(route('cursos.destroy', $curso))
        ->assertRedirect(route('cursos.index'));

    expect($curso->fresh())->toBeNull();
});

test('a course with fichas cannot be deleted', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $curso = Curso::factory()->for($team)->create();

    Ficha::factory()->for($team)->for($curso, 'curso')->create();

    $this->actingAs($user)
        ->delete(route('cursos.destroy', $curso))
        ->assertSessionHasErrors('curso');

    expect($curso->fresh())->not->toBeNull();
});

test('users cannot delete a course of another team', function () {
    $user = User::factory()->create();
    $curso = Curso::factory()->create();

    $this->actingAs($user)
        ->delete(route('cursos.destroy', $curso))
        ->assertNotFound();

    expect($curso->fresh())->not->toBeNull();
});

test('users cannot rename a course of another team', function () {
    $user = User::factory()->create();
    $curso = Curso::factory()->create();

    $this->actingAs($user)
        ->patch(route('cursos.update', $curso), ['curso' => 'INTRUSO'])
        ->assertNotFound();

    expect($curso->fresh()->curso)->not->toBe('INTRUSO');
});
