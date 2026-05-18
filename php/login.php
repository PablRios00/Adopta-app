<?php
session_start();
require_once("../includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $contrasena = trim($_POST["contrasena"]);

    if (empty($usuario) || empty($contrasena)) {
        echo "Por favor completa todos los campos.";
        exit;
    }

    try {
        // Buscar el usuario
        $stmt = $conn->prepare("SELECT * FROM Usuario WHERE nombreUsuario = :usuario");
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verificar el hash de la contraseña
            if (password_verify($contrasena, $user["contrasenaUsuario"])) {
                $_SESSION["usuario_id"] = $user["idUsuario"];
                $_SESSION["usuario_nombre"] = $user["nombreUsuario"];

                // 🔁 Redirigir al nuevo index (HTML)
                header("Location: ../public/index.html");
                exit;

            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "El usuario no existe.";
        }

    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
}
?>