<?php
session_start();
require_once 'config.php';

// Segurança: Apenas o coordenador
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

try {
    // Removi 't.horario' daqui para resolver o erro SQL
    $sql = "SELECT t.id, t.nome_turma, COUNT(u.id) as total_alunos 
            FROM turmas t 
            LEFT JOIN usuarios u ON t.id = u.id_turma 
            GROUP BY t.id, t.nome_turma 
            ORDER BY t.nome_turma ASC";
    
    $stmt = $pdo->query($sql);
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar os dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Turmas</title>
</head>
<body>

    <p><a href="coordenador.php">Voltar ao Painel</a></p>

    <h1>Gerenciamento de Turmas</h1>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome da Turma</th>
                <th>Qtd. Integrantes</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($turmas) > 0): ?>
                <?php foreach ($turmas as $turma): ?>
                    <tr>
                        <td><?php echo $turma['id']; ?></td>
                        <td><?php echo htmlspecialchars($turma['nome_turma']); ?></td>
                        <td><?php echo $turma['total_alunos']; ?> aluno(s)</td>
                        <td>
                            <a href="ver_horario.php?id_turma=<?php echo $turma['id']; ?>">Ver Horário</a> | 
                            <a href="ver_alunos_turmas.php?id=<?php echo $turma['id']; ?>">Ver Alunos</a> | 
                            <a href="excluir_turma.php?id=<?php echo $turma['id']; ?>" onclick="return confirm('Excluir turma?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhuma turma cadastrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br><hr>

    <h3>Cadastrar Nova Turma</h3>
    <form action="cadastrar_turma.php" method="POST">
        <label>Nome da Turma:</label><br>
        <input type="text" name="nome_turma" placeholder="Ex: IG12A25" required>
        <br><br>
        <button type="submit">Cadastrar Turma</button>
    </form>

</body>
</html>