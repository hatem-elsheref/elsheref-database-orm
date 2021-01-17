<?php

require_once 'Core/DatabaseInterface.php';
require_once 'Core/Database.php';
require_once 'Core/Join.php';
require_once 'Core/DB.php';
require_once 'Core/configurations.php';


try {
    DB::open(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
} catch (Exception $e) {
    echo $e->getMessage();
}