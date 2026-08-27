<?php

namespace App\Policies;

/**
 * Authorisation for the asset module.
 *
 * @see BaseModulePolicy
 */
class AssetCategoryPolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'asset';
    }
}
