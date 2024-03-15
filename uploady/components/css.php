<?php
if ($_SESSION['language'] == 'ar') {
    $utils->style("https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css");
} else {
    $utils->style("https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css");
}

$utils->style('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/fontawesome.min.css');
$utils->style('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
$utils->style('https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.css');
$utils->style('https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css');
$utils->style('css/main.css');
$utils->style('css/custom.css');
