<?php

$utils = new Uploady\Utils();

// update from 2.0 to 3.0 can't be done automatically because of the new database structure
// Updator will be available in the future versions

$utils->redirect(SITE_URL . "/" . "install.php?v=3.0");
