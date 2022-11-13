<?php

$adsense = $st['adsense_status'];

if ($adsense == true) {
    $utils->script("https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-{$st['adsense_client_code']}", async: true);
}
