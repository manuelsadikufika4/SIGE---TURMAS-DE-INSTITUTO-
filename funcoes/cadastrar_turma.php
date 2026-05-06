<?php
session_start();
require_once 'config.php';

// Segurança
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_turma = trim($_POST['nome_turma']);

    if (!empty($nome_turma)) {
        try {
            // Insere a nova turma
            $sql = "INSERT INTO turmas (nome_turma) VALUES (?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$nome_turma])) {
                // Redireciona de volta para a lista com mensagem de sucesso
                header("Location: ver_turmas.php?msg=sucesso");
                exit();
            }
        } catch (PDOException $e) {
            die("Erro ao salvar no banco: " . $e->getMessage());
        }
    } else {
        header("Location: cadastrar_turma.php?msg=vazio");
        exit();
    }
}