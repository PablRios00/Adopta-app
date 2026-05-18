<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario    = trim($_POST["usuario"]);
    $correo     = trim($_POST["correo"]);
    $contrasena = trim($_POST["contrasena"]);
    $telefono   = trim($_POST["telefono"]);
    $direccion  = trim($_POST["direccion"]);

    if (empty($usuario) || empty($correo) || empty($contrasena)) {
        echo "Por favor completa todos los campos obligatorios.";
        exit;
    }

    try {
        // Verificar si el nombre de usuario o el correo ya existen
        $sqlCheck = "SELECT 1 FROM Usuario WHERE nombreUsuario = :usuario OR emailUsuario = :correo";
        $stmt = $conn->prepare($sqlCheck);
        $stmt->execute([':usuario' => $usuario, ':correo' => $correo]);

        if ($stmt->fetch()) {
            echo "El nombre de usuario o el correo ya existen.";
            exit;
        }

        // 🔐 Encriptar la contraseña
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

        // Insertar nuevo registro
        $sqlInsert = "INSERT INTO Usuario
            (nombreUsuario, contrasenaUsuario, telefonoUsuario, direccionUsuario, emailUsuario, fechaRegistroUsuario)
            VALUES (:nombre, :contrasena, :telefono, :direccion, :email, NOW())";

        $stmt = $conn->prepare($sqlInsert);
        $stmt->execute([
            ':nombre'      => $usuario,
            ':contrasena'  => $hash,
            ':telefono'    => $telefono,
            ':direccion'   => $direccion,
            ':email'       => $correo
        ]);

        echo "success";
    } catch (PDOException $e) {
        echo "Error SQL: " . $e->getMessage();
    }
}
?>