<?php

namespace App\Policies;

/**
 * Authorisation for the payroll module.
 *
 * @see BaseModulePolicy
 */
class PayrollPeriodPolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'payroll';
    }
}
