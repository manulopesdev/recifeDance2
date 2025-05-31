<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'professor') {
    header('Location: ../index.php');
    exit;
}

require '../includes/db.php';

// Buscar parques ativos
$parques = [];
$sql_parques = $conn->query("SELECT id, nome FROM parques WHERE ativo = 1 ORDER BY nome");
while ($row = $sql_parques->fetch_assoc()) {
    $parques[] = $row;
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parque_id = $_POST['parque'];
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $id_professor = $_SESSION['user_id'];

    // Inserir nova aula
    $stmt = $conn->prepare("INSERT INTO aulas (id_parque, data, horario, id_professor) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('issi', $parque_id, $data, $horario, $id_professor);

    if ($stmt->execute()) {
        $mensagem = "Aula cadastrada com sucesso!";
        // Aqui você pode redirecionar ou oferecer link para vincular alunos
    } else {
        $mensagem = "Erro ao cadastrar aula: " . $conn->error;
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Aula</title>
</head>
<body>
    <h2>Cadastrar Nova Aula</h2>
    <?php if (isset($mensagem)) echo "<p>$mensagem</p>"; ?>

    <form method="post">
        <label>Parque:</label><br>
        <select name="parque" required>
            <option value="">Selecione um parque</option>
            <?php foreach ($parques as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Data:</label><br>
        <input type="date" name="data" required><br><br>

        <label>Horário:</label><br>
        <input type="time" name="horario" required><br><br>


        <button type="submit">Cadastrar Aula</button>
    </form>

    <br><a href="../painel_professor.php">Voltar ao Dashboard</a>
</body>
</html>
