<?php

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

function payrollViewer(): User
{
    $user = User::factory()->create();
    Permission::findOrCreate('payroll.view');
    $user->givePermissionTo('payroll.view');

    return $user;
}

function seedPayslips(PayrollPeriod $period, int $count): void
{
    foreach (range(1, $count) as $i) {
        $payslip = Payslip::create([
            'payroll_period_id' => $period->id,
            'employee_id' => Employee::factory()->create()->id,
            'gross_income' => 30000,
            'total_deduction' => 5000,
            'net_pay' => 25000,
            'status' => 'draft',
            'generated_at' => now(),
        ]);

        $payslip->items()->create(['type' => 'income', 'code' => 'BASE', 'name' => 'เงินเดือน', 'quantity' => 1, 'unit_amount' => 30000, 'amount' => 30000, 'sort_order' => 1]);
        $payslip->items()->create(['type' => 'deduction', 'code' => 'TAX', 'name' => 'ภาษี', 'quantity' => 1, 'unit_amount' => 5000, 'amount' => 5000, 'sort_order' => 2]);
    }
}

test('the page does not run more queries as more payslips are added', function () {
    $small = PayrollPeriod::factory()->create(['year' => 2569, 'month' => 1]);
    seedPayslips($small, 2);

    $large = PayrollPeriod::factory()->create(['year' => 2569, 'month' => 2]);
    seedPayslips($large, 12);

    $user = payrollViewer();

    // The first request of the test warms the permission and settings caches,
    // which would otherwise show up as a difference between the two counts and
    // has nothing to do with how many rows the page renders.
    $this->actingAs($user)->get(route('payroll-periods.show', $small))->assertOk();

    $count = function (PayrollPeriod $period) use ($user): int {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->actingAs($user)->get(route('payroll-periods.show', $period))->assertOk();

        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $queries;
    };

    // The view lists each slip's income and deduction lines inline. Without
    // eager loading that is two queries per row, so a six-fold larger period
    // would cost far more queries rather than the same handful.
    expect($count($large))->toBe($count($small));
});

test('the totals match the sum of the payslips', function () {
    $period = PayrollPeriod::factory()->create(['year' => 2569, 'month' => 3]);
    seedPayslips($period, 4);

    $this->actingAs(payrollViewer())
        ->get(route('payroll-periods.show', $period))
        ->assertOk()
        ->assertSee(number_format(4 * 30000, 2))
        ->assertSee(number_format(4 * 5000, 2))
        ->assertSee(number_format(4 * 25000, 2));
});

test('a period with no payslips shows zeroes rather than blanks', function () {
    $period = PayrollPeriod::factory()->create(['year' => 2569, 'month' => 4]);

    $this->actingAs(payrollViewer())
        ->get(route('payroll-periods.show', $period))
        ->assertOk()
        ->assertSee('ยังไม่มีสลิปเงินเดือนในรอบนี้')
        ->assertSee('0.00');
});
