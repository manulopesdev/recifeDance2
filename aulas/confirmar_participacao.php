<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: ../index.php');
    exit;
}

$id_aluno = $_SESSION['user_id'];

if (!isset($_GET['id_aula']) || !is_numeric($_GET['id_aula'])) {
    die("Aula inválida.");
}

$id_aula = intval($_GET['id_aula']);

$sql = "
SELECT a.*, 
       p.nome AS parque_nome,
       p.endereco,
       u.nome AS professor_nome,
       (SELECT COUNT(*) FROM presencas WHERE id_aula = a.id) AS inscritos
FROM aulas a
JOIN parques p ON a.id_parque = p.id
JOIN professores_login u ON a.id_professor = u.id
WHERE a.id = $id_aula
";
$res = $conn->query($sql);

if ($res->num_rows === 0) {
    die("Aula não encontrada.");
}

$aula = $res->fetch_assoc();
$vagas_restantes = $aula['vagas'] - $aula['inscritos'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirmar Participação</title>
</head>
<body>
    <h2>Confirmar sua participação</h2>

    <p><strong>Parque:</strong> <?= htmlspecialchars($aula['parque_nome']) ?></p>
    <p><strong>Endereço:</strong> <?= htmlspecialchars($aula['endereco']) ?></p>
    <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($aula['data'])) ?></p>
    <p><strong>Horário:</strong> <?= substr($aula['horario'], 0, 5) ?></p>
    <p><strong>Professor:</strong> <?= htmlspecialchars($aula['professor_nome']) ?></p>
    <p><strong>Vagas restantes:</strong> <?= $vagas_restantes ?></p>

    <?php if ($vagas_restantes <= 0): ?>
        <p style="color:red;">Esta aula está lotada.</p>
    <?php else: ?>
        <form method="post" action="participar_aula.php">
            <input type="hidden" name="id_aula" value="<?= $id_aula ?>">
            <button type="submit">Confirmar Participação</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="javascript:history.back()">← Cancelar</a>
</body>
</html>
