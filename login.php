<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $tipo = $_POST["tipo"];

    $tabela = $tipo == "aluno" ? "alunos_login" : "professores_login";

    $sql = $conn->prepare("SELECT id, nome, senha FROM $tabela WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $res = $sql->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($senha, $user["senha"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_nome"] = $user["nome"];
            $_SESSION["user_tipo"] = $tipo;

            header("Location: painel_" . $tipo . ".php");
            exit;
        }
    }

    echo "❌ Login inválido!";
}
?>

<h2>Login</h2>
<form method="POST">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Senha: <input type="password" name="senha" required></label><br>
    <label>Tipo:
        <select name="tipo" required>
            <option value="aluno">Aluno</option>
            <option value="professor">Professor</option>
        </select>
    </label><br>
    <button type="submit">Entrar</button>
</form>

<p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
