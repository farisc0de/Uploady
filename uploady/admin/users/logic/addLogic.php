<?php

$roleobj = new \Uploady\Role($db, $user);
$roles = $roleobj->getAll();
