<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost';
$user = 'root';
$pass = 'p@ss1234';
$db   = 'canteen';


$dsn = mysqli_connect($host, $user, $pass, $db);

if (!$dsn) {
    echo 'Database connection failed !';
}
