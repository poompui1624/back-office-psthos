<?php

namespace App\Policies;

/**
 * Authorisation for the employee module.
 *
 * @see BaseModulePolicy
 */
class EmployeePolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'employee';
    }
}
