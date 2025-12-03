<?php
/**
 * ARCHIVO: backend/api/api.php
 * DESCRIPCIÓN: API de conversión de monedas
 * BASADO EN: Tu backend.php anterior + Clase #10 (DAO)
 * 
 * CAMBIO PRINCIPAL:
 * - ANTES: Usaba array hardcodeado
 * - AHORA: Usa BD MySQL con DAO Pattern
 * 
 * El formato JSON de respuesta es IDÉNTICO
 * Tu frontend (script.js) NO necesita cambios
 */

// Cargar configuración e incluir clases
require_once '../config.php';

// Verificar si es POST (Unidad 4.1 - Condicionales)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener parámetros del formulario (igual que antes)
    $moneda_origen = isset($_POST['moneda_origen']) ? trim($_POST['moneda_origen']) : null;
    $moneda_destino = isset($_POST['moneda_destino']) ? trim($_POST['moneda_destino']) : null;
    $cantidad = isset($_POST['cantidad']) ? floatval($_POST['cantidad']) : 1;
    
    // Crear instancia del DAO (Patrón DAO - Clase #10)
    $tasaCambio = new tasacambio();
    
    // Realizar conversión usando el DAO
    // ESTO REEMPLAZA la lógica anterior que consultaba el array
    $resultado = $tasaCambio->convertir($moneda_origen, $moneda_destino, $cantidad);
    
    // Retornar respuesta (igual formato que antes)
    if ($resultado['exito']) {
        Response::success($resultado);
    } else {
        Response::error($resultado['mensaje']);
    }
    
} else {
    // Si no es POST, error
    Response::error('Método no permitido', 405);
}

?>