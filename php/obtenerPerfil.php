<?php
session_start();
require_once("../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }

$stmt = $conn->prepare("SELECT idUsuario, nombreUsuario, emailUsuario,
                               telefonoUsuario, direccionUsuario,
                               (fotoPerfil IS NOT NULL AND LENGTH(fotoPerfil) > 0) AS tieneFoto
                        FROM Usuario WHERE idUsuario = :id");
$stmt->execute([":id" => $_SESSION["usuario_id"]]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
?>