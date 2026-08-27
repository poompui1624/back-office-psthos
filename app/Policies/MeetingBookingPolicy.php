<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorisation for the meeting module.
 *
 * @see BaseModulePolicy
 */
class MeetingBookingPolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'meeting';
    }

    /**
     * Approving, rejecting, and cancelling all sit behind the module's approve permission.
     */
    public function approve(User $user, Model $model): bool
    {
        return $this->allows($user, 'approve') && $this->canSee($user, $model);
    }
}
