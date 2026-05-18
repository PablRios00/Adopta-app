<?php
session_start();
require_once("../includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403); echo "No autorizado"; exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idUsuario   = $_SESSION["usuario_id"];
    $nombre      = trim($_POST["nombreMascota"]);
    $especie     = trim($_POST["especieMascota"]);
    $sexo        = trim($_POST["sexoMascota"]);
    $edad        = trim($_POST["edadMascota"]);
    $raza        = trim($_POST["razaMascota"]);
    $descripcion = trim($_POST["descripcionMascota"]);
    $estado      = trim($_POST["estadoAdopcionMascota"]);
    $lugar       = trim($_POST["lugarMascota"]);

    if (empty($nombre) || empty($especie) || empty($sexo) ||
        empty($edad)   || empty($raza)   || empty($descripcion) ||
        empty($estado) || empty($lugar)) {
        echo "Por favor completa todos los campos."; exit;
    }

    // Recoger fotos
    $foto1 = !empty($_FILES["foto1"]["tmp_name"]) ? file_get_contents($_FILES["foto1"]["tmp_name"]) : null;
    $foto2 = !empty($_FILES["foto2"]["tmp_name"]) ? file_get_contents($_FILES["foto2"]["tmp_name"]) : null;
    $foto3 = !empty($_FILES["foto3"]["tmp_name"]) ? file_get_contents($_FILES["foto3"]["tmp_name"]) : null;

    if (!$foto1) { echo "Debes subir al menos una imagen."; exit; }

    $fechaPublicacion = date("Y-m-d");

    try {
        $stmt = $conn->prepare("INSERT INTO Mascota
            (nombreMascota, especieMascota, sexoMascota, edadMascota, razaMascota,
             descripcionMascota, lugarMascota, fotoMascota, fotoMascota2, fotoMascota3,
             estadoAdopcionMascota, fechaPublicacionMascota, idUsuarioFK1)
            VALUES (:nombre, :especie, :sexo, :edad, :raza, :descripcion, :lugar,
                    :foto1, :foto2, :foto3, :estado, :fecha, :idUsuario)");

        $stmt->bindParam(":nombre",      $nombre);
        $stmt->bindParam(":especie",     $especie);
        $stmt->bindParam(":sexo",        $sexo);
        $stmt->bindParam(":edad",        $edad);
        $stmt->bindParam(":raza",        $raza);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":lugar",       $lugar);
        $stmt->bindParam(":foto1",       $foto1, PDO::PARAM_LOB);
        $stmt->bindParam(":foto2",       $foto2, PDO::PARAM_LOB);
        $stmt->bindParam(":foto3",       $foto3, PDO::PARAM_LOB);
        $stmt->bindParam(":estado",      $estado);
        $stmt->bindParam(":fecha",       $fechaPublicacion);
        $stmt->bindParam(":idUsuario",   $idUsuario);
        $stmt->execute();

        echo "success";
    } catch (PDOException $e) {
        echo "Error al guardar: " . $e->getMessage();
    }
}
?>