<?php
session_start();
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'alunoComun') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Aluno Comum</title>
</head>

<body>
    <h1>Bem-vindo, Aluno!</h1>
    <p>Olá, <?php echo htmlspecialchars($_SESSION['nomeUuario']); ?>.</p>
    <p>Consulte notas, horários e comunicados.</p>
    <ul>
        <li>Ver boletim</li>
        <li>Acessar calendário de provas</li>
    </ul>
    <p><a href="logout.php">Sair</a></p>
</body>

</html>