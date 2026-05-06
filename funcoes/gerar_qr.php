<?php
require_once 'config.php';
require_once('../phpqrcode/phpqrcode.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID inválido.');
}

$id_aluno = (int)$_GET['id'];

// Buscar dados do aluno (apenas para garantir que existe)
$stmt = $pdo->prepare("SELECT id, nomeUsuario FROM usuarios WHERE id = ? AND cargo = 'alunoComun'");
$stmt->execute([$id_aluno]);
$aluno = $stmt->fetch();

if (!$aluno) {
    die('Aluno não encontrado.');
}

// Montar a URL completa do perfil do aluno
// Altere o domínio e caminho conforme seu ambiente
$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . $host . dirname($_SERVER['SCRIPT_NAME']);
$perfil_url = $base_url . '/perfilAluno.php?id=' . $id_aluno;

// Gerar QR Code com a URL
// Parâmetros: texto, arquivo de saída (null para enviar diretamente), nível de correção de erro, tamanho do pixel, margem
QRcode::png($perfil_url, false, QR_ECLEVEL_L, 6, 2);
exit;
?>