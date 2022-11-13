<?php
$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (!strpos($url, 'install.php')) {
    $adsense = $st['adsense_status'];

    if ($adsense == true) {
        $utils->script("https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-{$st['adsense_client_code']}", true);
    }
}
