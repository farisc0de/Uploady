<?php

$role = new Uploady\Role($db, $user);

$role_data = $role->get($_GET['id']);
