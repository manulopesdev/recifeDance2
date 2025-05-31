<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: ../index.php');
    exit;
}

$id_aluno = $_SESSION['user_id'];

if (!isset($_POST['id_aula']) || !is_numeric($_POST['id_aula'])) {
    die("Aula inválida.");
}

$id_aula = intval($_POST['id_aula']);

// Deleta a inscrição
if ($conn->query("DELETE FROM presencas WHERE id_aula = $id_aula AND id_aluno = $id_aluno")) {
    header("Location: minhas_aulas.php?msg=cancelado");
    exit;
} else {
    die("Erro ao cancelar inscrição: " . $conn->error);
}