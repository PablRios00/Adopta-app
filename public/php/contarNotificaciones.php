<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["noLeidas" => 0]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS noLeidas FROM Notificacion
                        WHERE idUsuarioFK = :uid AND leida = 0");
$stmt->execute([":uid" => $_SESSION["usuario_id"]]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(["noLeidas" => (int)($row["noLeidas"] ?? 0)]);
?>