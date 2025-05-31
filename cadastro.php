<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    $tipo = $_POST["tipo"];
    $telefone = $_POST["telefone"];

    if ($tipo == "aluno") {
        $sql = $conn->prepare("INSERT INTO alunos_login (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
    } else {
        $sql = $conn->prepare("INSERT INTO professores_login (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
    }

    $sql->bind_param("ssss", $nome, $email, $telefone, $senha);

    if ($sql->execute()) {
        echo "✅ Cadastro realizado! <a href='login.php'>Fazer login</a>";
        exit;
    } else {
        echo "❌ Erro: " . $conn->error;
    }
}
?>

<h2>Cadastro</h2>
<form method="POST">
    <label>Nome: <input type="text" name="nome" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Telefone: <input type="text" name="telefone" required></label><br>
    <label>Senha: <input type="password" name="senha" required></label><br>
    <label>Tipo:
        <select name="tipo" required>
            <option value="aluno">Aluno</option>
            <option value="professor">Professor</option>
        </select>
    </label><br>
    <button type="submit">Cadastrar</button>
</form>
