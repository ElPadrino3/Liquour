<?php

class BDD {
    private $host = 'localhost';
    private $dbname = 'liquour_bdd';
    private $user = 'root';
    private $password = '';
    private $conexion;

    public function conectar() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conexion = new PDO($dsn, $this->user, $this->password, $opciones);
            
            return $this->conexion;
            
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public function desconectar() {
        $this->conexion = null;
    }
}
?>