<?php
session_start();
echo "Bem-vindo, professor(a) " . $_SESSION["user_nome"] . "!";
echo "<br><a href='logout.php'>Sair</a>";


echo "<br><br><a href='aulas/cadastrar.php'>Cadastrar aula</a>";
echo "<br><a href='relatorios/relatorio_aulas.php'>Relatório de aulas</a>";