<?php
session_start();
require_once("../includes/conexion.php");
if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }

$id          = intval($_POST["idMascota"] ?? 0);
$nombre      = trim($_POST["nombreMascota"]         ?? "");
$especie     = trim($_POST["especieMascota"]         ?? "");
$sexo        = trim($_POST["sexoMascota"]            ?? "");
$edad        = intval($_POST["edadMascota"]          ?? 0);
$raza        = trim($_POST["razaMascota"]            ?? "");
$descripcion = trim($_POST["descripcionMascota"]     ?? "");
$lugar       = trim($_POST["lugarMascota"]           ?? "");
$estado      = trim($_POST["estadoAdopcionMascota"]  ?? "");

// Verificar que la publicación pertenece al usuario
$check = $conn->prepare("SELECT idMascota FROM Mascota WHERE idMascota=:id AND idUsuarioFK1=:uid");
$check->execute([":id"=>$id, ":uid"=>$_SESSION["usuario_id"]]);
if (!$check->fetch()) { echo "No autorizado."; exit; }

try {
    if (!empty($_FILES["foto1"]["tmp_name"])) {
        $foto = file_get_contents($_FILES["foto1"]["tmp_name"]);
        $stmt = $conn->prepare("UPDATE Mascota SET nombreMascota=:n, especieMascota=:es,
                                sexoMascota=:sx, edadMascota=:ed, razaMascota=:r,
                                descripcionMascota=:d, lugarMascota=:l,
                                estadoAdopcionMascota=:est, fotoMascota=:f
                                WHERE idMascota=:id");
        $stmt->bindValue(":f", $foto, PDO::PARAM_LOB);
    } else {
        $stmt = $conn->prepare("UPDATE Mascota SET nombreMascota=:n, especieMascota=:es,
                                sexoMascota=:sx, edadMascota=:ed, razaMascota=:r,
                                descripcionMascota=:d, lugarMascota=:l,
                                estadoAdopcionMascota=:est
                                WHERE idMascota=:id");
    }
    $stmt->bindValue(":n",   $nombre);
    $stmt->bindValue(":es",  $especie);
    $stmt->bindValue(":sx",  $sexo);
    $stmt->bindValue(":ed",  $edad);
    $stmt->bindValue(":r",   $raza);
    $stmt->bindValue(":d",   $descripcion);
    $stmt->bindValue(":l",   $lugar);
    $stmt->bindValue(":est", $estado);
    $stmt->bindValue(":id",  $id);
    $stmt->execute();
    echo "success";
} catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
?>