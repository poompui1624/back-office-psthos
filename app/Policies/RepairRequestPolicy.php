<?php

namespace App\Policies;

/**
 * Authorisation for the repair module.
 *
 * @see BaseModulePolicy
 */
class RepairRequestPolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'repair';
    }
}
