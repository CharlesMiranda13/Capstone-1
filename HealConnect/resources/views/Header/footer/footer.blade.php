<div class="footer-top-bar">
    <p>Physical HEALTH PROFESSIONAL? ADD YOUR PRACTICE. 
        <a href="{{url('logandsign') }}" class="get-listed-btn">GET LISTED</a>
    </p>
</div>

<!-- Main Footer -->
<footer class="healconnect-footer">
    <div class="footer-container">
        <div class="footer-left">
                  <img src="{{ asset('images/logo1.png') }}" alt="HealConnect Logo"  class="footer-logo">
            <p>&copy; {{ date('Y') }} HealConnect. All rights reserved.</p>
        </div>

        <div class="footer-right">
            <ul class="footer-links">
                <li><a href="{{url('/about') }}">About us</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="{{ url('/contact') }}">Contact</a></li>
            </ul>
        </div>
    </div>
</footer>
