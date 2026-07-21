<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["favorito" => false]);
    exit;
}

$idMascota = intval($_GET["id"] ?? 0);
$idUsuario = $_SESSION["usuario_id"];

$stmt = $conn->prepare("SELECT idFavorito FROM Favoritos
                        WHERE idUsuarioFK = :uid AND idMascotaFK = :mid");
$stmt->execute([":uid" => $idUsuario, ":mid" => $idMascota]);

echo json_encode(["favorito" => $stmt->fetch() !== false]);
?>