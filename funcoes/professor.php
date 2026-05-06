<?php
session_start();
require_once 'config.php';

// Verifica se o cargo na sessão é 'professor'
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'professor') {
    header('Location: login.php');
    exit();
}

$id_professor = $_SESSION['user_id'];

try {
    // Busca as turmas e a disciplina específica que o professor leciona nelas
    $stmt = $pdo->prepare("
        SELECT t.id as id_turma, t.nome_turma, d.nome_disciplina 
        FROM professor_turma pt
        INNER JOIN turmas t ON pt.id_turma = t.id
        INNER JOIN disciplinas d ON pt.id_disciplina = d.id
        WHERE pt.id_professor = ?
        ORDER BY t.nome_turma ASC
    ");
    $stmt->execute([$id_professor]);
    $minhas_turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar turmas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Professor</title>
</head>
<body>

    <div style="text-align: right;">
        <strong>Professor(a): <?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?></strong> | 
        <a href="perfil.php">Meu Perfil (Alterar Dados)</a> | 
        <a href="logout.php">Sair</a>
    </div>

    <h1>Painel do Professor</h1>
    
    <hr>

    <h3>Minhas Turmas e Disciplinas</h3>
    
    <?php if (count($minhas_turmas) > 0): ?>
        <table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>Turma</th>
                    <th>Disciplina</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($minhas_turmas as $linha): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($linha['nome_turma']); ?></strong></td>
                        <td><?php echo htmlspecialchars($linha['nome_disciplina']); ?></td>
                        <td>
                            <a href="ver_horario.php?id_turma=<?php echo $linha['id_turma']; ?>">Ver Horário</a> | 
                            <a href="lista_turma.php?id_turma=<?php echo $linha['id_turma']; ?>">Lista de Alunos</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Você ainda não está vinculado a nenhuma turma ou disciplina.</p>
    <?php endif; ?>

    <hr>
    
    <p>
        <a href="mensagem.php">Acessar Mensagens</a>
    </p>

</body>
</html>