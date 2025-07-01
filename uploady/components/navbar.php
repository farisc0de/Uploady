  <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm sticky-top">
      <div class="container">
          <a class="navbar-brand d-flex align-items-center" href="<?= $utils->siteUrl(); ?>">
              <?php if ($st['website_logo'] != null) : ?>
                  <img src="<?= $st['website_logo'] ?>" alt="<?= $st['website_name'] ?>" width="30" height="30" class="d-inline-block align-top me-2">
                  <span class="fw-bold"><?= $st['website_name'] ?></span>
              <?php else : ?>
                  <i class="fas fa-cloud-upload-alt me-2"></i>
                  <span class="fw-bold"><?= $st['website_name'] ?></span>
              <?php endif; ?>
          </a>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarColor01">
              <ul class="navbar-nav me-auto">
                  <li class="nav-item">
                      <a class="nav-link <?= $utils->linkActive($page, 'index'); ?>" href="<?= $utils->siteUrl('/index.php') ?>">
                          <i class="fas fa-home me-1"></i> <?= $lang['navbar']['home'] ?>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?= $utils->linkActive($page, 'terms'); ?>" href="<?= $utils->siteUrl('/page.php?s=terms') ?>">
                          <i class="fas fa-file-alt me-1"></i> <?= $lang['navbar']['tos'] ?>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?= $utils->linkActive($page, 'about'); ?>" href="<?= $utils->siteUrl('/page.php?s=about') ?>">
                          <i class="fas fa-info-circle me-1"></i> <?= $lang['navbar']['about'] ?>
                      </a>
                  </li>
              </ul>

              <ul class="navbar-nav ms-auto">
                  <!-- Theme Switcher -->
                  <li class="nav-item me-1 dropdown">
                      <button class="btn nav-link px-2 rounded-circle d-flex align-items-center justify-content-center"
                          style="width: 36px; height: 36px;"
                          type="button" id="themeswitcher" data-bs-toggle="dropdown" aria-expanded="false"
                          data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= $lang['theme']['change_theme'] ?? 'Change Theme' ?>">
                          <i class="fas fa-palette"></i>
                      </button>

                      <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="themeswitcher">
                          <h6 class="dropdown-header"><?= $lang['theme']['select_theme'] ?? 'Select Theme' ?></h6>
                          <a class="dropdown-item d-flex align-items-center" href="<?= $utils->siteUrl("/?theme=light"); ?>">
                              <i class="fas fa-sun me-2"></i> <?= $lang['theme']['light'] ?>
                          </a>
                          <a class="dropdown-item d-flex align-items-center" href="<?= $utils->siteUrl("/?theme=dark"); ?>">
                              <i class="fas fa-moon me-2"></i> <?= $lang['theme']['dark'] ?>
                          </a>
                      </div>
                  </li>

                  <!-- Language Switcher -->
                  <li class="nav-item me-1 dropdown">
                      <button class="btn nav-link px-2 rounded-circle d-flex align-items-center justify-content-center"
                          style="width: 36px; height: 36px;"
                          type="button" id="langswitcher" data-bs-toggle="dropdown" aria-expanded="false"
                          data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= $lang['navbar']['change_language'] ?? 'Change Language' ?>">
                          <i class="fas fa-globe"></i>
                      </button>

                      <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="langswitcher">
                          <h6 class="dropdown-header"><?= $lang['navbar']['select_language'] ?? 'Select Language' ?></h6>
                          <?php foreach ($localization->getActiveLanguages() as $language) : ?>
                              <a class="dropdown-item d-flex align-items-center" href="<?= $utils->siteUrl("/?lang=$language->language_code") ?>">
                                  <span class="me-2"><?= $language->language_code ?></span> <?= $language->language ?>
                              </a>
                          <?php endforeach; ?>
                      </div>
                  </li>

                  <?php if (isset($_SESSION['loggedin'])) : ?>
                      <!-- User Menu -->
                      <li class="nav-item dropdown">
                          <button class="btn nav-link d-flex align-items-center" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                              <div class="d-flex align-items-center">
                                  <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                      <i class="fas fa-user text-primary"></i>
                                  </div>
                                  <span class="d-none d-md-inline"><?= $_SESSION['username'] ?? 'User' ?></span>
                                  <i class="fas fa-chevron-down ms-1 small"></i>
                              </div>
                          </button>

                          <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdown">
                              <h6 class="dropdown-header"><?= $lang['navbar']['account'] ?? 'Account' ?></h6>
                              <a class="dropdown-item d-flex align-items-center" href="<?= $utils->siteUrl('/profile/account.php'); ?>">
                                  <i class="fas fa-cog me-2"></i> <?= $lang['navbar']['settings'] ?>
                              </a>
                              <a class="dropdown-item d-flex align-items-center" href="<?= $utils->siteUrl('/profile/my_files.php'); ?>">
                                  <i class="fas fa-folder me-2"></i> <?= $lang['navbar']['my_files'] ?>
                              </a>
                              <?php if ($data->role == 3) : ?>
                                  <div class="dropdown-divider"></div>
                                  <h6 class="dropdown-header"><?= $lang['navbar']['admin'] ?? 'Administration' ?></h6>
                                  <a class="dropdown-item d-flex align-items-center" href="<?= $utils->siteUrl('/admin/index.php'); ?>">
                                      <i class="fas fa-tachometer-alt me-2"></i> <?= $lang['navbar']['dashboard'] ?>
                                  </a>
                              <?php endif; ?>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item d-flex align-items-center" href="<?= $utils->siteUrl('/logout.php'); ?>">
                                  <i class="fas fa-sign-out-alt me-2"></i> <?= $lang['navbar']['logout'] ?>
                              </a>
                          </div>
                      </li>
                  <?php else : ?>
                      <!-- Login/Signup Buttons -->
                      <li class="nav-item d-flex align-items-center ms-2">
                          <?php if ($settings->getSettingValue("disable_signup") == false): ?>
                              <a href="<?= $utils->siteUrl('/signup.php') ?>" class="btn btn-sm btn-outline-secondary me-2">
                                  <i class="fas fa-user-plus me-1"></i> <?= $lang['navbar']['signup'] ?>
                              </a>
                          <?php endif; ?>
                          <a href="<?= $utils->siteUrl('/login.php') ?>" class="btn btn-sm btn-primary">
                              <i class="fas fa-sign-in-alt me-1"></i> <?= $lang['navbar']['login'] ?>
                          </a>
                      </li>
                  <?php endif; ?>
              </ul>
          </div>
      </div>
  </nav>