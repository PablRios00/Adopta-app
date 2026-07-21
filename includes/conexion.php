<?php
require_once __DIR__ . '/config.php';

try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // En producción nunca mostrar el error real
    error_log("Fallo en la conexión: " . $e->getMessage());
    die(json_encode(["error" => "Error de conexión con la base de datos."]));
}
?>
