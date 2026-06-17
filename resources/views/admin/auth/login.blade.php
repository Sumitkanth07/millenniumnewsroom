<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | MILLENNIUM NEWSROOM</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-body">
    <form class="login-card" method="POST" action="{{ route('admin.login.store') }}">
        @csrf
        <h1>MILLENNIUM NEWSROOM Admin</h1>
        <p>Sign in to manage the website.</p>
        @error('email')<div class="notice danger">{{ $message }}</div>@enderror
        <label>Email <input name="email" type="email" value="{{ old('email') }}" required></label>
        <label>Password <input name="password" type="password" required></label>
        <label class="check"><input name="remember" type="checkbox"> Remember me</label>
        <button class="btn primary">Login</button>
    </form>
</body>
</html>
