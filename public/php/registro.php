<?php
session_start();
require_once("../../includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") exit;

$nombre    = trim($_POST["nombreUsuario"]     ?? "");
$email     = trim($_POST["emailUsuario"]      ?? "");
$email2    = trim($_POST["emailUsuario2"]     ?? "");
$pass      = trim($_POST["contrasenaUsuario"] ?? "");
$pass2     = trim($_POST["contrasena2"]       ?? "");
$lugar     = trim($_POST["lugarUsuario"]      ?? "");
$municipio = trim($_POST["municipioUsuario"]  ?? "");
$provincia = trim($_POST["provinciaUsuario"]  ?? "");
$comunidad = trim($_POST["comunidadUsuario"]  ?? "");
$latitud   = isset($_POST["latitudUsuario"])  && $_POST["latitudUsuario"]  !== "" ? floatval($_POST["latitudUsuario"])  : null;
$longitud  = isset($_POST["longitudUsuario"]) && $_POST["longitudUsuario"] !== "" ? floatval($_POST["longitudUsuario"]) : null;

header("Content-Type: application/json; charset=utf-8");

if (!$nombre || !$email || !$pass) {
    echo json_encode(["error" => "Completa los campos obligatorios."]); exit;
}
if ($email !== $email2) {
    echo json_encode(["error" => "Los correos no coinciden."]); exit;
}
if ($pass !== $pass2) {
    echo json_encode(["error" => "Las contraseñas no coinciden."]); exit;
}
if (strlen($pass) < 6) {
    echo json_encode(["error" => "La contraseña debe tener al menos 6 caracteres."]); exit;
}
if (!$municipio || !$provincia || !$comunidad) {
    echo json_encode(["error" => "Por favor selecciona un municipio, provincia y comunidad autónoma válidos."]); exit;
}

try {
    $check = $conn->prepare("SELECT idUsuario FROM Usuario WHERE nombreUsuario = :n OR emailUsuario = :e");
    $check->execute([":n" => $nombre, ":e" => $email]);
    if ($check->fetch()) {
        echo json_encode(["error" => "El usuario o correo ya existe."]); exit;
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO Usuario
        (nombreUsuario, contrasenaUsuario, emailUsuario,
         lugarUsuario, latitudUsuario, longitudUsuario, fechaRegistroUsuario)
        VALUES (:n, :p, :e, :l, :lat, :lon, CURDATE())");
    $stmt->execute([
        ":n"   => $nombre,
        ":p"   => $hash,
        ":e"   => $email,
        ":l"   => $lugar ?: null,
        ":lat" => $latitud,
        ":lon" => $longitud
    ]);

    echo json_encode(["ok" => true]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al registrar: " . $e->getMessage()]);
}
?>