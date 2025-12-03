<?php

// Configuración de Base de Datos
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

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../tasacambio.php';
require_once __DIR__ . '/../response.php';

?>