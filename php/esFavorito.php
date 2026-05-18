<?php
session_start();
require_once("../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["favorito" => false]); exit;
}

$idMascota = intval($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT 1 FROM Favoritos
                        WHERE idUsuarioFK = :uid AND idMascotaFK = :mid");
$stmt->execute([":uid" => $_SESSION["usuario_id"], ":mid" => $idMascota]);

echo json_encode(["favorito" => (bool)$stmt->fetch()]);
?>