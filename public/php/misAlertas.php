<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }

$stmt = $conn->prepare("SELECT * FROM Alerta WHERE idUsuarioFK = :uid ORDER BY fechaCreacion DESC");
$stmt->execute([":uid" => $_SESSION["usuario_id"]]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>