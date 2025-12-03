<?php


class tasacambio {
    
    private $conexion;
    
    // Constructor
    public function __construct() {
        $db = Database::getInstance();
        $db->conectar();
        $this->conexion = $db->getConexion();
    }
    
    /**
     * Obtener la tasa de cambio entre dos monedas
     * Usa Prepared Statements para SEGURIDAD (Clase #10)
     * 
     * @param string $origen - Código ISO (USD, EUR, CRC)
     * @param string $destino - Código ISO (EUR, MXN, etc)
     * @return array|false - La tasa o false si no existe
     */
    public function obtenerTasa($origen, $destino) {
        try {
            // Prepared Statement - Previene SQL Injection
            $sql = "SELECT tasa FROM tasas_cambio WHERE moneda_origen = ? AND moneda_destino = ?";
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conexion->error);
            }
            
            // Bind parameters (Clase #10 - Prepared Statements)
            $stmt->bind_param("ss", $origen, $destino);
            
            // Ejecutar
            $stmt->execute();
            
            // Obtener resultado
            $resultado = $stmt->get_result();
            
            // Si existe la tasa
            if ($resultado->num_rows > 0) {
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
     * Convertir una cantidad de una moneda a otra
     * ESTE ES EL MÉTODO PRINCIPAL que reemplaza la lógica de backend.php
     * 
     * @param string $moneda_origen
     * @param string $moneda_destino
     * @param float $cantidad
     * @return array|false - Array con resultado o false si hay error
     */
    public function convertir($moneda_origen, $moneda_destino, $cantidad) {
        try {
            // Validaciones básicas (Unidad 4.1 - Condicionales)
            if ($cantidad <= 0) {
                throw new Exception("La cantidad debe ser mayor a 0");
            }
            
            if (empty($moneda_origen) || empty($moneda_destino)) {
                throw new Exception("Las monedas no pueden estar vacías");
            }
            
            // Obtener tasa de BD (en lugar del array anterior)
            $tasa = $this->obtenerTasa($moneda_origen, $moneda_destino);
            
            if ($tasa === false) {
                throw new Exception("Tasa de cambio no disponible");
            }
            
            // Realizar cálculo (igual que antes)
            $monto_convertido = round($cantidad * $tasa, 2);
            
            // Registrar conversión en historial (Opcional)
            $this->registrarConversion($moneda_origen, $moneda_destino, $cantidad, $monto_convertido, $tasa);
            
            // Retornar en formato que espera tu frontend
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
                'mensaje' => $e->getMessage()
            );
        }
    }
    
    /**
     * Obtener todas las tasas disponibles
     * Para futuras consultas
     */
    public function obtenerTodas() {
        try {
            $sql = "SELECT * FROM tasas_cambio ORDER BY moneda_origen, moneda_destino";
            $resultado = $this->conexion->query($sql);
            
            if ($resultado->num_rows > 0) {
                $tasas = array();
                while ($row = $resultado->fetch_assoc()) {
                    $tasas[] = $row;
                }
                return $tasas;
            }
            
            return array();
            
        } catch (Exception $e) {
            error_log("Error en obtenerTodas: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registrar la conversión realizada (Historial)
     * Esto es NUEVO en la Práctica #4
     * 
     * @param string $moneda_origen
     * @param string $moneda_destino
     * @param float $cantidad_origen
     * @param float $cantidad_destino
     * @param float $tasa
     */
    private function registrarConversion($moneda_origen, $moneda_destino, $cantidad_origen, $cantidad_destino, $tasa) {
        try {
            $sql = "INSERT INTO conversiones (moneda_origen, moneda_destino, cantidad_origen, cantidad_destino, tasa_aplicada) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando inserción: " . $this->conexion->error);
            }
            
            $stmt->bind_param("ssddd", $moneda_origen, $moneda_destino, $cantidad_origen, $cantidad_destino, $tasa);
            $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error registrando conversión: " . $e->getMessage());
        }
    }
}

?> 