<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $globalSettings['school_name'] }}</title>
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

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 60px 40px;
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .school-logo {
            margin-bottom: 40px;
            display: inline-block;
        }

        .school-logo img {
            max-width: 120px;
            max-height: 120px;
            object-fit: contain;
            border-radius: 12px;
        }

        .school-logo-placeholder {
            background-color: #e8e8e8;
            color: #333;
            padding: 30px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .login-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 16px;
            background-color: #f8f8f8;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #333;
            background-color: white;
        }

        .form-input::placeholder {
            color: #999;
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .login-button:hover {
            background-color: #555;
        }

        .back-link {
            color: #333;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            color: #555;
        }

        .alert {
            background-color: #fee;
            color: #c33;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            text-align: left;
        }

        .demo-info {
            background-color: #f0f8ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 12px;
            color: #0066cc;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 40px 30px;
                margin: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="school-logo">
            @if ($globalSettings['school_logo'])
                <img src="{{ asset('storage/' . $globalSettings['school_logo']) }}"
                    alt="{{ $globalSettings['school_name'] }}">
            @else
                <div class="school-logo-placeholder">{{ $globalSettings['school_name'] }}</div>
            @endif
        </div>

        <h1 class="login-title">{{ $globalSettings['school_name'] }}</h1>
        <p class="login-subtitle">{{ $globalSettings['academic_session'] }} • {{ $globalSettings['current_term'] }}</p>

        @if (session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 25px; font-size: 14px; text-align: left; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email or Admission Number:</label>
                <input type="text" id="email" name="email" class="form-input"
                    placeholder="Enter email or admission number" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-input"
                    placeholder="Enter your password" required>
            </div>

            <button type="submit" class="login-button">Login</button>
        </form>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <a href="#" class="back-link" style="font-size: 14px;">
                Forgot Password?
            </a>
            <a href="{{ route('parent.register') }}" class="back-link" style="font-size: 14px; color: #0066cc;">
                Register as Parent →
            </a>
        </div>

        <div style="margin-top: 25px; text-align: center;">
            <a href="{{ route('landing') }}" style="display: inline-flex; align-items: center; gap: 6px; color: #555; text-decoration: none; font-size: 14px; font-weight: 500; padding: 10px 24px; border: 1px solid #ddd; border-radius: 25px; transition: all 0.3s;">
                ← Back to Home Page
            </a>
        </div>

        <div style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee;">
            <p style="font-size: 12px; color: #999;">
                {{ $globalSettings['email'] }} | {{ $globalSettings['phone_number'] }}<br>
                {{ $globalSettings['school_address'] }}
            </p>
        </div>
    </div>
</body>

</html>
