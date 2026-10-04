<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atelier Sign In | Tōramally</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-900: #1F3D2B;
            --brass-500: #9C7A3C;
            --ivory-50: #FAF8F5;
            --ivory-100: #F3EFE6;
            --charcoal: #1E1E1E;
            --muted: #6B6862;
            --line: rgba(31, 61, 43, 0.15);
            --serif: 'Cormorant Garamond', Georgia, serif;
            --sans: 'Jost', system-ui, sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--sans);
            background: var(--green-900);
            color: var(--ivory-50);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #FAF8F5;
            color: var(--charcoal);
            border-radius: 8px;
            padding: 40px 36px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.35);
            border: 1px solid var(--brass-500);
        }
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-header h1 {
            font-family: var(--serif);
            font-size: 34px;
            letter-spacing: .08em;
            color: var(--green-900);
            margin-bottom: 4px;
        }
        .brand-header p {
            font-size: 13px;
            color: var(--muted);
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(31,61,43,0.25);
            background: #fff;
            border-radius: 4px;
            font-size: 14px;
            font-family: var(--sans);
            color: var(--charcoal);
            outline: none;
            transition: border-color .2s;
        }
        input:focus {
            border-color: var(--brass-500);
            box-shadow: 0 0 0 2px rgba(156,122,60,0.15);
        }
        .btn-submit {
            width: 100%;
            padding: 13px;
            background: var(--green-900);
            color: var(--ivory-50);
            border: 0;
            border-radius: 4px;
            font-family: var(--sans);
            font-size: 13px;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-weight: 500;
            cursor: pointer;
            transition: background .2s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: #2A523A;
        }
        .alert {
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #FCE8E6;
            color: #C5221F;
            border: 1px solid #FAD2CF;
        }
        .alert-success {
            background: #E6F4EA;
            color: #137333;
            border: 1px solid #CEEAD6;
        }
        .default-creds {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px dashed rgba(31,61,43,0.15);
            font-size: 12px;
            color: var(--muted);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-header">
            <h1>Tōramally</h1>
            <p>Atelier Operations Portal</p>
        </div>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Atelier Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', 'admin@toramally.com') }}" required autocomplete="email" autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required autocomplete="current-password" placeholder="••••••••">
            </div>

            <button type="submit" class="btn-submit">Enter Atelier</button>
        </form>

        <div class="default-creds">
            <div>Default Admin Login: <strong>admin@toramally.com</strong></div>
            <div>Password: <strong>admin123</strong></div>
        </div>
    </div>
</body>
</html>
