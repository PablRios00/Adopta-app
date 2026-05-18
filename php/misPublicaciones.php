<?php
session_start();
require_once("../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }

$stmt = $conn->prepare("
    SELECT m.idMascota, m.nombreMascota, m.especieMascota, m.sexoMascota,
           m.edadMascota, m.razaMascota, m.descripcionMascota,
           m.lugarMascota, m.estadoAdopcionMascota, m.fechaPublicacionMascota,
           m.adoptado,
           u.idUsuario AS idPublicador,
           u.nombreUsuario AS nombrePublicador,
           (u.fotoPerfil IS NOT NULL AND LENGTH(u.fotoPerfil) > 0) AS tieneFoto
    FROM Mascota m
    JOIN Usuario u ON u.idUsuario = m.idUsuarioFK1
    WHERE m.idUsuarioFK1 = :id
    ORDER BY m.fechaPublicacionMascota DESC
");
$stmt->execute([":id" => $_SESSION["usuario_id"]]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>