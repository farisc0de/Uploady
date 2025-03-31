<?php
include_once 'session.php';
include_once APP_PATH . 'logic/pageLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php"><?= $lang["general"]['home'] ?? 'Home'; ?></a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= $page_content->title; ?></li>
        </ol>
      </nav>
      
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header py-3">
          <h5 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>
            <?= $page_content->title; ?>
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="page-content">
            <?= $page_content->content; ?>
          </div>
          
          <!-- Last Updated Info -->
          <?php if (isset($page_content->updated_at)) : ?>
          <div class="mt-5 pt-3 border-top">
            <small class="text-muted">
              <i class="fas fa-clock me-1"></i> <?= $lang["general"]['last_updated'] ?? 'Last updated'; ?>: 
              <?= date('F j, Y', strtotime($page_content->updated_at)); ?>
            </small>
          </div>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Back Button -->
      <div class="text-center mt-4">
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-2"></i><?= $lang["general"]['back'] ?? 'Back'; ?>
        </a>
      </div>
    </div>
  </div>
</div>

<style>
  /* Custom styles for page content */
  .page-content h1, .page-content h2, .page-content h3, 
  .page-content h4, .page-content h5, .page-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
  }
  
  .page-content p {
    margin-bottom: 1rem;
    line-height: 1.6;
  }
  
  .page-content ul, .page-content ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
  }
  
  .page-content li {
    margin-bottom: 0.5rem;
  }
  
  .page-content a {
    text-decoration: underline;
  }
  
  .page-content blockquote {
    padding: 1rem;
    border-left: 4px solid var(--bs-primary, #0d6efd);
    margin: 1.5rem 0;
  }
</style>

<?php include_once 'components/footer.php'; ?>