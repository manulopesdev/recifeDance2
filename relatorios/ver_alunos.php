<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'professor') {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['id_aula']) || !is_numeric($_GET['id_aula'])) {
    die("Aula inválida.");
}

$id_aula = intval($_GET['id_aula']);
$id_professor = $_SESSION['user_id'];

// Verifica se essa aula pertence ao professor logado
$verificacao = $conn->query("SELECT id FROM aulas WHERE id = $id_aula AND id_professor = $id_professor");

if ($verificacao->num_rows === 0) {
    die("Você não tem permissão para ver essa aula.");
}

// Buscar alunos inscritos
$sql = "
SELECT a.nome, a.email
FROM presencas pr
JOIN alunos_login a ON pr.id_aluno = a.id
WHERE pr.id_aula = $id_aula
";

$res = $conn->query($sql);

function formatarTelefone($numero) {
    // Remove tudo que não for número
    $numero = preg_replace('/\D/', '', $numero);

    // Celulares com 11 dígitos (ex: 81912345678)
    if (strlen($numero) === 11) {
        return '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 5) . '-' . substr($numero, 7);
    }

    // Telefones fixos com 10 dígitos (ex: 8131234567)
    if (strlen($numero) === 10) {
        return '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 4) . '-' . substr($numero, 6);
    }

    // Se não bater nenhum padrão, retorna como está
    return $numero;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Alunos Inscritos</title>
</head>
<body>
    <h2>Alunos inscritos na aula #<?= $id_aula ?></h2>
    <a href="relatorio_aulas.php">← Voltar</a><br><br>

    <?php if ($res->num_rows === 0): ?>
        <p>Nenhum aluno inscrito nesta aula.</p>
    <?php else: ?>
        <ul>
        <?php while ($aluno = $res->fetch_assoc()): ?>
            <li><?= htmlspecialchars($aluno['nome']) ?> – <?= htmlspecialchars($aluno['email']) ?></li>
        <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
