<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Profile - {{ $student->full_name ?? 'Student' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 11px;
            color: #666;
        }
        .student-info {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .photo {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 50%;
            object-fit: cover;
        }
        .details {
            flex: 1;
        }
        .details h2 {
            margin: 0 0 10px;
            font-size: 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 8px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            margin: 0 0 10px;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            background: #f5f5f5;
            padding: 5px 10px;
        }
        .section-content {
            padding: 10px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-row .label {
            width: 150px;
            flex-shrink: 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Profile</h1>
        <p>Generated on: {{ now()->format('jS F Y, g:i A') }}</p>
    </div>

    @php
        $photoUrl = $student->photo_path
            ? asset('storage/' . $student->photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name ?? 'Student') . '&background=4f46e5&color=fff&size=120&rounded=true';
        $cls = data_get($student, 'classArm.schoolClass.name');
        $arm = data_get($student, 'classArm.name');
        $className = trim(($cls ?: '-') . ' ' . ($arm ?: ''));
        $primaryGuardian = null;
        if (isset($student->parentsGuardians)) {
            $primaryGuardian = $student->parentsGuardians->firstWhere('pivot.is_primary_contact', true) ?? $student->parentsGuardians->first();
        }
    @endphp

    <div class="student-info">
        <img src="{{ $photoUrl }}" alt="Student Photo" class="photo">
        <div class="details">
            <h2>{{ $student->full_name ?? '-' }}</h2>
            <div class="info-grid">
                <div class="label">Admission No:</div>
                <div>{{ $student->admission_no ?? '-' }}</div>
                <div class="label">Class:</div>
                <div>{{ $className }}</div>
                <div class="label">Academic Year:</div>
                <div>{{ data_get($student, 'academicSession.name', '—') }}</div>
                <div class="label">Status:</div>
                <div>{{ ucfirst($student->status ?? 'Inactive') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Personal Information</h3>
        <div class="section-content">
            <div class="info-row">
                <div class="label">Full Name:</div>
                <div>{{ $student->full_name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Gender:</div>
                <div>{{ $student->gender ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Date of Birth:</div>
                <div>{{ $student->dob ? $student->dob->format('jS F Y') : '-' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Age:</div>
                <div>{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->age . ' years' : '-' }}</div>
            </div>
            <div class="info-row">
                <div class="label">State of Origin:</div>
                <div>{{ $student->state_of_origin ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="label">LGA:</div>
                <div>{{ $student->lga ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Admission Date:</div>
                <div>{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('jS F Y') : '-' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Guardian Information</h3>
        <div class="section-content">
            <div class="info-row">
                <div class="label">Name:</div>
                <div>{{ $primaryGuardian->full_name ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Relationship:</div>
                <div>{{ $primaryGuardian->relationship ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Phone:</div>
                <div>{{ $primaryGuardian->phone ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Email:</div>
                <div>{{ $primaryGuardian->email ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Portal Access</h3>
        <div class="section-content">
            <div class="info-row">
                <div class="label">Username (Email):</div>
                <div>{{ $student->user->email ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Password:</div>
                <div>{{ $student->admission_no ?? 'STUDENT' . $student->id }}</div>
            </div>
            <div class="info-row">
                <div class="label">Last Login:</div>
                <div>{{ isset($student->user) && !empty($student->user->last_login_at) ? \Carbon\Carbon::parse($student->user->last_login_at)->format('jS F Y, g:i A') : '—' }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This document is computer-generated and does not require a signature.</p>
    </div>
</body>
</html>
