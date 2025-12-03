<?php
/**
 * ARCHIVO: tasacambio.php
 * DESCRIPCIÓN: Clase DAO para gestionar tasas de cambio
 * BASADO EN: Clase #10 (DAO Pattern, Prepared Statements)
 * 
 * CORRECCIONES REALIZADAS:
 * 1. Nombre de clase con mayúscula inicial: tasacambio → TasaCambio
 * 2. Verificación correcta de conexión
 * 3. Manejo mejor de errores
 * 4. Sin errores de SQL
 */

class TasaCambio {
    
    private $conexion;
    
    /**
     * Constructor
     * Obtiene la instancia única de la BD
     */
    public function __construct() {
        try {
            // Obtener instancia de Database
            $db = Database::getInstance();
            
            // Conectar a la BD
            if (!$db->conectar()) {
                throw new Exception("No se pudo conectar a la base de datos");
            }
            
            // Obtener la conexión
            $this->conexion = $db->getConexion();
            
            // Verificar que la conexión sea válida
            if (!$this->conexion) {
                throw new Exception("Conexión nula después de conectar");
            }
            
        } catch (Exception $e) {
            error_log("Error en constructor TasaCambio: " . $e->getMessage());
            die("Error al inicializar TasaCambio: " . $e->getMessage());
        }
    }
    
    /**
     * MÉTODO: obtenerTasa
     * DESCRIPCIÓN: Obtiene la tasa entre dos monedas
     * 
     * @param string $origen - Código ISO (USD, EUR, CRC)
     * @param string $destino - Código ISO (EUR, MXN, etc)
     * @return float|false - La tasa o false si no existe
     */
    public function obtenerTasa($origen, $destino) {
        try {
            // Limpiar espacios
            $origen = trim($origen);
            $destino = trim($destino);
            
            // Verificar que la conexión existe
            if (!$this->conexion) {
                throw new Exception("No hay conexión a la BD");
            }
            
            // Prepared Statement - Previene SQL Injection (Clase #10)
            $sql = "SELECT tasa FROM tasas_cambio WHERE moneda_origen = ? AND moneda_destino = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            // Verificar que prepare() funcionó
            if (!$stmt) {
                throw new Exception("Error en prepare(): " . $this->conexion->error);
            }
            
            // Bind parameters: "ss" = dos strings
            // ESTO ES IMPORTANTE: Es "ss" no "sss"
            $stmt->bind_param("ss", $origen, $destino);
            
            // Ejecutar la consulta
            if (!$stmt->execute()) {
                throw new Exception("Error en execute(): " . $stmt->error);
            }
            
            // Obtener resultado
            $resultado = $stmt->get_result();
            
            // Verificar si hay resultados
            if ($resultado && $resultado->num_rows > 0) {
                $row = $resultado->fetch_assoc();
                return floatval($row['tasa']);
            }
            
            // No encontró tasa
            return false;
            
        } catch (Exception $e) {
            error_log("Error en obtenerTasa: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * MÉTODO: convertir
     * DESCRIPCIÓN: Realiza la conversión entre dos monedas
     * Este es el MÉTODO PRINCIPAL
     * 
     * @param string $moneda_origen
     * @param string $moneda_destino
     * @param float $cantidad
     * @return array - Array con resultado o error
     */
    public function convertir($moneda_origen, $moneda_destino, $cantidad) {
        try {
            // Limpiar entrada
            $moneda_origen = trim($moneda_origen);
            $moneda_destino = trim($moneda_destino);
            $cantidad = floatval($cantidad);
            
            // VALIDACIÓN 1: Verificar que los parámetros existen
            if (empty($moneda_origen) || empty($moneda_destino)) {
                return array(
                    'exito' => false,
                    'mensaje' => 'Las monedas no pueden estar vacías'
                );
            }
            
            // VALIDACIÓN 2: Verificar que la cantidad es válida
            if ($cantidad <= 0) {
                return array(
                    'exito' => false,
                    'mensaje' => 'La cantidad debe ser mayor a 0'
                );
            }
            
            // OBTENER TASA: Consultar la BD
            $tasa = $this->obtenerTasa($moneda_origen, $moneda_destino);
            
            // VALIDACIÓN 3: Verificar que la tasa existe
            if ($tasa === false) {
                return array(
                    'exito' => false,
                    'mensaje' => 'No hay tasa de cambio de ' . $moneda_origen . ' a ' . $moneda_destino
                );
            }
            
            // CÁLCULO: Realizar la conversión
            $monto_convertido = round($cantidad * $tasa, 2);
            
            // REGISTRAR: Guardar en historial (opcional)
            $this->registrarConversion($moneda_origen, $moneda_destino, $cantidad, $monto_convertido, $tasa);
            
            // RETORNAR: Respuesta exitosa
            // IMPORTANTE: El formato es exactamente igual al que tu frontend espera
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
     * MÉTODO: obtenerTodas
     * DESCRIPCIÓN: Obtiene todas las tasas de la BD
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
     * MÉTODO PRIVADO: registrarConversion
     * DESCRIPCIÓN: Registra la conversión en la tabla de historial
     * PRIVADO: Solo se usa dentro de esta clase
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
                return; // No registra si no hay conexión
            }
            
            $sql = "INSERT INTO conversiones 
                    (moneda_origen, moneda_destino, cantidad_origen, cantidad_destino, tasa_aplicada) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                // Si la tabla no existe, simplemente no registra
                return;
            }
            
            // Bind: ssddd = string, string, double, double, double
            $stmt->bind_param("ssddd", $moneda_origen, $moneda_destino, $cantidad_origen, $cantidad_destino, $tasa);
            $stmt->execute();
            
        } catch (Exception $e) {
            // Si hay error, solo lo registra en log, no interrumpe
            error_log("Error registrando conversión: " . $e->getMessage());
        }
    }
}

?>