<?php
session_start();
if (isset($_SESSION['user_cargo'])) {
    $cargo = $_SESSION['user_cargo'];
    switch ($cargo) {
        case 'delegado': header('Location: delegado.php'); break;
        case 'professor': header('Location: professores.php'); break;
        case 'alunoComun': header('Location: alunoComun.php'); break;
        case 'coordenador': header('Location: coordenador.php'); break;
        default: session_destroy(); header('Location: login.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Sistema da Turma</title>
</head>

<body>
    <h1>Login do Sistema</h1>
    <?php if (isset($_GET['erro'])): ?>
    <p style="color: red;">Usuário ou senha inválidos!</p>
    <?php endif; ?>
    <form method="post" action="funcoes/autenticacao.php">
        <label>Usuário:</label><br>
        <input type="text" name="txtnomeUsuario" required><br><br>
        <label>Senha:</label><br>
        <input type="password" name="txtsenha" required><br><br>
        <button type="submit">Entrar</button>
    </form>
</body>

</html>