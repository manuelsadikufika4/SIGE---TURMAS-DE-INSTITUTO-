<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];

    try {
        // Opcional: Buscar o id_turma antes de deletar para poder voltar para a página da turma certa
        $stmtBusca = $pdo->prepare("SELECT id_turma FROM usuarios WHERE id = ?");
        $stmtBusca->execute([$id_usuario]);
        $usuario = $stmtBusca->fetch(PDO::FETCH_ASSOC);
        
        $id_retorno = $usuario ? $usuario['id_turma'] : null;

        // 3. Executa a exclusão
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id_usuario])) {
            // Redireciona de volta para a lista da turma de onde o aluno saiu
            if ($id_retorno) {
                header("Location: ver_alunos_turmas.php?id=" . $id_retorno . "&msg=sucesso");
            } else {
                header("Location: ver_turmas.php?msg=sucesso");
            }
            exit();
        }

    } catch (PDOException $e) {
        die("Erro ao excluir usuário: " . $e->getMessage());
    }
} else {
    header("Location: ver_turmas.php");
    exit();
}