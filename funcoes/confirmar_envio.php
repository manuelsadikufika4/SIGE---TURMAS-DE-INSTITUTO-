<?php
session_start();
require_once 'config.php';

// 1. Identificação precisa do erro de sessão
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_turma'])) {
    // Debug amigável para você saber o que está faltando
    $erro = !isset($_SESSION['user_id']) ? "ID do usuário" : "Turma";
    die("Erro: O campo [ $erro ] não foi encontrado na sessão. <a href='../login.php'>Faça login novamente</a>");
}

$id_delegado = $_SESSION['user_id'];
$turma = $_SESSION['user_turma'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // 2. Inserção com tratamento de erro
        $stmt = $pdo->prepare("INSERT INTO relatorios_entregues (delegado_id, turma, data_envio) VALUES (?, ?, NOW())");
        
        if ($stmt->execute([$id_delegado, $turma])) {
            echo "<h2>✅ Relatório Enviado!</h2>";
            echo "A lista da turma <strong>$turma</strong> foi enviada ao coordenador.<br>";
            echo "<a href='delegado.php'>Voltar ao Painel</a>";
        }
    } catch (PDOException $e) {
        // Caso a tabela relatorios_entregues ainda não exista
        echo "Erro Crítico: " . $e->getMessage();
    }
} else {
    // Se tentarem acessar a página direto pela URL sem ser via POST do botão
    header("Location: delegado.php");
    exit();
}