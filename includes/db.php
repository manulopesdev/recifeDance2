<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "recife_dance";

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}
?>
