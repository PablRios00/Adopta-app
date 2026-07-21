<?php
session_start();
require_once("../../includes/config.php");
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$uid      = $_SESSION["usuario_id"];
$nombre   = trim($_POST["nombreUsuario"]     ?? "");
$email    = trim($_POST["emailUsuario"]      ?? "");
$pass     = trim($_POST["contrasenaUsuario"] ?? "");
$lugar    = trim($_POST["lugarUsuario"]      ?? "") ?: null;
$latitud  = isset($_POST["latitudUsuario"])  && $_POST["latitudUsuario"]  !== "" ? floatval($_POST["latitudUsuario"])  : null;
$longitud = isset($_POST["longitudUsuario"]) && $_POST["longitudUsuario"] !== "" ? floatval($_POST["longitudUsuario"]) : null;

if (empty($nombre)) {
    echo json_encode(["error" => "El nombre de usuario es obligatorio."]); exit;
}
if (empty($email)) {
    echo json_encode(["error" => "El correo electrónico es obligatorio."]); exit;
}

// Si no viene ubicación nueva, mantener la que ya tiene en BD
if ($lugar === null) {
    $stmtActual = $conn->prepare("SELECT lugarUsuario, latitudUsuario, longitudUsuario
                                  FROM Usuario WHERE idUsuario = :id");
    $stmtActual->execute([":id" => $uid]);
    $actual   = $stmtActual->fetch(PDO::FETCH_ASSOC);
    $lugar    = $actual["lugarUsuario"]    ?? null;
    $latitud  = $actual["latitudUsuario"]  !== null ? floatval($actual["latitudUsuario"])  : null;
    $longitud = $actual["longitudUsuario"] !== null ? floatval($actual["longitudUsuario"]) : null;
}

$fotoData = null;
if (!empty($_FILES["fotoPerfil"]["tmp_name"])) {
    $tmpPath  = $_FILES["fotoPerfil"]["tmp_name"];
    $fileSize = $_FILES["fotoPerfil"]["size"];

    // ── Validar imagen de perfil ──
    if ($fileSize > IMG_MAX_SIZE_BYTES) {
        echo json_encode(["error" => "La foto supera el tamaño máximo de " . IMG_MAX_SIZE_MB . "MB."]);
        exit;
    }

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeReal = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    if (!in_array($mimeReal, IMG_TIPOS_PERMITIDOS)) {
        echo json_encode(["error" => "El archivo no es una imagen válida. Solo se permiten JPG, PNG, GIF y WebP."]);
        exit;
    }

    $info = @getimagesize($tmpPath);
    if (!$info) {
        echo json_encode(["error" => "El archivo no es una imagen válida."]);
        exit;
    }

    $fotoData = file_get_contents($tmpPath);
}

try {
    if ($fotoData) {
        $stmt = $conn->prepare("UPDATE Usuario SET
            nombreUsuario   = :n,
            emailUsuario    = :e,
            fotoPerfil      = :f,
            lugarUsuario    = :l,
            latitudUsuario  = :lat,
            longitudUsuario = :lon
            WHERE idUsuario = :id");
        $stmt->bindValue(":f", $fotoData, PDO::PARAM_LOB);
    } else {
        $stmt = $conn->prepare("UPDATE Usuario SET
            nombreUsuario   = :n,
            emailUsuario    = :e,
            lugarUsuario    = :l,
            latitudUsuario  = :lat,
            longitudUsuario = :lon
            WHERE idUsuario = :id");
    }

    $stmt->bindValue(":n",   $nombre);
    $stmt->bindValue(":e",   $email);
    $stmt->bindValue(":l",   $lugar);
    $stmt->bindValue(":lat", $latitud);
    $stmt->bindValue(":lon", $longitud);
    $stmt->bindValue(":id",  $uid);
    $stmt->execute();

    if ($pass !== "") {
        $hash  = password_hash($pass, PASSWORD_BCRYPT);
        $stmtP = $conn->prepare("UPDATE Usuario SET contrasenaUsuario = :p WHERE idUsuario = :id");
        $stmtP->execute([":p" => $hash, ":id" => $uid]);
    }

    $_SESSION["usuario_nombre"] = $nombre;
    echo json_encode(["ok" => true]);

} catch (PDOException $e) {
    error_log("Error editarPerfil.php: " . $e->getMessage());
    echo json_encode(["error" => "Error al guardar el perfil."]);
}
?>
