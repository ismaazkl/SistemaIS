<?php

namespace App\Policies;

use App\Models\Ficha;
use App\Models\User;

class FichaPolicy
{
    /**
     * Determine whether the user can view any enrolment records.
     */
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null;
    }

    /**
     * Determine whether the user can view the enrolment record.
     */
    public function view(User $user, Ficha $ficha): bool
    {
        return $this->belongsToTeam($user, $ficha);
    }

    /**
     * Determine whether the user can create enrolment records on their current team.
     */
    public function create(User $user): bool
    {
        $team = $user->currentTeam;

        return $team !== null && $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can update the enrolment record.
     */
    public function update(User $user, Ficha $ficha): bool
    {
        return $this->belongsToTeam($user, $ficha);
    }

    /**
     * Determine whether the user can delete the enrolment record.
     */
    public function delete(User $user, Ficha $ficha): bool
    {
        return $this->belongsToTeam($user, $ficha);
    }

    /**
     * Determine whether the record belongs to one of the user's teams.
     */
    protected function belongsToTeam(User $user, Ficha $ficha): bool
    {
        return $user->belongsToTeam($ficha->team);
    }
}
