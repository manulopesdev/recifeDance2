<?php
session_start();
echo "Bem-vindo, " . $_SESSION["user_nome"] . "!";
echo "<br><a href='logout.php'>Sair</a>";


echo "<br><br><a href='aulas/listar_aulas.php'>Me juntar a uma aula</a>";
echo "<br><a href='parques/parques.php'>Conheça os parques</a>";
echo "<br><a href='aulas/minhas_aulas.php'>Veja suas aulas</a>";
