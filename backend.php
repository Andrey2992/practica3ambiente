<?php
session_start();
$tasas = array(
    'USD' => 1.0,
    'EUR' => 0.92,
    'CRC' => 520.5,
    'MXN' => 17.05,
    'BRL' => 4.97,
    'CAD' => 1.32,
    'COP' => 3850.0,
    'ARS' => 850.0
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

//este es para obtener  los datos del formulario
    $moneda_origen = isset($_POST['moneda_origen']) ? $_POST['moneda_origen'] : null;
    $moneda_destino = isset($_POST['moneda_destino']) ? $_POST['moneda_destino'] : null;
    $cantidad = isset($_POST['cantidad']) ? floatval($_POST['cantidad']) : 1;

    // se confirma que esteban las monedas en el array
    if (array_key_exists($moneda_origen, $tasas) && array_key_exists($moneda_destino, $tasas)) {
        // Calcular la tasa de cambio
        $tasa_origen = $tasas[$moneda_origen];
        $tasa_destino = $tasas[$moneda_destino];
        $tasa_cambio = $tasa_destino / $tasa_origen;

        //se calcula el monto
        $monto_convertido = $cantidad * $tasa_cambio;

        // aqui se responden con json
        header('Content-Type: application/json');
        echo json_encode(array(
            'exito' => true,
            'tasa' => round($tasa_cambio, 4),
            'monto_convertido' => round($monto_convertido, 2),
            'moneda_origen' => $moneda_origen,
            'moneda_destino' => $moneda_destino
        ));
    } else {
        // Error: moneda no válida
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(array(
            'exito' => false,
            'mensaje' => 'Moneda no válida'
        ));
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversor de Monedas</title>
   <link rel="stylesheet" href="backend.css" />
</head>
<body>
    <div class="container">
        <h1>Back del Conversor de Monedas</h1>
        <div class="info">
            <p>
                <strong>El backend funciona correctamente</strong>
            </p>
            <p>Realiza solicitudes al archivo con los siguientes parametros: </p>
            <ul>
                <li>Moneda origen: Codigo de la moneda (USD, EUR, CRC)</li>
                <li>Moneda destino: Codigo de moneda destino</li>
                <li>Cantidad: Monto a convertir</li>
            </ul>
        </div>
    </div>
</body>
</html>