<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
$stmt = $pdo->prepare("INSERT INTO frequencia (aluno_id, data_registro) VALUES (?, NOW())");
    
    try {
        if ($stmt->execute([$id])) {
            header("Location: perfilAluno.php?id=$id&status=sucesso");
            exit; 
        }
        else {
            echo "Erro ao registrar presença.";
        }
    } catch (PDOException $e) {
         echo "Erro no banco de dados: " . $e->getMessage();
    }
} else {
    echo "ID do aluno não fornecido.";
}