<?php
session_start();
require_once("../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$busqueda  = trim($_GET["busqueda"]  ?? "");
$especie   = trim($_GET["especie"]   ?? "");
$sexo      = trim($_GET["sexo"]      ?? "");
$estado    = trim($_GET["estado"]    ?? "");
$comunidad = trim($_GET["comunidad"] ?? "");
$provincia = trim($_GET["provincia"] ?? "");
$municipio = trim($_GET["municipio"] ?? "");

$where  = [];
$params = [];

if ($busqueda !== "") {
    $where[] = "(m.nombreMascota LIKE :busqueda OR m.razaMascota LIKE :busqueda2
                 OR m.descripcionMascota LIKE :busqueda3 OR m.lugarMascota LIKE :busqueda4)";
    $like = "%" . $busqueda . "%";
    $params[":busqueda"]  = $like;
    $params[":busqueda2"] = $like;
    $params[":busqueda3"] = $like;
    $params[":busqueda4"] = $like;
}
if ($especie   !== "") { $where[] = "m.especieMascota = :especie";       $params[":especie"]   = $especie; }
if ($sexo      !== "") { $where[] = "m.sexoMascota = :sexo";             $params[":sexo"]      = $sexo; }
if ($estado    !== "") { $where[] = "m.estadoAdopcionMascota = :estado"; $params[":estado"]    = $estado; }
if ($comunidad !== "") { $where[] = "m.lugarMascota LIKE :comunidad";    $params[":comunidad"] = "%$comunidad%"; }
if ($provincia !== "") { $where[] = "m.lugarMascota LIKE :provincia";    $params[":provincia"] = "%$provincia%"; }
if ($municipio !== "") { $where[] = "m.lugarMascota LIKE :municipio";    $params[":municipio"] = "%$municipio%"; }

$sql = "SELECT m.idMascota, m.nombreMascota, m.especieMascota, m.sexoMascota,
               m.edadMascota, m.razaMascota, m.lugarMascota,
               m.estadoAdopcionMascota, m.fechaPublicacionMascota,
               m.adoptado, m.idUsuarioFK1,
               u.nombreUsuario AS nombrePublicador
        FROM Mascota m
        JOIN Usuario u ON u.idUsuario = m.idUsuarioFK1";

if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY m.fechaPublicacionMascota DESC";

try {
    $stmt = $conn->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>