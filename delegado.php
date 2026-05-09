<?php
session_start();
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'delegado') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Delegado da Turma</title>
</head>

<body>
    <h1>Bem-vindo, Delegado da Turma!</h1>
    <p>Olá, <?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?>.</p>
    <p>Área exclusiva para representante de turma.</p>
    <ul>
        <li>Ver lista de alunos</li>
        <li>Registrar avisos</li>
        <li>Solicitar reunião com coordenador</li>
    </ul>
    <p><a href="logout.php">Sair</a></p>
</body>

</html>