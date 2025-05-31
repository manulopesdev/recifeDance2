<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: ../index.php');
    exit;
}

$res = $conn->query("SELECT id, nome, endereco FROM parques WHERE ativo = TRUE ORDER BY nome");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Parques</title>
    <style>
        .card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 15px;
            border-radius: 10px;
            width: 250px;
            display: inline-block;
            vertical-align: top;
            text-align: left;
            box-shadow: 1px 1px 5px rgba(0,0,0,0.1);
        }
        .card a {
            text-decoration: none;
            color: #0077cc;
        }
    </style>
</head>
<body>
    <a href="../painel_aluno.php">← Voltar para o início</a><br><br>
    <h1>Parques com Aulas</h1>

    <?php while ($p = $res->fetch_assoc()): ?>
        <div class="card">
        <iframe
            width="100%"
            height="200"
            style="border:0; border-radius: 8px;"
            loading="lazy"
            allowfullscreen
            src="https://maps.google.com/maps?q=<?= urlencode($p['nome']) ?>&output=embed">
        </iframe>
            <h3><?= htmlspecialchars($p['nome']) ?></h3>
            <p><?= htmlspecialchars($p['endereco']) ?></p>
            <a href="../aulas/aulas_parque.php?id=<?= $p['id'] ?>">Ver aulas neste parque →</a>
        </div>
    <?php endwhile; ?>
</body>
</html>
