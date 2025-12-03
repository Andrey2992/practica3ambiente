<?php


class Database {
    private $conexion;
    private static $instancia = null;
    

    private function __construct() {
        $this->conexion = null;
    }
    

    public static function getInstance() {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia;
    }
    

    public function conectar() {
        try {
            $this->conexion = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME
            );
            

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
    

    public function getConexion() {
        return $this->conexion;
    }
    

    public function query($sql) {
        if (!$this->conexion) {
            return null;
        }
        return $this->conexion->query($sql);
    }
    

    public function prepare($sql) {
        if (!$this->conexion) {
            return null;
        }
        return $this->conexion->prepare($sql);
    }
    
    public function getError() {
        return $this->conexion ? $this->conexion->error : "Conexión no disponible";
    }
    

    public function cerrar() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
    

    public function __destruct() {
        $this->cerrar();
    }
}

?>