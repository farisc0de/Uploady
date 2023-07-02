<div class="text-center container">
  <ul class="list-inline">
    <li class="list-inline-item">
      <a href="<?= $st['twitter_link'] ?>" class="btn btn-info rounded-circle">
        <i class="fab fa-twitter text-white"></i>
      </a>
    </li>
    <li class="list-inline-item">
      <a href="<?= $st['instagram_link'] ?>" class="btn btn-danger rounded-circle">
        <i class="fab fa-instagram text-white"></i>
      </a>
    </li>
    <li class="list-inline-item">
      <a href="<?= $st['linkedin_link'] ?>" class="btn btn-primary rounded-circle">
        <i class="fab fa-linkedin-in text-white"></i>
      </a>
    </li>
  </ul>
</div>

<footer class="footer py-5 my-sm-10 bg-body-tertiary mt-auto py-3">
  <div class="container-fluid my-auto">
    <p class="m-0 text-center">
      <?= $lang["general"]['copyright_text'] ?> &copy;
      <?= $st['website_name'] . " - " . date('Y') ?>
    </p>
  </div>
</footer>

<?php include_once APP_PATH . 'components/js.php'; ?>

</body>

</html>