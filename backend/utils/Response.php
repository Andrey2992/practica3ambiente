<?php

class response {
    
    /**
    
     * 
     * @param array $datos 
     */
    public static function success($datos) {
        http_response_code(200);
        echo json_encode($datos);
    }
    
    /**
     * 
     * @param string $mensaje 
     * @param int $codigo 
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