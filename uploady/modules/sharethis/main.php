<?php

if (strpos($_SERVER['REQUEST_URI'], 'download.php') !== false) {
    $utils->script("https://platform-api.sharethis.com/js/sharethis.js#property={$st['sharethis_code']}&product=inline-share-buttons");
}
