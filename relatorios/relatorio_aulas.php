<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'professor') {
    header('Location: ../index.php');
    exit;
}

$id_professor = $_SESSION['user_id'];

$sql = "
SELECT a.*, 
       p.nome AS parque_nome,
       (SELECT COUNT(*) FROM presencas WHERE id_aula = a.id) AS total_alunos
FROM aulas a
JOIN parques p ON a.id_parque = p.id
WHERE a.id_professor = $id_professor
ORDER BY a.data DESC
";

$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Aulas</title>
</head>
<body>
    <a href='../painel_professor.php'> <- Voltar ao início</a>
    <h2>Relatório das Suas Aulas</h2>

    <?php if ($res->num_rows === 0): ?>
        <p>Você ainda não cadastrou nenhuma aula.</p>
    <?php else: ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Data</th>
                <th>Horário</th>
                <th>Parque</th>
                <th>Inscritos</th>
                <th>Ver Alunos</th>
                <th>Cancelar Aula</th>
            </tr>
            <?php while ($aula = $res->fetch_assoc()): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($aula['data'])) ?></td>
                <td><?= substr($aula['horario'], 0, 5) ?></td>
                <td><?= htmlspecialchars($aula['parque_nome']) ?></td>
                <td><?= $aula['total_alunos'] ?></td>
                <td>
                    <a href="ver_alunos.php?id_aula=<?= $aula['id'] ?>">Ver alunos</a>
                </td>
                <td>
                    <a href="prof_cancelar_aula.php?id_aula=<?= $aula['id'] ?>">Cancelar Aula</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>
</html>
