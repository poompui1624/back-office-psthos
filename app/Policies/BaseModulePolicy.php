<?php

namespace App\Policies;

use App\Concerns\ScopesByDepartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared authorisation for the CRUD modules.
 *
 * Every module in this application gates on the same permission shape —
 * `<prefix>.view`, `<prefix>.create`, `<prefix>.update`, `<prefix>.delete` — so a
 * subclass only has to name its prefix.
 *
 * Record-level visibility funnels through {@see self::canSee()}. That is the single
 * seam department scoping plugs into, so overriding it in one place changes who
 * can open a record everywhere.
 */
abstract class BaseModulePolicy
{
    /**
     * The permission prefix for this module, e.g. 'leave'.
     */
    abstract protected function permissionPrefix(): string;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'view');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->allows($user, 'view') && $this->canSee($user, $model);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'create');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->allows($user, 'update') && $this->canSee($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->allows($user, 'delete') && $this->canSee($user, $model);
    }

    /**
     * Whether this user may see this particular record.
     *
     * Records that carry a department are limited to the viewer's own department
     * unless they hold `<prefix>.view.all`. Records without one — reference data
     * such as leave types or shift types — stay visible to the whole module.
     */
    protected function canSee(User $user, Model $model): bool
    {
        if (! in_array(ScopesByDepartment::class, class_uses_recursive($model), true)) {
            return true;
        }

        if ($user->can($this->permissionPrefix().'.view.all')) {
            return true;
        }

        $departmentId = $user->employee?->department_id;

        return $departmentId !== null && $model->department_id === $departmentId;
    }

    protected function allows(User $user, string $ability): bool
    {
        return $user->can($this->permissionPrefix().'.'.$ability);
    }
}
