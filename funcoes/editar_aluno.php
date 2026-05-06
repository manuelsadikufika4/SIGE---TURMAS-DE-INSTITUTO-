<?php
session_start();
require_once 'config.php';

// Segurança: Apenas coordenador acessa
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    header("Location: ver_turmas.php");
    exit();
}

$id_usuario = $_GET['id'];

// 1. Processar a atualização quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeCompleto = $_POST['nome_completo'];
    $nomeUsuario  = $_POST['nomeUsuario'];
    $numero       = $_POST['numeroInterno'];
    $id_turma     = $_POST['id_turma'];
    $cargo        = $_POST['cargo'];

    try {
        $sql = "UPDATE usuarios SET nome_completo = ?, nomeUsuario = ?, numeroInterno = ?, id_turma = ?, cargo = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomeCompleto, $nomeUsuario, $numero, $id_turma, $cargo, $id_usuario]);
        
        $sucesso = "Dados atualizados com sucesso!";
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}

// 2. Buscar os dados atuais do membro
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuário não encontrado.");
    }

    // Buscar turmas para o select
    $stmtTurmas = $pdo->query("SELECT id, nome_turma FROM turmas ORDER BY nome_turma ASC");
    $listaTurmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro no banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Membro</title>
</head>
<body>

    <h2>Editar Membro da Turma</h2>
    <a href="ver_alunos_turmas.php?id=<?php echo $usuario['id_turma']; ?>">Voltar para a Lista</a>
    <br><br>

    <?php if(isset($sucesso)) echo "<p style='color:green'>$sucesso</p>"; ?>
    <?php if(isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>

    <form method="POST">
        <label>Nome Completo:</label><br>
        <input type="text" name="nome_completo" value="<?php echo htmlspecialchars($usuario['nome_completo']); ?>" required><br><br>

        <label>Nome de Usuário (Login):</label><br>
        <input type="text" name="nomeUsuario" value="<?php echo htmlspecialchars($usuario['nomeUsuario']); ?>" required><br><br>

        <label>Número Interno:</label><br>
        <input type="text" name="numeroInterno" value="<?php echo htmlspecialchars($usuario['numeroInterno']); ?>" required><br><br>

        <label>Turma:</label><br>
        <select name="id_turma" required>
            <?php foreach($listaTurmas as $t): ?>
                <option value="<?php echo $t['id']; ?>" <?php echo ($t['id'] == $usuario['id_turma']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($t['nome_turma']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Cargo:</label><br>
        <select name="cargo" required>
            <option value="alunoComun" <?php echo ($usuario['cargo'] == 'alunoComun') ? 'selected' : ''; ?>>Aluno Comum</option>
            <option value="delegado" <?php echo ($usuario['cargo'] == 'delegado') ? 'selected' : ''; ?>>Delegado</option>
        </select><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>

</body>
</html>