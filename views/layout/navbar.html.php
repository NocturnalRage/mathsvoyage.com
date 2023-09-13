<nav class="navbar navbar-expand-md bg-primary mb-4" data-bs-theme="dark">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavBar" aria-controls="mainNavBar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse d-lg-flex" id="mainNavBar">
      <a class="navbar-brand col-lg-3 me-0" href="/">Cube Shack</a>
      <ul class="navbar-nav col-md-6 justify-content-lg-center">
        <li class="nav-item">
          <a class="nav-link px-4 <?= $activeLink == 'Home' ? 'active' : ''; ?>" href="/"><i class="bi bi-house"></i> Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-4 <?= $activeLink == 'Curricula' ? 'active' : ''; ?>" href="/curriculum"><i class="bi bi-table"></i> Curricula</a>
        </li>
      </ul>
      <ul class="navbar-nav col-md-3 justify-content-lg-center">
        <?php if ($loggedIn) { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"><?= $this->esc($user['given_name']); ?></a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="/profile">
                  <i class="bi bi-person-circle"></i> Profile
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="/logout"
                  onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();"
                >
                  <i class="bi bi-box-arrow-right"></i> Logout
                </a>
              </li>
              <form id="logout-form" action="/logout" method="post" style="display: none;">
                <?php $this->crsfToken(); ?>
              </form>
            </ul>
          </li>
        <?php } else { ?>
          <li class="nav-item">
            <a class="nav-link px-4 <?= $activeLink == 'Login' ? 'active' : ''; ?>" href="/login"><i class="bi bi-box-arrow-in-right"></i> Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-4 <?= $activeLink == 'Register' ? 'active' : ''; ?>" href="/register"><i class="bi bi-r-square"></i> Register</a>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>
