<?php

namespace App\Policies;

/**
 * Authorisation for the position module.
 *
 * @see BaseModulePolicy
 */
class PositionPolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'position';
    }
}
