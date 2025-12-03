<?php
/**
 * CLASE: Database
 * DESCRIPCIÓN: Gestiona la conexión a MySQL
 * BASADO EN: Clase #10 (MySQL y DAO Pattern)
 */

class database {
    private $conexion;
    private static $instancia = null;
    
    // Constructor privado para Singleton
    private function __construct() {
        $this->conexion = null;
    }
    
    // Obtener instancia única
    public static function getInstance() {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia;
    }
    
    // Conectar a la BD
    public function conectar() {
        try {
            $this->conexion = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME
            );
            
            // Verificar conexión
            if ($this->conexion->connect_error) {
                throw new Exception("Error de conexión: " . $this->conexion->connect_error);
            }
            
            $this->conexion->set_charset(DB_CHARSET);
            return true;
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Obtener conexión
    public function getConexion() {
        return $this->conexion;
    }
    
    // Ejecutar query
    public function query($sql) {
        if (!$this->conexion) {
            return null;
        }
        return $this->conexion->query($sql);
    }
    
    // Preparar statement (Prepared Statements - Seguridad)
    public function prepare($sql) {
        if (!$this->conexion) {
            return null;
        }
        return $this->conexion->prepare($sql);
    }
    
    // Obtener error
    public function getError() {
        return $this->conexion ? $this->conexion->error : "Conexión no disponible";
    }
    
    // Cerrar conexión
    public function cerrar() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
    
    // Destructor
    public function __destruct() {
        $this->cerrar();
    }
}

?>