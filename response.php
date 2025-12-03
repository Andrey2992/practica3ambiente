<?php
/**
 * CLASE: Response
 * DESCRIPCIÓN: Estandariza las respuestas JSON
 * BASADO EN: Unidad 7 (Backend-Frontend, API REST)
 * 
 * Mantiene la misma estructura JSON que tu backend.php anterior
 * pero de forma más profesional y reutilizable
 */

class response {
    
    /**
     * Respuesta exitosa
     * Retorna el mismo formato que antes: { exito: true, tasa: ..., monto_convertido: ... }
     * 
     * @param array $datos - Datos a retornar
     */
    public static function success($datos) {
        http_response_code(200);
        echo json_encode($datos);
    }
    
    /**
     * Respuesta de error
     * Retorna el mismo formato que antes: { exito: false, mensaje: ... }
     * 
     * @param string $mensaje - Mensaje de error
     * @param int $codigo - Código HTTP (400, 404, 500)
     */
    public static function error($mensaje, $codigo = 400) {
        http_response_code($codigo);
        echo json_encode(array(
            'exito' => false,
            'mensaje' => $mensaje
        ));
    }
}

?>