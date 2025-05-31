<?php
// Conecta ao MySQL (sem banco selecionado ainda)
$conn = new mysqli("localhost", "root", "");

// Verifica conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verifica se o banco já existe
$db_check = $conn->query("SHOW DATABASES LIKE 'recife_dance'");
if ($db_check->num_rows == 0) {
    // Cria o banco de dados se não existir
    $sql = "CREATE DATABASE recife_dance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql) === TRUE) {
        echo "Banco de dados 'recife_dance' criado com sucesso.<br>";
        echo "<br><br><a href='index.php'>crie as tabelas aqui</a>";
    } else {
        die("Erro ao criar banco: " . $conn->error);
    }
} else {
    echo "Banco de dados 'recife_dance' já existe.<br>";

    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "recife_dance";

    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        die("Erro na conexão com o banco: " . $conn->connect_error);
    } else {
        // Tabela de login para professores
        $conn->query("CREATE TABLE IF NOT EXISTS professores_login (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            telefone VARCHAR(15) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            senha VARCHAR(255) NOT NULL,
            motivo VARCHAR (255)
        )") ? print("✅ Tabela 'professores_login' OK.<br>") : die("Erro: " . $conn->error);

        // Tabela de login para alunos
        $conn->query("CREATE TABLE IF NOT EXISTS alunos_login (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            telefone VARCHAR(15) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            senha VARCHAR(255) NOT NULL,
            motivo VARCHAR (255)
        )") ? print("✅ Tabela 'alunos_login' OK.<br>") : die("Erro: " . $conn->error);

        // Tabela de parques
        $conn->query("CREATE TABLE IF NOT EXISTS parques (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL UNIQUE,
            endereco VARCHAR(250) NOT NULL,
            ativo BOOLEAN DEFAULT TRUE
        )") ? print("✅ Tabela 'parques' OK.<br>") : die("Erro: " . $conn->error);
        
        // Tabela de cadastro de aulas
        $conn->query("CREATE TABLE IF NOT EXISTS aulas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_parque INT NOT NULL,
            data DATE NOT NULL,
            horario TIME NOT NULL,
            id_professor INT NOT NULL,
            vagas INT NOT NULL DEFAULT 10,
            FOREIGN KEY (id_parque) REFERENCES parques(id),
            FOREIGN KEY (id_professor) REFERENCES professores_login(id)
        )") ? print("✅ Tabela 'aulas' OK.<br>") : die("Erro: " . $conn->error);

        // Tabela de presenças
        $conn->query("CREATE TABLE IF NOT EXISTS presencas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_aula INT,
            id_aluno INT,
            confirmado BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (id_aula) REFERENCES aulas(id),
            FOREIGN KEY (id_aluno) REFERENCES alunos_login(id)
        )") ? print("✅ Tabela 'presenças' OK.<br>") : die("Erro: " . $conn->error);

        // Inserir parques
        $conn->query("INSERT IGNORE INTO parques (nome, endereco, ativo) VALUES 
            ('Parque da Jaqueira', 'Rua do Futuro, s/n - Jaqueira, Recife - PE', TRUE),
            ('Parque 13 de Maio', 'Av. Rui Barbosa, s/n - Boa Vista, Recife - PE', TRUE),
            ('Parque Santana', 'Av. Santana, s/n - Santana, Recife - PE', TRUE),
            ('Parque Dona Lindu', 'Av. Boa Viagem, s/n - Boa Viagem, Recife - PE', TRUE),
            ('Parque do Caiara', 'Av. Professor José dos Anjos, s/n - Cordeiro, Recife - PE', TRUE)
        ") ? print("✅ INSERT 'parques' OK.<br>") : die("Erro: " . $conn->error);

        echo "<br><br><a href='login.php'>faça login ou cadastre-se aqui</a>";

    }
}






?>