<?php
session_start();
require_once("../includes/conexion.php");
if (!isset($_SESSION["usuario_id"])) { http_response_code(403); echo "No autorizado"; exit; }

$id = intval($_POST["idMascota"] ?? 0);
if (!$id) { echo "ID no válido"; exit; }

$check = $conn->prepare("SELECT idMascota FROM Mascota WHERE idMascota=:id AND idUsuarioFK1=:uid");
$check->execute([":id" => $id, ":uid" => $_SESSION["usuario_id"]]);
if (!$check->fetch()) { echo "No autorizado"; exit; }

try {
    $stmt = $conn->prepare("UPDATE Mascota SET adoptado=1, estadoAdopcionMascota='Adoptado' WHERE idMascota=:id");
    $stmt->execute([":id" => $id]);
    echo "success";
} catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
?>