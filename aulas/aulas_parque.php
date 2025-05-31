<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: ../index.php');
    exit;
}

$id_aluno = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Parque inválido.");
}
$id_parque = intval($_GET['id']);

$parque_res = $conn->query("SELECT nome FROM parques WHERE id = $id_parque AND ativo = TRUE");
if ($parque_res->num_rows === 0) die("Parque não encontrado.");
$parque_nome = $parque_res->fetch_assoc()['nome'];

$data_hoje = date('Y-m-d');

$sql = "
SELECT a.*, u.nome AS professor_nome,
       (SELECT COUNT(*) FROM presencas WHERE id_aula = a.id) AS inscritos
FROM aulas a
JOIN professores_login u ON a.id_professor = u.id
WHERE a.id_parque = $id_parque AND a.data >= '$data_hoje'
ORDER BY a.data, a.horario
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aulas em <?= htmlspecialchars($parque_nome) ?></title>
</head>
<body>
    <h2>Aulas no parque <?= htmlspecialchars($parque_nome) ?></h2>
    <a href="../parques/parques.php">← Voltar aos parques</a><br><br>

    <?php if ($result->num_rows === 0): ?>
        <p>Não há aulas agendadas neste parque no momento.</p>
    <?php else: ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Data</th>
                <th>Horário</th>
                <th>Professor</th>
                <th>Vagas restantes</th>
                <th>Ação</th>
            </tr>
            <?php while ($aula = $result->fetch_assoc()):
                $id_aula = $aula['id'];
                $vagas_restantes = $aula['vagas'] - $aula['inscritos'];

                $inscrito = $conn->query("SELECT id FROM presencas WHERE id_aula = $id_aula AND id_aluno = $id_aluno")->num_rows > 0;
            ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($aula['data'])) ?></td>
                <td><?= substr($aula['horario'], 0, 5) ?></td>
                <td><?= htmlspecialchars($aula['professor_nome']) ?></td>
                <td style="text-align:center;"><?= $vagas_restantes ?></td>
                <td>
                    <?php if ($inscrito): ?>
                        <form method="post" action="cancelar_aula.php">
                            <input type="hidden" name="id_aula" value="<?= $id_aula ?>">
                            <button type="submit" style="background:red;color:white;">Cancelar</button>
                        </form>
                    <?php elseif ($vagas_restantes > 0): ?>
                        <a href="confirmar_participacao.php?id_aula=<?= $id_aula ?>">
                            <button type="button">Participar</button>
                        </a>
                    <?php else: ?>
                        <em>Esgotado</em>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>
</html>
