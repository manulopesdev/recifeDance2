<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'professor') {
    header('Location: ../index.php');
    exit;
}

$id_professor = $_SESSION['user_id'];

if (!isset($_GET['id_aula']) || !is_numeric($_GET['id_aula'])) {
    die("Aula inválida.");
}

$id_aula = intval($_GET['id_aula']);

// Deleta a inscrição
if ($conn->query("DELETE FROM aulas WHERE id = $id_aula")) {
    header("Location: relatorio_aulas.php?msg=cancelado");
    exit;
} else {
    die("Erro ao cancelar inscrição: " . $conn->error);
}