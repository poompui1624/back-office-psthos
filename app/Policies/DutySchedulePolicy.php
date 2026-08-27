<?php

namespace App\Policies;

/**
 * Authorisation for the duty module.
 *
 * @see BaseModulePolicy
 */
class DutySchedulePolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'duty';
    }
}
