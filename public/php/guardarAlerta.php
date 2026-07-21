<?php
session_start();
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$uid      = $_SESSION["usuario_id"];
$especie  = trim($_POST["especie"]   ?? "");
$sexo     = trim($_POST["sexo"]      ?? "");
$estado   = trim($_POST["estado"]    ?? "");
$municipio = trim($_POST["municipio"] ?? "");
$provincia = trim($_POST["provincia"] ?? "");
$comunidad = trim($_POST["comunidad"] ?? "");
$lat      = isset($_POST["lat"]) && $_POST["lat"] !== "" ? floatval($_POST["lat"]) : null;
$lon      = isset($_POST["lon"]) && $_POST["lon"] !== "" ? floatval($_POST["lon"]) : null;
$radio    = isset($_POST["radio"])   && $_POST["radio"] !== "" ? intval($_POST["radio"]) : 50;

if (!$especie && !$sexo && !$estado && !$municipio) {
    echo json_encode(["error" => "Debes tener al menos un filtro activo para guardar una alerta."]);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO Alerta
        (idUsuarioFK, especie, sexo, estado, municipio, provincia, comunidad, latitud, longitud, radioKm)
        VALUES (:uid, :especie, :sexo, :estado, :municipio, :provincia, :comunidad, :lat, :lon, :radio)");
    $stmt->execute([
        ":uid"       => $uid,
        ":especie"   => $especie   ?: null,
        ":sexo"      => $sexo      ?: null,
        ":estado"    => $estado    ?: null,
        ":municipio" => $municipio ?: null,
        ":provincia" => $provincia ?: null,
        ":comunidad" => $comunidad ?: null,
        ":lat"       => $lat,
        ":lon"       => $lon,
        ":radio"     => $radio
    ]);
    echo json_encode(["ok" => true]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>