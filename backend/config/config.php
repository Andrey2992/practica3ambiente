<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           
define('DB_NAME', 'convertidor_monedas');
define('DB_CHARSET', 'utf8mb4');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');


require_once __DIR__ . '/../models/Database.php';

require_once __DIR__ . '/../utils/Response.php';


require_once __DIR__ . '/../models/TasaCambio.php';

?>