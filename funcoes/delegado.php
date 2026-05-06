<?php
session_start();
require_once 'config.php';

// Verifica se o cargo é delegado
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'delegado') {
    header('Location: login.php');
    exit();
}

// Garante que o ID do delegado esteja na sessão para os processos de QR Code/Relatórios
$_SESSION['delegado_id'] = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Delegado</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>

    <div style="text-align: right;">
        <strong><?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?> (Delegado)</strong> | 
        <a href="perfil.php">Meu Perfil (Alterar Dados)</a> | 
        <a href="logout.php">Sair</a>
    </div>

    <h1>Painel do Delegado</h1>
    <p>Bem-vindo ao centro de controle de turma.</p>

    <hr>

    <h3>Leitura de QR Code (Chamada)</h3>
    <div id="reader" style="width: 300px; border: 1px solid #ccc;"></div>
    <div id="status"></div>
    <br>
    <button onclick="iniciarCamera()">Abrir Câmera para Scan</button>

    <hr>

    <h3>Ações e Consultas</h3>
    <ul>
        <li><a href="lista_turma.php">Ver Lista da Turma</a></li>
        <br>
        <li><a href="ver_horario.php">Ver Horário da Turma</a></li>
        <br>
        <li><a href="lista_presenca.php">Ver Lista de Presença</a></li>
        <br>
        <li><a href="registrar_barulho.php">Criar Lista dos Barulhentos</a></li>
        <br>
        <li><a href="mensagem.php">Mensagens</a></li>
    </ul>

    <script>
        function onScanSuccess(decodedText) {
            // O QR Code deve conter a URL de registro, ex: registrar_presenca.php?id=123
            window.location.href = decodedText;
        }

        function iniciarCamera() {
            let scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
            scanner.render(onScanSuccess);
        }
    </script>

</body>
</html>