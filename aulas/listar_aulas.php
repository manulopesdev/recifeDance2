<?php
session_start();
require '../includes/db.php';

// Verifica se o usuário é aluno
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: ../index.php');
    exit;
}

$id_aluno = $_SESSION['user_id'];
$data_hoje = date('Y-m-d');

// Pega o filtro do parque se enviado (GET)
$parque_id = isset($_GET['parque_id']) ? intval($_GET['parque_id']) : 0;

// Pega lista de parques para o select
$parques_res = $conn->query("SELECT id, nome FROM parques WHERE ativo = TRUE ORDER BY nome");

// Monta a query das aulas
$sql = "
SELECT 
    a.id,
    a.id_parque,
    p.nome AS nome_parque,
    p.endereco AS endereco_parque,
    a.data,
    a.horario,
    u.nome AS nome_professor,
    -- Conta quantos alunos já inscritos
    (SELECT COUNT(*) FROM presencas pr WHERE pr.id_aula = a.id) AS inscritos,
    a.vagas -- supondo que você tenha esse campo na tabela aulas
FROM aulas a
JOIN parques p ON a.id_parque = p.id
JOIN professores_login u ON a.id_professor = u.id
WHERE a.data >= '$data_hoje'
";

if ($parque_id > 0) {
    $sql .= " AND a.id_parque = $parque_id ";
}

$sql .= " ORDER BY a.data, a.horario";

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Aulas Disponíveis</title>
</head>
<body>
<a href="../painel_aluno.php">← Voltar para o início</a><br><br>
<h1>Aulas Disponíveis</h1>

<!-- Formulário filtro por parque -->
<form method="get" action="listar_aulas.php">
    <label for="parque_id">Filtrar por Parque:</label>
    <select name="parque_id" id="parque_id" onchange="this.form.submit()">
        <option value="0">Todos</option>
        <?php while($parque = $parques_res->fetch_assoc()): ?>
            <option value="<?= $parque['id'] ?>" <?= ($parque['id'] == $parque_id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($parque['nome']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<table border="1" cellpadding="5" cellspacing="0" style="margin-top:10px;">
    <tr>
        <th>Parque</th>
        <th>Endereço</th>
        <th>Data</th>
        <th>Horário</th>
        <th>Professor</th>
        <th>Vagas Restantes</th>
        <th>Ação</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()):
        $id_aula = $row['id'];
        $vagas = $row['vagas'];
        $inscritos = $row['inscritos'];
        $vagas_restantes = $vagas - $inscritos;

        // Verifica inscrição do aluno
        $check = $conn->query("SELECT id FROM presencas WHERE id_aula = $id_aula AND id_aluno = $id_aluno");
        $inscrito = $check->num_rows > 0;
    ?>
    <tr>
        <td><?= htmlspecialchars($row['nome_parque']) ?></td>
        <td><?= htmlspecialchars($row['endereco_parque']) ?></td>
        <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
        <td><?= htmlspecialchars(substr($row['horario'], 0, 5)) ?></td>
        <td><?= htmlspecialchars($row['nome_professor']) ?></td>
        <td style="text-align:center;"><?= $vagas_restantes ?></td>
        <td>
            <?php if ($inscrito): ?>
                <form method="post" action="cancelar_aula.php" onsubmit="return confirm('Tem certeza que quer cancelar a inscrição?');">
                    <input type="hidden" name="id_aula" value="<?= $id_aula ?>">
                    <button type="submit" style="background-color:#f44336;color:#fff;border:none;padding:5px 10px;cursor:pointer;">Cancelar</button>
                </form>
            <?php elseif ($vagas_restantes > 0): ?>
                <form method="post" action="confirmar_participacao.php">
                    <input type="hidden" name="id_aula" value="<?= $id_aula ?>">
                    <button type="submit">Participar</button>
                </form>
            <?php else: ?>
                <em>Esgotado</em>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>