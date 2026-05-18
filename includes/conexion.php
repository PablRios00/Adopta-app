<?php
$servername = "localhost";
$username = "pablo";
$password = "1234";
$database = "adopcionesDB";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Fallo en la conexión: " . $e->getMessage());
}
?>