<?php
session_start();
require_once("../../includes/config.php");
require_once("../../includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403); echo "No autorizado"; exit;
}

$id          = intval($_POST["idMascota"]           ?? 0);
$nombre      = trim($_POST["nombreMascota"]         ?? "");
$especie     = trim($_POST["especieMascota"]        ?? "");
$sexo        = trim($_POST["sexoMascota"]           ?? "");
$edad        = intval($_POST["edadMascota"]         ?? 0);
$raza        = trim($_POST["razaMascota"]           ?? "");
$descripcion = trim($_POST["descripcionMascota"]    ?? "");
$lugar       = trim($_POST["lugarMascota"]          ?? "");
$estado      = trim($_POST["estadoAdopcionMascota"] ?? "");

if (!$id) { echo "ID no válido."; exit; }

// Verificar que la publicación pertenece al usuario
$check = $conn->prepare("SELECT idMascota FROM Mascota WHERE idMascota = :id AND idUsuarioFK1 = :uid");
$check->execute([":id" => $id, ":uid" => $_SESSION["usuario_id"]]);
if (!$check->fetch()) { echo "No autorizado."; exit; }

try {
    if (!empty($_FILES["foto1"]["tmp_name"])) {

        // ── Validar imagen: MIME real + tamaño + getimagesize ──
        $tmpPath  = $_FILES["foto1"]["tmp_name"];
        $fileSize = $_FILES["foto1"]["size"];

        if ($fileSize > IMG_MAX_SIZE_BYTES) {
            echo "La imagen supera el tamaño máximo de " . IMG_MAX_SIZE_MB . "MB."; exit;
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!in_array($mimeReal, IMG_TIPOS_PERMITIDOS)) {
            echo "El archivo no es una imagen válida. Solo se permiten JPG, PNG, GIF y WebP."; exit;
        }

        $info = @getimagesize($tmpPath);
        if (!$info) { echo "El archivo no es una imagen válida."; exit; }

        $foto = file_get_contents($tmpPath);

        $stmt = $conn->prepare("UPDATE Mascota SET
            nombreMascota       = :n,
            especieMascota      = :es,
            sexoMascota         = :sx,
            edadMascota         = :ed,
            razaMascota         = :r,
            descripcionMascota  = :d,
            lugarMascota        = :l,
            estadoAdopcionMascota = :est,
            fotoMascota         = :f
            WHERE idMascota     = :id");
        $stmt->bindValue(":f", $foto, PDO::PARAM_LOB);
    } else {
        $stmt = $conn->prepare("UPDATE Mascota SET
            nombreMascota       = :n,
            especieMascota      = :es,
            sexoMascota         = :sx,
            edadMascota         = :ed,
            razaMascota         = :r,
            descripcionMascota  = :d,
            lugarMascota        = :l,
            estadoAdopcionMascota = :est
            WHERE idMascota     = :id");
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
} catch (PDOException $e) {
    error_log("Error editarPublicacion.php: " . $e->getMessage());
    echo "Error al actualizar la publicación.";
}
?>
