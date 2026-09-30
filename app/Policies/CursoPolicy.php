<?php

namespace App\Policies;

use App\Enums\TeamPermission;
use App\Models\Curso;
use App\Models\User;

class CursoPolicy
{
    /**
     * Determine whether the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null;
    }

    /**
     * Determine whether the user can view the course.
     */
    public function view(User $user, Curso $curso): bool
    {
        return $this->belongsToTeam($user, $curso);
    }

    /**
     * Determine whether the user can create courses on their current team.
     */
    public function create(User $user): bool
    {
        return $this->canManageTeamCourses($user);
    }

    /**
     * Determine whether the user can update the course.
     */
    public function update(User $user, Curso $curso): bool
    {
        return $this->belongsToTeam($user, $curso)
            && $this->canManageTeamCourses($user);
    }

    /**
     * Determine whether the user can delete the course.
     */
    public function delete(User $user, Curso $curso): bool
    {
        return $this->update($user, $curso);
    }

    /**
     * Determine whether the user administers the courses of their current team.
     */
    protected function canManageTeamCourses(User $user): bool
    {
        $team = $user->currentTeam;

        return $team !== null
            && $user->belongsToTeam($team)
            && $user->hasTeamPermission($team, TeamPermission::UpdateTeam);
    }

    /**
     * Determine whether the course belongs to one of the user's teams.
     */
    protected function belongsToTeam(User $user, Curso $curso): bool
    {
        return $user->belongsToTeam($curso->team);
    }
}
