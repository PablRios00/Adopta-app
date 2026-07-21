<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$stmt = $conn->prepare("SELECT idUsuario, nombreUsuario, emailUsuario,
    lugarUsuario, latitudUsuario, longitudUsuario,
    (fotoPerfil IS NOT NULL AND LENGTH(fotoPerfil) > 0) AS tieneFoto,
    fechaRegistroUsuario
    FROM Usuario WHERE idUsuario = :id");
$stmt->execute([":id" => $_SESSION["usuario_id"]]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$u) { echo json_encode(["error" => "No encontrado"]); exit; }

echo json_encode($u);
?>