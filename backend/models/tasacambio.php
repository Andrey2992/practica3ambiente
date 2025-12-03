<?php

class TasaCambio {
    
    private $conexion;
    
   
    public function __construct() {
        try {
            $db = Database::getInstance();
            
            if (!$db->conectar()) {
                throw new Exception("No se pudo conectar a la base de datos");
            }
            
            $this->conexion = $db->getConexion();
            
            if (!$this->conexion) {
                throw new Exception("Conexión nula después de conectar");
            }
            
        } catch (Exception $e) {
            error_log("Error en constructor TasaCambio: " . $e->getMessage());
            die("Error al inicializar TasaCambio: " . $e->getMessage());
        }
    }
    
    /**
    
     * 
     * @param string $origen 
     * @param string $destino 
     * @return float|false 
     */
    public function obtenerTasa($origen, $destino) {
        try {
            $origen = trim($origen);
            $destino = trim($destino);
            
            if (!$this->conexion) {
                throw new Exception("No hay conexión a la BD");
            }
            
            $sql = "SELECT tasa FROM tasas_cambio WHERE moneda_origen = ? AND moneda_destino = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error en prepare(): " . $this->conexion->error);
            }
            
            $stmt->bind_param("ss", $origen, $destino);
            
            if (!$stmt->execute()) {
                throw new Exception("Error en execute(): " . $stmt->error);
            }
            
            $resultado = $stmt->get_result();
            
            if ($resultado && $resultado->num_rows > 0) {
                $row = $resultado->fetch_assoc();
                return floatval($row['tasa']);
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error en obtenerTasa: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     
     * 
     * @param string $moneda_origen
     * @param string $moneda_destino
     * @param float $cantidad
     * @return array 
     */
    public function convertir($moneda_origen, $moneda_destino, $cantidad) {
        try {
            $moneda_origen = trim($moneda_origen);
            $moneda_destino = trim($moneda_destino);
            $cantidad = floatval($cantidad);
            
            if (empty($moneda_origen) || empty($moneda_destino)) {
                return array(
                    'exito' => false,
                    'mensaje' => 'Las monedas no pueden estar vacías'
                );
            }
            
            if ($cantidad <= 0) {
                return array(
                    'exito' => false,
                    'mensaje' => 'La cantidad debe ser mayor a 0'
                );
            }
            
           
            $tasa = $this->obtenerTasa($moneda_origen, $moneda_destino);
            
            
            if ($tasa === false) {
                return array(
                    'exito' => false,
                    'mensaje' => 'No hay tasa de cambio de ' . $moneda_origen . ' a ' . $moneda_destino
                );
            }
            
            
            $monto_convertido = round($cantidad * $tasa, 2);
            
        
            $this->registrarConversion($moneda_origen, $moneda_destino, $cantidad, $monto_convertido, $tasa);
            
           
            return array(
                'exito' => true,
                'tasa' => round($tasa, 4),
                'monto_convertido' => $monto_convertido,
                'moneda_origen' => $moneda_origen,
                'moneda_destino' => $moneda_destino
            );
            
        } catch (Exception $e) {
            error_log("Error en convertir: " . $e->getMessage());
            return array(
                'exito' => false,
                'mensaje' => 'Error al procesar la conversión: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * 
     * @return array - Array de tasas
     */
    public function obtenerTodas() {
        try {
            if (!$this->conexion) {
                throw new Exception("No hay conexión a la BD");
            }
            
            $sql = "SELECT * FROM tasas_cambio ORDER BY moneda_origen, moneda_destino";
            $resultado = $this->conexion->query($sql);
            
            if ($resultado && $resultado->num_rows > 0) {
                $tasas = array();
                while ($row = $resultado->fetch_assoc()) {
                    $tasas[] = $row;
                }
                return $tasas;
            }
            
            return array();
            
        } catch (Exception $e) {
            error_log("Error en obtenerTodas: " . $e->getMessage());
            return array();
        }
    }
    
    /**
     * 
     * @param string $moneda_origen
     * @param string $moneda_destino
     * @param float $cantidad_origen
     * @param float $cantidad_destino
     * @param float $tasa
     */
    private function registrarConversion($moneda_origen, $moneda_destino, $cantidad_origen, $cantidad_destino, $tasa) {
        try {
            if (!$this->conexion) {
                return; 
            }
            
            $sql = "INSERT INTO conversiones 
                    (moneda_origen, moneda_destino, cantidad_origen, cantidad_destino, tasa_aplicada) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
               
                return;
            }
            
          
            $stmt->bind_param("ssddd", $moneda_origen, $moneda_destino, $cantidad_origen, $cantidad_destino, $tasa);
            $stmt->execute();
            
        } catch (Exception $e) {
            
            error_log("Error registrando conversión: " . $e->getMessage());
        }
    }
}

?>