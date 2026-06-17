<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Registration - {{ $globalSettings['school_name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
        }

        .school-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .school-logo img {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
            border-radius: 12px;
        }

        .register-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .register-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 30px;
            text-align: center;
            line-height: 1.5;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 6px;
        }

        .required {
            color: #e74c3c;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            background-color: #f8f8f8;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #333;
            background-color: white;
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .register-button {
            width: 100%;
            padding: 15px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .register-button:hover {
            background-color: #555;
        }

        .back-link {
            display: block;
            text-align: center;
            color: #666;
            text-decoration: none;
            font-size: 14px;
            margin-top: 20px;
        }

        .back-link:hover {
            color: #333;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background-color: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .alert-info {
            background-color: #e3f2fd;
            color: #1976d2;
            border: 1px solid #bbdefb;
        }

        .help-text {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .register-container {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="school-logo">
            @if ($globalSettings['school_logo'])
                <img src="{{ asset('storage/' . $globalSettings['school_logo']) }}"
                    alt="{{ $globalSettings['school_name'] }}">
            @else
                <div style="font-size: 20px; font-weight: 600; color: #333;">
                    {{ $globalSettings['school_name'] }}
                </div>
            @endif
        </div>

        <h1 class="register-title">Parent/Guardian Registration</h1>
        <p class="register-subtitle">
            Create an account to access your child's academic information, make payments, and communicate with the
            school.
        </p>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info">
            <strong>Note:</strong> After registration, you will have immediate access to the portal. 
            Please contact the school administrator with your registered email address to link your child(ren) to your account 
            so you can view their academic information and make payments.
        </div>

        <form method="POST" action="{{ route('parent.register.store') }}">
            @csrf

            <!-- Personal Information -->
            <div class="form-section">
                <div class="section-title">Personal Information</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Full Name <span class="required">*</span>
                        </label>
                        <input type="text" name="name" class="form-input" value="{{ old('name') }}"
                            placeholder="e.g., John Doe" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Phone Number <span class="required">*</span>
                        </label>
                        <input type="tel" name="phone" class="form-input" value="{{ old('phone') }}"
                            placeholder="e.g., +234 800 000 0000" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Email Address <span class="required">*</span>
                    </label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}"
                        placeholder="your.email@example.com" required>
                    <div class="help-text">This will be used as your login username</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="occupation" class="form-input" value="{{ old('occupation') }}"
                            placeholder="e.g., Engineer, Teacher">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Relationship to Student(s)</label>
                        <select name="primary_relationship" class="form-select">
                            <option value="Father" {{ old('primary_relationship') === 'Father' ? 'selected' : '' }}>
                                Father</option>
                            <option value="Mother" {{ old('primary_relationship') === 'Mother' ? 'selected' : '' }}>
                                Mother</option>
                            <option value="Guardian" {{ old('primary_relationship') === 'Guardian' ? 'selected' : '' }}>
                                Guardian</option>
                            <option value="Uncle" {{ old('primary_relationship') === 'Uncle' ? 'selected' : '' }}>Uncle
                            </option>
                            <option value="Aunt" {{ old('primary_relationship') === 'Aunt' ? 'selected' : '' }}>Aunt
                            </option>
                            <option value="Grandparent"
                                {{ old('primary_relationship') === 'Grandparent' ? 'selected' : '' }}>Grandparent
                            </option>
                            <option value="Other" {{ old('primary_relationship') === 'Other' ? 'selected' : '' }}>Other
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-textarea"
                        placeholder="Your residential address">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Account Security -->
            <div class="form-section">
                <div class="section-title">Account Security</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Password <span class="required">*</span>
                        </label>
                        <input type="password" name="password" class="form-input" required minlength="6">
                        <div class="help-text">Minimum 6 characters</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Confirm Password <span class="required">*</span>
                        </label>
                        <input type="password" name="password_confirmation" class="form-input" required minlength="6">
                    </div>
                </div>
            </div>

            <button type="submit" class="register-button">
                Create Account
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            ← Back to Login
        </a>
    </div>
</body>

</html>
