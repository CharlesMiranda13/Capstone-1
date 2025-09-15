<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealConnect - Admin</title>
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/Logandreg.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/Loading.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-login">
    @include('loading') 
    <div id="page-content" style="display:none;">
        <main>
            <div class="tab-container">
                <div id="login" class="tab-content active">
                    <div class="login-container">
                        <div class="logo-image">
                            <img src="{{ asset('/images/logo.jpg') }}" alt="Logo" />
                        </div>  
                        <h2>Admin | Login</h2>
                        <form id="adminLoginForm" action="{{ route('admin.login') }}" method="POST">
                            @csrf
    
                            {{-- Show global error message --}}
                            @if ($errors->any())
                                <div class="alert alert-danger" style="color: red; margin-bottom: 10px;">
                                    <ul style="list-style: none; padding: 0;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="text" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" required>
                                @error('password')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/loading.js') }}"></script>
</body>
</html>
