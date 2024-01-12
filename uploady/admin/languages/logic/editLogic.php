<?php

$loclizer = new Uploady\Localization($db);

$language = $loclizer->loadLangauge($_GET["lang"]);
