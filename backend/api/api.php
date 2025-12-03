<?php



require_once __DIR__ . '/../config/config.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    Response::error('Método no permitido. Use POST', 405);
    exit;
}


$moneda_origen = isset($_POST['moneda_origen']) ? trim($_POST['moneda_origen']) : null;
$moneda_destino = isset($_POST['moneda_destino']) ? trim($_POST['moneda_destino']) : null;
$cantidad = isset($_POST['cantidad']) ? floatval($_POST['cantidad']) : null;


try {
    
    $tasaCambio = new TasaCambio();
    
    
    $resultado = $tasaCambio->convertir($moneda_origen, $moneda_destino, $cantidad);
    
   
    if ($resultado['exito']) {
        Response::success($resultado);
    } else {
        Response::error($resultado['mensaje']);
    }
    
} catch (Exception $e) {
    
    error_log("Error en api.php: " . $e->getMessage());
    Response::error('Error del servidor: ' . $e->getMessage(), 500);
}

?>