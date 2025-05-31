<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: ../index.php');
    exit;
}

$id_aluno = $_SESSION['user_id'];

$sql = "
SELECT a.*, 
       p.nome AS parque_nome,
       p.endereco,
       u.nome AS professor_nome
FROM presencas pr
JOIN aulas a ON pr.id_aula = a.id
JOIN parques p ON a.id_parque = p.id
JOIN professores_login u ON a.id_professor = u.id
WHERE pr.id_aluno = $id_aluno
ORDER BY a.data, a.horario
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Minhas Aulas</title>
</head>
<body>
    <h2>Minhas Aulas Agendadas</h2>

    <a href="../parques/parques.php">← Ver todos os parques</a><br><br>
    <a href="../painel_aluno.php">← Voltar para o início</a><br><br>

    <?php if ($result->num_rows === 0): ?>
        <p>Você ainda não está inscrito em nenhuma aula.</p>
    <?php else: ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Data</th>
                <th>Horário</th>
                <th>Parque</th>
                <th>Professor</th>
                <th>Ação</th>
            </tr>
            <?php while ($aula = $result->fetch_assoc()): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($aula['data'])) ?></td>
                <td><?= substr($aula['horario'], 0, 5) ?></td>
                <td><?= htmlspecialchars($aula['parque_nome']) ?></td>
                <td><?= htmlspecialchars($aula['professor_nome']) ?></td>
                <td>
                    <form method="post" action="cancelar_aula.php">
                        <input type="hidden" name="id_aula" value="<?= $aula['id'] ?>">
                        <button type="submit" style="background:red;color:white;">Cancelar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>
</html>
