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

$res = $conn->query("SELECT id FROM presencas WHERE id_aula = $id_aula AND id_aluno = $id_aluno");
if ($res->num_rows > 0) {
    die("Você já está inscrito nessa aula.");
}

if ($conn->query("INSERT INTO presencas (id_aula, id_aluno, confirmado) VALUES ($id_aula, $id_aluno, FALSE)")) {
    header("Location: minhas_aulas.php?msg=inscrito");
    exit;
} else {
    die("Erro ao se inscrever: " . $conn->error);
}
