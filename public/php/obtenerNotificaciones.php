<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }

$stmt = $conn->prepare("
    SELECT n.idNotificacion, n.mensaje, n.leida, n.fechaCreacion,
           n.idMascotaFK, m.nombreMascota, m.especieMascota
    FROM Notificacion n
    LEFT JOIN Mascota m ON m.idMascota = n.idMascotaFK
    WHERE n.idUsuarioFK = :uid
    ORDER BY n.fechaCreacion DESC
    LIMIT 20
");
$stmt->execute([":uid" => $_SESSION["usuario_id"]]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>