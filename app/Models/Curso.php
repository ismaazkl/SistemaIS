<?php

namespace App\Models;

use Database\Factories\CursoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property string $curso
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team $team
 * @property-read Collection<int, Ficha> $fichas
 * @property-read int $fichas_count
 */
#[Fillable(['curso', 'team_id'])]
class Curso extends Model
{
    /** @use HasFactory<CursoFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cursos';

    /**
     * Get the team that owns this course.
     *
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the enrolment records of this course.
     *
     * @return HasMany<Ficha, $this>
     */
    public function fichas(): HasMany
    {
        return $this->hasMany(Ficha::class, 'curso_id');
    }

    /**
     * Scope the query to a single team.
     *
     * @param  Builder<Curso>  $query
     * @return Builder<Curso>
     */
    public function scopeForTeam(Builder $query, Team|int $team): Builder
    {
        return $query->where('team_id', $team instanceof Team ? $team->getKey() : $team);
    }

    /**
     * Scope the query to courses whose name contains the given search term.
     *
     * @param  Builder<Curso>  $query
     * @return Builder<Curso>
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        return $query->when($search !== '', fn (Builder $query) => $query->where('curso', 'like', '%'.$search.'%'));
    }

    /**
     * Order the query by course name.
     *
     * @param  Builder<Curso>  $query
     * @return Builder<Curso>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('LOWER(curso)');
    }
}
