<?php
session_start();
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Coordenador</title>
</head>

<body>
    <h1>Bem-vindo, Coordenador(a)!</h1>
    <p>Olá, <?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?>.</p>
    <p>Painel de coordenação: gerencie turmas, professores e alunos.</p>
    <ul>
        <li>Cadastrar disciplinas</li>
        <li>Atribuir professores</li>
    </ul>
    <p><a href="logout.php">Sair</a></p>
</body>

</html>