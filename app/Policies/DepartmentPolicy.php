<?php

namespace App\Policies;

/**
 * Authorisation for the department module.
 *
 * @see BaseModulePolicy
 */
class DepartmentPolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'department';
    }
}
