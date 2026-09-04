<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Restricts records to the viewer's own department.
 *
 * Applies to models carrying a `department_id`. Whether a user is held to their
 * own department is decided by one permission per module: holding
 * `<prefix>.view.all` means "the whole hospital", and not holding it means
 * "my department only".
 *
 * A user with no employee record has no department to scope to, so they see
 * nothing rather than everything — failing closed is the safe direction when
 * the link between account and person is missing.
 */
trait ScopesByDepartment
{
    /**
     * The permission prefix this model scopes under, e.g. 'leave'.
     */
    abstract public function departmentScopePrefix(): string;

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->can($this->departmentScopePrefix().'.view.all')) {
            return $query;
        }

        $departmentId = $user->employee?->department_id;

        if (! $departmentId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where($this->getTable().'.department_id', $departmentId);
    }
}
