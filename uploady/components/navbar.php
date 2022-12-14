  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
          <a class="navbar-brand" href="<?= $utils->siteUrl(); ?>">
              <?= $st['website_name'] ?>
          </a>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarColor01">
              <ul class="navbar-nav me-auto">
                  <li class="nav-item <?= $utils->linkActive($page, 'index'); ?>">
                      <a class="nav-link" href="<?= $utils->siteUrl('/index.php') ?>">
                          <span class="fa fa-home"></span> Home
                      </a>
                  </li>
                  <li class="nav-item <?= $utils->linkActive($page, 'tos'); ?>">
                      <a class="nav-link" href="<?= $utils->siteUrl('/page.php?s=terms') ?>">
                          <span class="fa fa-file-text"></span> Terms of Services
                      </a>
                  </li>
                  <li class="nav-item <?= $utils->linkActive($page, 'about'); ?>">
                      <a class="nav-link" href="<?= $utils->siteUrl('/page.php?s=about') ?>">
                          <span class="fa fa-user"></span> About Us
                      </a>
                  </li>
              </ul>
              <ul class="navbar-nav ml-auto">
                  <li class="nav-item">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fa-solid fa-globe"></i>
                      </a>

                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                          <a class="dropdown-item" href="?lang=en">English</a>
                          <a class="dropdown-item" href="?lang=ar">Arabic</a>
                      </div>
                  </li>
                  <?php if (isset($_SESSION['loggedin'])) : ?>
                      <li class="nav-item">
                          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="fa fa-user fa-fw"></i>
                          </a>

                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                              <a class="dropdown-item" href="<?= $utils->siteUrl('/profile/account.php'); ?>">
                                  Settings
                              </a>
                              <a class="dropdown-item" href="<?= $utils->siteUrl('/profile/my_files.php'); ?>">My files</a>
                              <?php if ($data->role == 3) : ?>
                                  <a class="dropdown-item" href="<?= $utils->siteUrl('/admin/index.php'); ?>">Dashboard</a>
                              <?php endif; ?>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="<?= $utils->siteUrl('/logout.php'); ?>">
                                  Logout
                              </a>
                          </div>
                      </li>
                  <?php else : ?>
                      <li class="nav-item <?= $utils->linkActive($page, 'signupPage'); ?>">
                          <a class="nav-link" href="<?= $utils->siteUrl('/signup.php') ?>">
                              Sign up
                          </a>
                      </li>
                      <li class="nav-item <?= $utils->linkActive($page, 'loginPage'); ?>">
                          <a class="nav-link" href="<?= $utils->siteUrl('/login.php') ?>">
                              Login
                          </a>
                      </li>
                  <?php endif; ?>
              </ul>
          </div>
      </div>
  </nav>