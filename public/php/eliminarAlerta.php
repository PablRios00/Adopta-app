<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }

$id  = intval($_POST["idAlerta"] ?? 0);
$uid = $_SESSION["usuario_id"];

if (!$id) { echo json_encode(["error" => "ID no válido"]); exit; }

$stmt = $conn->prepare("DELETE FROM Alerta WHERE idAlerta = :id AND idUsuarioFK = :uid");
$stmt->execute([":id" => $id, ":uid" => $uid]);
echo json_encode(["ok" => true]);
?>