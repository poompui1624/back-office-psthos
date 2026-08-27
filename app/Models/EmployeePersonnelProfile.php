<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePersonnelProfile extends Model
{
    protected $fillable = [
        'employee_id',
        'nationality',
        'ethnicity',
        'religion',
        'blood_type',
        'marital_status',
        'military_status',
        'birth_place',
        'taxpayer_no',
        'social_security_no',
        'professional_license_no',
        'professional_license_expired_at',
        'father_name',
        'mother_name',
        'spouse_name',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'registered_address',
        'current_address',
        'family_members',
        'education_histories',
        'training_histories',
        'position_histories',
        'salary_histories',
        'service_histories',
        'disciplinary_histories',
        'decorations',
        'name_change_histories',
        'notes',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'professional_license_expired_at' => 'date',
            'registered_address' => 'array',
            'current_address' => 'array',
            'family_members' => 'array',
            'education_histories' => 'array',
            'training_histories' => 'array',
            'position_histories' => 'array',
            'salary_histories' => 'array',
            'service_histories' => 'array',
            'disciplinary_histories' => 'array',
            'decorations' => 'array',
            'name_change_histories' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
