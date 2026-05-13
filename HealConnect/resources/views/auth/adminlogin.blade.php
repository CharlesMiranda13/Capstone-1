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
                <div class="admin-login-card">
                    <!-- Branding Side -->
                    <div class="admin-left-side">
                        <div class="logo-white">
                            <img src="{{ asset('/images/logo.jpg') }}" alt="Logo" />
                        </div>
                        <h1>Admin Portal</h1>
                        <p>Secure access to the HealConnect management dashboard.</p>
                    </div>

                    <!-- Login Form Side -->
                    <div class="admin-right-side">
                        <div class="login-container">
                            <h2 style="margin-top: 0; text-align: left; margin-bottom: 1.5rem;">Sign In</h2>
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
                                    <label for="email">Admin Email</label>
                                    <input type="text" id="email" name="email" value="{{ old('email') }}" required placeholder="admin@healconnect.com">
                                    @error('email')
                                        <span style="color:red;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" required placeholder="••••••••">
                                    @error('password')
                                        <span style="color:red;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" style="margin-top: 2rem;">Login to Dashboard</button>
                            </form>
                            <div style="text-align: center; margin-top: 2rem;">
                                <a href="{{ url('/') }}" style="color: var(--slate-500); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease;" onmouseover="this.style.color='var(--primary)'; this.style.textDecoration='underline'" onmouseout="this.style.color='var(--slate-500)'; this.style.textDecoration='none'">
                                    <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back to Homepage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/loading.js') }}"></script>
</body>
</html>
