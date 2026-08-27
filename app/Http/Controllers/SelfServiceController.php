<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDailySummary;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\MeetingBooking;
use App\Models\Payslip;
use App\Models\RepairRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

/**
 * The staff portal at /me.
 *
 * Every query here is keyed on the signed-in user's own employee record rather
 * than on a module permission, so a member of staff can read back what they
 * submitted without being able to see anyone else's. The `.own` permissions
 * only decide which sections appear.
 */
class SelfServiceController extends Controller
{
    public function index(): View
    {
        $employee = $this->employee();

        if (! $employee) {
            return view('me.unlinked');
        }

        return view('me.index', [
            'employee' => $employee,
            'pendingLeaveCount' => $this->leaveQuery($employee)->where('status', 'pending')->count(),
            'approvedLeaveDays' => (float) $this->leaveQuery($employee)
                ->where('status', 'approved')
                ->whereYear('start_date', now()->year)
                ->sum('total_days'),
            'upcomingDuties' => DutySchedule::query()
                ->with('shiftType')
                ->where('employee_id', $employee->id)
                ->whereDate('work_date', '>=', now()->toDateString())
                ->orderBy('work_date')
                ->limit(5)
                ->get(),
            'recentLeaves' => $this->leaveQuery($employee)
                ->with('leaveType')
                ->latest()
                ->limit(5)
                ->get(),
            'openRepairs' => $this->repairQuery($employee)
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
            'latestPayslip' => $this->payslipQuery($employee)
                ->with('payrollPeriod')
                ->latest('generated_at')
                ->first(),
        ]);
    }

    public function leaves(): View
    {
        $employee = $this->requireEmployee();

        return view('me.leaves', [
            'employee' => $employee,
            'leaveRequests' => $this->leaveQuery($employee)
                ->with(['leaveType', 'approver'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function duties(Request $request): View
    {
        $employee = $this->requireEmployee();

        ['month' => $month, 'year' => $year, 'start_date' => $from, 'end_date' => $to]
            = resolve_month_filter($request->input('month'), $request->input('year'));

        return view('me.duties', [
            'employee' => $employee,
            'month' => $month,
            'year' => $year,
            'schedules' => DutySchedule::query()
                ->with(['shiftType', 'department'])
                ->where('employee_id', $employee->id)
                ->whereDate('work_date', '>=', $from)
                ->whereDate('work_date', '<=', $to)
                ->orderBy('work_date')
                ->get(),
        ]);
    }

    public function attendance(Request $request): View
    {
        $employee = $this->requireEmployee();

        ['month' => $month, 'year' => $year, 'start_date' => $from, 'end_date' => $to]
            = resolve_month_filter($request->input('month'), $request->input('year'));

        $summaries = AttendanceDailySummary::query()
            ->where('employee_id', $employee->id)
            ->whereDate('work_date', '>=', $from)
            ->whereDate('work_date', '<=', $to)
            ->orderBy('work_date')
            ->get();

        return view('me.attendance', [
            'employee' => $employee,
            'month' => $month,
            'year' => $year,
            'summaries' => $summaries,
            'lateMinutes' => (int) $summaries->sum('late_minutes'),
            'absentDays' => $summaries->where('status', 'absent')->count(),
        ]);
    }

    public function payslips(): View
    {
        $employee = $this->requireEmployee();

        return view('me.payslips', [
            'employee' => $employee,
            'payslips' => $this->payslipQuery($employee)
                ->with('payrollPeriod')
                ->latest('generated_at')
                ->paginate(20),
        ]);
    }

    public function repairs(): View
    {
        $employee = $this->requireEmployee();

        return view('me.repairs', [
            'employee' => $employee,
            'repairRequests' => $this->repairQuery($employee)
                ->with('assignedUser')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function meetings(): View
    {
        $employee = $this->requireEmployee();

        return view('me.meetings', [
            'employee' => $employee,
            'bookings' => MeetingBooking::query()
                ->with('room')
                ->where('employee_id', $employee->id)
                ->latest('start_at')
                ->paginate(20),
        ]);
    }

    private function employee(): ?Employee
    {
        $employeeId = auth()->user()?->employee_id;

        return $employeeId ? Employee::with(['department', 'position'])->find($employeeId) : null;
    }

    /**
     * The signed-in user's employee record, or the "not linked yet" page.
     *
     * An account with no employee has no "mine" to show. That is an
     * administrative gap rather than a permission failure, so the section pages
     * send the user somewhere that explains it instead of returning a bare 403.
     */
    private function requireEmployee(): Employee
    {
        $employee = $this->employee();

        if ($employee === null) {
            throw new HttpResponseException(redirect()->route('me.index'));
        }

        return $employee;
    }

    private function leaveQuery(Employee $employee)
    {
        return LeaveRequest::query()->where('employee_id', $employee->id);
    }

    private function repairQuery(Employee $employee)
    {
        return RepairRequest::query()->where('requester_employee_id', $employee->id);
    }

    private function payslipQuery(Employee $employee)
    {
        return Payslip::query()->where('employee_id', $employee->id);
    }
}
