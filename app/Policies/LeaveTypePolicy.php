<?php

namespace App\Policies;

/**
 * Authorisation for the leave module.
 *
 * @see BaseModulePolicy
 */
class LeaveTypePolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'leave';
    }
}
