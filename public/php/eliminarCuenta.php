<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$uid  = $_SESSION["usuario_id"];
$pass = trim($_POST["password"] ?? "");

if (empty($pass)) {
    echo json_encode(["error" => "Debes confirmar tu contraseña para eliminar la cuenta."]);
    exit;
}

try {
    // Verificar contraseña
    $stmt = $conn->prepare("SELECT contrasenaUsuario FROM Usuario WHERE idUsuario = :id");
    $stmt->execute([":id" => $uid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($pass, $row["contrasenaUsuario"])) {
        echo json_encode(["error" => "Contraseña incorrecta."]);
        exit;
    }

    // Eliminar en cascada (FK con ON DELETE CASCADE se encarga del resto)
    $stmtDel = $conn->prepare("DELETE FROM Usuario WHERE idUsuario = :id");
    $stmtDel->execute([":id" => $uid]);

    // Destruir sesión
    session_destroy();

    echo json_encode(["ok" => true]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>