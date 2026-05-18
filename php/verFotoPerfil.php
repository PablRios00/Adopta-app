<?php
require_once("../includes/conexion.php");

$id = intval($_GET["id"] ?? 0);
if (!$id) { http_response_code(404); exit; }

$stmt = $conn->prepare("SELECT fotoPerfil FROM Usuario WHERE idUsuario = :id");
$stmt->execute([":id" => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && !empty($row["fotoPerfil"])) {
    header("Content-Type: image/jpeg");
    echo $row["fotoPerfil"];
} else {
    http_response_code(404);
}
?>