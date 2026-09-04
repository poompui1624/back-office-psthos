<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeePersonnelProfileController extends Controller
{
    public function edit(Employee $employee): View
    {
        abort_unless($this->canViewSensitiveEmployeeData(), 403);

        $employee->load(['department', 'position', 'personnelProfile.updatedBy']);

        $profile = $employee->personnelProfile()->firstOrNew([
            'employee_id' => $employee->id,
        ]);

        return view('employees.personnel-profile.edit', [
            'employee' => $employee,
            'profile' => $profile,
            'registeredAddress' => $profile->registered_address ?? [],
            'currentAddress' => $profile->current_address ?? [],
            'familyMembers' => $this->rows($profile->family_members, 3),
            'educationHistories' => $this->rows($profile->education_histories, 3),
            'trainingHistories' => $this->rows($profile->training_histories, 3),
            'positionHistories' => $this->rows($profile->position_histories, 3),
            'salaryHistories' => $this->rows($profile->salary_histories, 3),
            'serviceHistories' => $this->rows($profile->service_histories, 3),
            'disciplinaryHistories' => $this->rows($profile->disciplinary_histories, 2),
            'decorations' => $this->rows($profile->decorations, 2),
            'nameChangeHistories' => $this->rows($profile->name_change_histories, 2),
        ]);
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        abort_unless($this->canUpdateSensitiveEmployeeData(), 403);

        $validated = $request->validate([
            'nationality' => ['nullable', 'string', 'max:100'],
            'ethnicity' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'military_status' => ['nullable', 'string', 'max:100'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'taxpayer_no' => ['nullable', 'string', 'max:50'],
            'social_security_no' => ['nullable', 'string', 'max:50'],
            'professional_license_no' => ['nullable', 'string', 'max:100'],
            'professional_license_expired_at' => ['nullable', 'date'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'registered_address' => ['nullable', 'array'],
            'registered_address.*' => ['nullable', 'string', 'max:255'],
            'current_address' => ['nullable', 'array'],
            'current_address.*' => ['nullable', 'string', 'max:255'],
            'family_members' => ['nullable', 'array'],
            'family_members.*.*' => ['nullable', 'string', 'max:255'],
            'education_histories' => ['nullable', 'array'],
            'education_histories.*.*' => ['nullable', 'string', 'max:255'],
            'training_histories' => ['nullable', 'array'],
            'training_histories.*.*' => ['nullable', 'string', 'max:255'],
            'position_histories' => ['nullable', 'array'],
            'position_histories.*.*' => ['nullable', 'string', 'max:255'],
            'salary_histories' => ['nullable', 'array'],
            'salary_histories.*.*' => ['nullable', 'string', 'max:255'],
            'service_histories' => ['nullable', 'array'],
            'service_histories.*.*' => ['nullable', 'string', 'max:255'],
            'disciplinary_histories' => ['nullable', 'array'],
            'disciplinary_histories.*.*' => ['nullable', 'string', 'max:255'],
            'decorations' => ['nullable', 'array'],
            'decorations.*.*' => ['nullable', 'string', 'max:255'],
            'name_change_histories' => ['nullable', 'array'],
            'name_change_histories.*.*' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data = array_merge($validated, [
            'registered_address' => $this->filledValues($validated['registered_address'] ?? []),
            'current_address' => $this->filledValues($validated['current_address'] ?? []),
            'family_members' => $this->filledRows($validated['family_members'] ?? []),
            'education_histories' => $this->filledRows($validated['education_histories'] ?? []),
            'training_histories' => $this->filledRows($validated['training_histories'] ?? []),
            'position_histories' => $this->filledRows($validated['position_histories'] ?? []),
            'salary_histories' => $this->filledRows($validated['salary_histories'] ?? []),
            'service_histories' => $this->filledRows($validated['service_histories'] ?? []),
            'disciplinary_histories' => $this->filledRows($validated['disciplinary_histories'] ?? []),
            'decorations' => $this->filledRows($validated['decorations'] ?? []),
            'name_change_histories' => $this->filledRows($validated['name_change_histories'] ?? []),
            'updated_by' => auth()->id(),
        ]);

        $employee->personnelProfile()->updateOrCreate(
            ['employee_id' => $employee->id],
            $data
        );

        return redirect()
            ->route('employees.personnel-profile.edit', $employee)
            ->with('success', 'บันทึกข้อมูล ก.พ.7 เรียบร้อยแล้ว');
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $rows
     * @return array<int, array<string, mixed>>
     */
    private function rows(?array $rows, int $minimumRows): array
    {
        $rows = array_values($rows ?? []);

        while (count($rows) < $minimumRows) {
            $rows[] = [];
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, string>
     */
    private function filledValues(array $values): array
    {
        return collect($values)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter(fn ($value) => filled($value))
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, string>>
     */
    private function filledRows(array $rows): array
    {
        return collect($rows)
            ->map(fn ($row) => $this->filledValues((array) $row))
            ->filter(fn ($row) => $row !== [])
            ->values()
            ->all();
    }

    private function canViewSensitiveEmployeeData(): bool
    {
        return auth()->user()?->can('employee.sensitive.view')
            || auth()->user()?->can('employee.update');
    }

    private function canUpdateSensitiveEmployeeData(): bool
    {
        return auth()->user()?->can('employee.sensitive.update')
            || auth()->user()?->can('employee.update');
    }
}
