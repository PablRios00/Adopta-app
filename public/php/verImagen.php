<?php
require_once("../../includes/conexion.php");

if (!isset($_GET["id"])) exit;

$id = intval($_GET["id"]);
$n  = intval($_GET["n"] ?? 1);

$campo = "fotoMascota";
if ($n === 2) $campo = "fotoMascota2";
if ($n === 3) $campo = "fotoMascota3";

$stmt = $conn->prepare("SELECT $campo AS foto FROM Mascota WHERE idMascota = :id");
$stmt->bindParam(":id", $id);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && !empty($row["foto"])) {
    header("Content-Type: image/jpeg");
    echo $row["foto"];
} else {
    http_response_code(404);
}
?>