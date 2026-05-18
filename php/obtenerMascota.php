<?php
session_start();
require_once("../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$id = intval($_GET["id"] ?? 0);
if (!$id) { echo json_encode(["error" => "ID no válido"]); exit; }

$stmt = $conn->prepare("
    SELECT m.idMascota, m.nombreMascota, m.especieMascota, m.sexoMascota,
           m.edadMascota, m.razaMascota, m.descripcionMascota, m.lugarMascota,
           m.estadoAdopcionMascota, m.fechaPublicacionMascota, m.idUsuarioFK1,
           u.nombreUsuario AS nombrePublicador,
           (u.fotoPerfil IS NOT NULL AND LENGTH(u.fotoPerfil) > 0) AS tieneFotoPublicador
    FROM Mascota m
    JOIN Usuario u ON u.idUsuario = m.idUsuarioFK1
    WHERE m.idMascota = :id
");
$stmt->execute([":id" => $id]);
$mascota = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mascota) { echo json_encode(["error" => "No encontrada"]); exit; }

echo json_encode($mascota);
?>