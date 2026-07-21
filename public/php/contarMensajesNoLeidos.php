<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["noLeidos" => 0]);
    exit;
}

$stmt = $conn->prepare("
    SELECT COUNT(*) AS noLeidos
    FROM Mensaje
    WHERE idReceptorFK = :uid AND leido = 0
");
$stmt->execute([":uid" => $_SESSION["usuario_id"]]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(["noLeidos" => (int)($row["noLeidos"] ?? 0)]);
?>