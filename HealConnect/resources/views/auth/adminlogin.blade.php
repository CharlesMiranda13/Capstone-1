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
<body>
      @include('loading') 
    <main style="display:none">
        <div class="tab-container">
            <div id="login" class="tab-content active">
                <div class="login-container">
                    <div class="logo-image">
                        <img src="{{ asset('/images/logo.jpg') }}" alt="Logo" />
                    </div>  
                    <h2>Admin | Login</h2>
                    <form id="adminLoginForm" onsubmit="return validateAdminLogin()">
                        <div class="form-group">
                            <label for="username">Email:</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

   <script src="{{ asset('js/loading.js') }}"></script>
    <script src="{{ asset('/js/Admin_login.js') }}"></script>
</body>
</html>
