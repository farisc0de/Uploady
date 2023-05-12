  <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
          <a class="navbar-brand" href="<?= $utils->siteUrl(); ?>">
              <?php if ($st['website_logo'] != null) : ?>
                  <img src="<?= $st['website_logo'] ?>" alt="<?= $st['website_name'] ?>" width="30" height="30" class="d-inline-block align-top">
              <?php else : ?>
                  <?= $st['website_name'] ?>
              <?php endif; ?>
          </a>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarColor01">
              <ul class="navbar-nav me-auto">
                  <li class="nav-item">
                      <a class="nav-link <?= $utils->linkActive($page, 'index'); ?>" href="<?= $utils->siteUrl('/index.php') ?>">
                          <span class="fa fa-home"></span> <?= $lang['navbar']['home'] ?>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?= $utils->linkActive($page, 'terms'); ?>" href="<?= $utils->siteUrl('/page.php?s=terms') ?>">
                          <span class="fa fa-file-text"></span> <?= $lang['navbar']['tos'] ?>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?= $utils->linkActive($page, 'about'); ?>" href="<?= $utils->siteUrl('/page.php?s=about') ?>">
                          <span class="fa fa-user"></span> <?= $lang['navbar']['about'] ?>
                      </a>
                  </li>
              </ul>
              <ul class="navbar-nav ml-auto">
                  <li class="nav-item">
                      <a class="nav-link dropdown-toggle" href="#" id="themeswitcher" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fa-solid fa-palette"></i>
                      </a>

                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="themeswitcher">
                          <a class="dropdown-item" href="<?= $utils->siteUrl("/?theme=light"); ?>"><?= $lang['theme']['light'] ?></a>
                          <a class="dropdown-item" href="<?= $utils->siteUrl("/?theme=dark"); ?>"><?= $lang['theme']['dark'] ?></a>
                      </div>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link dropdown-toggle" href="#" id="langswitcher" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fa-solid fa-globe"></i>
                      </a>

                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="langswitcher">
                          <?php foreach ($localization->getActiveLanguages() as $language) : ?>
                              <a class="dropdown-item" href="<?= $utils->siteUrl("/?lang=$language->language_code") ?>"><?= $language->language ?></a>
                          <?php endforeach; ?>
                      </div>
                  </li>
                  <?php if (isset($_SESSION['loggedin'])) : ?>
                      <li class="nav-item">
                          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="fa fa-user fa-fw"></i>
                          </a>

                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                              <a class="dropdown-item" href="<?= $utils->siteUrl('/profile/account.php'); ?>">
                                  <?= $lang['navbar']['settings'] ?>
                              </a>
                              <a class="dropdown-item" href="<?= $utils->siteUrl('/profile/my_files.php'); ?>"><?= $lang['navbar']['my_files'] ?></a>
                              <?php if ($data->role == 3) : ?>
                                  <a class="dropdown-item" href="<?= $utils->siteUrl('/admin/index.php'); ?>"><?= $lang['navbar']['dashboard'] ?></a>
                              <?php endif; ?>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="<?= $utils->siteUrl('/logout.php'); ?>">
                                  <?= $lang['navbar']['logout'] ?>
                              </a>
                          </div>
                      </li>
                  <?php else : ?>
                      <li class="nav-item">
                          <a class="nav-link <?= $utils->linkActive($page, 'signupPage'); ?>" href="<?= $utils->siteUrl('/signup.php') ?>">
                              <?= $lang['navbar']['signup'] ?>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link <?= $utils->linkActive($page, 'loginPage'); ?>" href="<?= $utils->siteUrl('/login.php') ?>">
                              <?= $lang['navbar']['login'] ?>
                          </a>
                      </li>
                  <?php endif; ?>
              </ul>
          </div>
      </div>
  </nav>