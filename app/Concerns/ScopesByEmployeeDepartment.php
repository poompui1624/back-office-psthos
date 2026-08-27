<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Restricts records to the viewer's own department, reached through the
 * employee the record belongs to.
 *
 * For models that carry a `department_id` of their own, use
 * {@see ScopesByDepartment} instead — it filters on the column directly and
 * avoids the subquery.
 *
 * Like that trait, this one fails closed: an account with no employee record
 * has no department to compare against and therefore sees nothing.
 */
trait ScopesByEmployeeDepartment
{
    /**
     * The permission prefix this model scopes under, e.g. 'attendance'.
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

        return $query->whereHas(
            'employee',
            fn (Builder $employeeQuery) => $employeeQuery->where('department_id', $departmentId)
        );
    }
}
