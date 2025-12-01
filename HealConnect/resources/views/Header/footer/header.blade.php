<header id="homepage">
  <div class="homepage-header">
    <div class="header-logo">
      <a href="{{ url('/') }}">
        <img src="{{ asset('images/logo.jpg') }}" alt="HealConnect Logo" />
        <h2>
          <span class="heal-blue">Heal</span><span class="connect-green">Connect</span>
        </h2>
      </a>
    </div>

    <nav id="navbar">
      <a href="{{ url('/') }}">Home</a>
      <a href="{{ url('/services') }}">Services</a>
      <a href="{{ url('/ptlist') }}">Therapist</a>
      <a href="{{ url('/pricing') }}">Pricing</a>
      <a href="{{ url('/contact') }}">Contact</a>
      <a href="{{ url('/logandsign') }}" class="btn-getstarted">Get Started</a>  
    </nav>
    
    <button class="hamburger" onclick="toggleMenu()">☰</button>
  </div>
</header>
