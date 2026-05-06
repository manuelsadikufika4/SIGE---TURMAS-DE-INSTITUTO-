<?php
session_start();
require_once 'config.php';

// Segurança: Apenas coordenador acessa
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

// Verifica se o ID da turma foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ver_turmas.php");
    exit();
}

$id_turma = $_GET['id'];

try {
    // 1. Busca o nome da turma para o título
    $stmtTurma = $pdo->prepare("SELECT nome_turma FROM turmas WHERE id = ?");
    $stmtTurma->execute([$id_turma]);
    $dadosTurma = $stmtTurma->fetch(PDO::FETCH_ASSOC);

    if (!$dadosTurma) {
        die("Turma não encontrada.");
    }

    // 2. Busca todos os usuários vinculados a essa turma
    $sql = "SELECT id, nome_completo, nomeUsuario, cargo, numeroInterno 
            FROM usuarios 
            WHERE id_turma = :id_t 
            ORDER BY cargo DESC, nome_completo ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_t', $id_turma, PDO::PARAM_INT);
    $stmt->execute();
    $integrantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Integrantes da Turma</title>
</head>
<body>

    <a href="ver_turmas.php">← Voltar para Turmas</a>

    <h1>Membros da Turma: <?php echo htmlspecialchars($dadosTurma['nome_turma']); ?></h1>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome Completo</th>
                <th>Usuário (Login)</th>
                <th>Cargo</th>
                <th>Nº Interno</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($integrantes) > 0): ?>
                <?php foreach ($integrantes as $pessoa): ?>
                    <tr>
                        <td><?php echo $pessoa['id']; ?></td>
                        <td><?php echo htmlspecialchars($pessoa['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($pessoa['nomeUsuario']); ?></td>
                        <td>
                            <strong><?php echo ($pessoa['cargo'] == 'delegado') ? 'DELEGADO' : 'Aluno'; ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($pessoa['numeroInterno']); ?></td>
                        <td>
                            <a href="editar_aluno.php?id=<?php echo $pessoa['id']; ?>">Editar</a> | 
                            <a href="excluir_usuario.php?id=<?php echo $pessoa['id']; ?>" onclick="return confirm('Excluir este usuário?')">Remover</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Nenhum aluno ou delegado cadastrado nesta turma.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>