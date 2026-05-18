<?php
session_start();
require_once("../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403); exit;
}

$stmt = $conn->prepare("
    SELECT m.idMascota, m.nombreMascota, m.especieMascota, m.sexoMascota,
           m.edadMascota, m.razaMascota, m.lugarMascota, m.estadoAdopcionMascota
    FROM Favoritos f
    JOIN Mascota m ON f.idMascotaFK = m.idMascota
    WHERE f.idUsuarioFK = :uid
    ORDER BY f.fechaFavorito DESC
");
$stmt->execute([":uid" => $_SESSION["usuario_id"]]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>