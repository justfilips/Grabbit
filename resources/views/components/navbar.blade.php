<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{route('home')}}">Grabbit</a>

    <form class="d-flex flex-grow-1 mx-4" role="search">
      <input
        class="form-control rounded-pill"
        type="search"
        placeholder="Search items..."
        aria-label="Search"
        style="height: 40px;"
      />
      <button class="btn btn-primary ms-2 rounded-pill px-4" type="submit">Search</button>
    </form>

    <ul class="navbar-nav mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link" href="{{route('login.form')}}">Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('register.form')}}">Register</a>
      </li>
    </ul>
  </div>
</nav>