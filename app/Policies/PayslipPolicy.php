<?php

namespace App\Policies;

use App\Models\Payslip;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorisation for a single payslip.
 *
 * A payslip carries no department of its own, so the base policy would have let
 * anyone holding payroll.view open any payslip by changing the id in the URL.
 * Access is decided here on three grounds instead:
 *
 *   - payroll.view.all  — the whole hospital, for payroll staff
 *   - payroll.view      — the viewer's own department
 *   - payslip.view.own  — the viewer's own payslip and nothing else
 *
 * @see BaseModulePolicy
 */
class PayslipPolicy extends BaseModulePolicy
{
    protected function permissionPrefix(): string
    {
        return 'payroll';
    }

    public function view(User $user, Model $model): bool
    {
        if (! $model instanceof Payslip) {
            return false;
        }

        if ($this->isOwn($user, $model) && $user->can('payslip.view.own')) {
            return true;
        }

        if (! $user->can('payroll.view')) {
            return false;
        }

        if ($user->can('payroll.view.all')) {
            return true;
        }

        return $this->sharesDepartment($user, $model);
    }

    private function isOwn(User $user, Payslip $payslip): bool
    {
        return $user->employee_id !== null
            && $payslip->employee_id === $user->employee_id;
    }

    private function sharesDepartment(User $user, Payslip $payslip): bool
    {
        $departmentId = $user->employee?->department_id;

        return $departmentId !== null
            && $payslip->employee?->department_id === $departmentId;
    }
}
