<?php
session_start();
require_once("../../includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    exit;
}

$id = isset($_GET["id"]) ? intval($_GET["id"]) : $_SESSION["usuario_id"];

$stmt = $conn->prepare("SELECT fotoPerfil FROM Usuario WHERE idUsuario = :id");
$stmt->execute([":id" => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && !empty($row["fotoPerfil"])) {
    header("Content-Type: image/jpeg");
    header("Cache-Control: no-store");
    echo $row["fotoPerfil"];
} else {
    http_response_code(404);
}
?>