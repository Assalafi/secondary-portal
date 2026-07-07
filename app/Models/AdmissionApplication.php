<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionApplication extends Model
{
    use SoftDeletes, Auditable;

    protected $auditModule = 'admissions';
    protected $auditIdentifier = 'application_number';

    protected $fillable = [
        'application_number',
        'parent_id',
        'payment_id',
        'invoice_id',
        // Student Information
        'first_name',
        'last_name',
        'other_name',
        'date_of_birth',
        'gender',
        'nationality',
        'state_of_origin',
        'lga',
        'home_address',
        'religion',
        'blood_group',
        'medical_conditions',
        'place_of_birth_town',
        'place_of_birth_lga',
        'place_of_birth_state',
        'health_status',
        'disability_details',
        // Academic Information
        'proposed_class_id',
        'proposed_class_arm_id',
        'academic_session_id',
        'previous_school',
        'reason_for_admission',
        // Guardian Information
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'guardian_occupation',
        'guardian_address',
        'guardian_city',
        'guardian_state',
        'guardian_relationship',
        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        // Documents
        'birth_certificate_path',
        'passport_photo_path',
        'previous_report_path',
        // Status
        'status',
        'admin_remarks',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /**
     * Generate unique application number
     */
    public static function generateApplicationNumber(): string
    {
        $year = date('Y');
        $lastApplication = self::whereYear('created_at', $year)
            ->latest('id')
            ->first();
        
        $number = $lastApplication ? intval(substr($lastApplication->application_number, -4)) + 1 : 1;
        
        return 'APP/' . $year . '/' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Parent who submitted the application
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Payment for the application
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Invoice for the application
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Proposed class for admission
     */
    public function proposedClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'proposed_class_id');
    }

    /**
     * Proposed class arm
     */
    public function proposedClassArm(): BelongsTo
    {
        return $this->belongsTo(ClassArm::class, 'proposed_class_arm_id');
    }

    /**
     * Academic session for admission
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Admin who reviewed the application
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get full name of applicant
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . ($this->other_name ? $this->other_name . ' ' : '') . $this->last_name;
    }

    /**
     * Check if application is paid
     */
    public function isPaid(): bool
    {
        // Check invoice status (new flow using invoices)
        if ($this->invoice_id !== null && $this->invoice) {
            return $this->invoice->status === 'Paid';
        }
        
        // Fallback to old payment flow (if still being used)
        if ($this->payment_id !== null && $this->payment) {
            return $this->payment->status === 'Completed';
        }
        
        return false;
    }

    /**
     * Check if application can be edited
     */
    public function canEdit(): bool
    {
        return in_array($this->status, ['Draft', 'Pending Payment']);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'Draft' => 'bg-secondary',
            'Pending Payment' => 'bg-warning',
            'Submitted' => 'bg-info',
            'Under Review' => 'bg-primary',
            'Approved' => 'bg-success',
            'Rejected' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
