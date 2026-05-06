<?php
session_start();
require_once 'config.php';

// Verifica se o cargo é alunoComun ou delegado (ambos costumam usar este painel)
$cargos_permitidos = ['alunoComun', 'delegado'];

if (!isset($_SESSION['user_cargo']) || !in_array($_SESSION['user_cargo'], $cargos_permitidos)) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel do Aluno</title>
</head>

<body>
    <div style="text-align: right;">
        <strong><?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?></strong> | 
        <a href="perfil.php">Meu Perfil (Alterar Dados)</a> | 
        <a href="logout.php">Sair</a>
    </div>

    <h1>Bem-vindo, Aluno!</h1>
    <p>Olá, <?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?>.</p>
    <p>Consulte suas informações acadêmicas abaixo:</p>
    <a href="gerar_qr.php?id=<?php echo $_SESSION['user_id']; ?>" target="_blank">
    <button type="button">Gerar meu QR Code</button>
</a>
    <hr>

    <ul>
        <li><a href="ver_horario.php">Ver Horário da Turma</a></li>
        <br>
        <li><a href="lista_turma.php">Ver Lista de Colegas / Presença</a></li>
        <br>
        <li><a href="mensagem.php">Mensagens</a></li>
    </ul>

    <hr>
    
    <p>Educação é o caminho para o sucesso.</p>

</body>
</html>