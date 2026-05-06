<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $nome = trim($_POST['nome_disciplina']);
    if (!empty($nome)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO disciplinas (nome_disciplina) VALUES (?)");
            $stmt->execute([$nome]);
            echo "<script>alert('Disciplina cadastrada!');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Erro: Esta disciplina já existe.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Cadastrar Disciplinas</title></head>
<body>
    <h2>Gerenciar Disciplinas</h2>
    <form method="POST">
        <input type="text" name="nome_disciplina" placeholder="Ex: Matemática" required>
        <button type="submit" name="cadastrar">Salvar</button>
    </form>
    <br><a href="vincular_professor.php">Voltar para Vínculos</a>
</body>
</html>