<?php

namespace App\Policies;

/**
 * Authorisation for the meeting module.
 *
 * @see BaseModulePolicy
 */
class MeetingRoomPolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'meeting';
    }
}
