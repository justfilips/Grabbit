<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('home') }}">Grabbit</a>

    <form class="d-flex flex-grow-1 mx-4" role="search" method="GET" action="{{ route('home') }}">
      <input
        name="search"
        class="form-control rounded-pill"
        type="search"
        placeholder="Search items..."
        aria-label="Search"
        value="{{ request('search') }}"
        style="height: 40px;"
        data-translate
      />
      <button class="btn btn-primary ms-2 rounded-pill px-4" type="submit" data-translate>Search</button>
    </form>

    <ul class="navbar-nav mb-2 mb-lg-0">
      @guest
        <li class="nav-item">
          <a class="nav-link" href="{{ route('login.form') }}" data-translate>Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('register.form') }}" data-translate>Register</a>
        </li>
      @endguest

      @auth
        <li class="nav-item ms-3 d-flex align-items-center">
          <a href="{{ route('wishlist.index') }}" class="nav-link" title="Wishlist">
            <i class="bi bi-heart-fill text-primary fs-4"></i>
          </a>
        </li>

        <li class="nav-item dropdown">
          <a 
            class="nav-link dropdown-toggle d-flex align-items-center" 
            href="#" 
            id="navbarDropdown" 
            role="button" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            aria-label="User menu"
          >
            <img 
              src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : 'https://i.pinimg.com/474x/47/ba/71/47ba71f457434319819ac4a7cbd9988e.jpg' }}" 
              alt="Profile" 
              class="rounded-circle" 
              width="40" height="40"
            >
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <li>
                  <a class="dropdown-item" href="{{ route('profile') }}" data-translate>Profile</a>
              </li>
              <li>
                  <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item" data-translate>Logout</button>
                  </form>
              </li>
          </ul>
        </li>

        @if(auth()->user()->isAdmin())
          <li class="nav-item ms-2">
              <a href="{{ route('admin.panel') }}" class="btn btn-outline-primary rounded-pill px-3" data-translate>
                  Admin Panel
              </a>
          </li>
        @endif
      @endauth
    </ul>

    <div class="d-flex ms-3 align-items-center">
      <div class="btn-group" role="group" aria-label="Language selector">
        <button 
          type="button" 
          class="btn btn-outline-secondary" 
          onclick="translatePage('lv')"
        >
          LV
        </button>

        <button 
          type="button" 
          class="btn btn-outline-secondary" 
          onclick="translatePage('en')"
        >
          EN
        </button>
      </div>
    </div>
  </div>
</nav>
