<?php


require_once 'Database/DatabaseInterface.php';
require_once 'Database/Database.php';
require_once 'Database/DB.php';
require_once 'Database/configurations.php';


try {
    DB::open(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
} catch (Exception $e) {
    echo $e->getMessage();
}