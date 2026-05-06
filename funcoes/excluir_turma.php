<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

if (isset($_GET['id'])) {
    $id_turma = $_GET['id'];

    try {
        // Verifica se existem alunos vinculados a esta turma antes de excluir
        $check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id_turma = ?");
        $check->execute([$id_turma]);
        
        if ($check->fetchColumn() > 0) {
            die("Erro: Não é possível excluir uma turma que ainda possui alunos cadastrados.");
        }

        $sql = "DELETE FROM turmas WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_turma]);

        header("Location: ver_turmas.php?msg=excluido");
        exit();
    } catch (PDOException $e) {
        die("Erro ao excluir: " . $e->getMessage());
    }
}