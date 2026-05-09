<?php
session_start();
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'professor') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Professores</title>
</head>

<body>
    <h1>Bem-vindo, Professor(a)!</h1>
    <p>Olá, <?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?>.</p>
    <p>Gerencie suas turmas, notas e frequências.</p>
    <ul>
        <li>Lançar notas</li>
        <li>Registrar faltas</li>
    </ul>
    <p><a href="logout.php">Sair</a></p>
</body>

</html>