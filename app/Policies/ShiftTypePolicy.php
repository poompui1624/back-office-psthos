<?php

namespace App\Policies;

/**
 * Authorisation for the duty module.
 *
 * @see BaseModulePolicy
 */
class ShiftTypePolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'duty';
    }
}
