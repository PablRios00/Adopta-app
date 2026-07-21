<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403); exit;
}

$id = intval($_GET["id"] ?? 0);
if (!$id) { echo json_encode(["nombre" => "Usuario", "tieneFoto" => false]); exit; }

$stmt = $conn->prepare("SELECT nombreUsuario,
                               (fotoPerfil IS NOT NULL AND LENGTH(fotoPerfil) > 0) AS tieneFoto
                        FROM Usuario WHERE idUsuario = :id");
$stmt->execute([":id" => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "nombre"    => $row["nombreUsuario"] ?? "Usuario",
    "tieneFoto" => (bool)($row["tieneFoto"] ?? false)
]);
?>