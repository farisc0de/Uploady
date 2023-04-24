<?php
$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (!strpos($url, 'install.php')) {
    $analytics = $st['analytics_status'];

    if ($analytics == true) {
        $utils->script("https://www.googletagmanager.com/gtag/js?id={$st['analytics_code']}", true);

        echo "<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', '" . $st['analytics_code'] . "');
</script>";
    }
}
