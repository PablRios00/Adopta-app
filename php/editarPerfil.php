<?php
session_start();
require_once("../includes/conexion.php");
if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }

$nombre    = trim($_POST["nombreUsuario"]    ?? "");
$email     = trim($_POST["emailUsuario"]     ?? "");
$telefono  = trim($_POST["telefonoUsuario"]  ?? "");
$direccion = trim($_POST["direccionUsuario"] ?? "");
$pass      = trim($_POST["contrasenaUsuario"]?? "");

try {
    // Foto de perfil
    $fotoSql = "";
    $fotoVal = null;
    if (!empty($_FILES["fotoPerfil"]["tmp_name"])) {
        $fotoVal = file_get_contents($_FILES["fotoPerfil"]["tmp_name"]);
        $fotoSql = ", fotoPerfil = :foto";
    }

    if ($pass !== "") {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $sql  = "UPDATE Usuario SET nombreUsuario=:n, emailUsuario=:e,
                 telefonoUsuario=:t, direccionUsuario=:d,
                 contrasenaUsuario=:p $fotoSql WHERE idUsuario=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":p", $hash);
    } else {
        $sql  = "UPDATE Usuario SET nombreUsuario=:n, emailUsuario=:e,
                 telefonoUsuario=:t, direccionUsuario=:d $fotoSql WHERE idUsuario=:id";
        $stmt = $conn->prepare($sql);
    }

    $stmt->bindValue(":n",  $nombre);
    $stmt->bindValue(":e",  $email);
    $stmt->bindValue(":t",  $telefono);
    $stmt->bindValue(":d",  $direccion);
    $stmt->bindValue(":id", $_SESSION["usuario_id"]);
    if ($fotoVal !== null) $stmt->bindValue(":foto", $fotoVal, PDO::PARAM_LOB);
    $stmt->execute();

    $_SESSION["usuario_nombre"] = $nombre;
    echo "success";
} catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
?>