<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'delegado') {
    die("Acesso negado.");
}

$turmas = $pdo->query("SELECT * FROM turmas ORDER BY nome_turma ASC")->fetchAll(PDO::FETCH_ASSOC);

$alunos = [];
$turma_selecionada = $_GET['id_turma'] ?? null;
if ($turma_selecionada) {
    $stmt = $pdo->prepare("SELECT id, nome_completo FROM usuarios WHERE id_turma = ? AND cargo = 'alunoComun' ORDER BY nome_completo ASC");
    $stmt->execute([$turma_selecionada]);
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];
    $motivo = $_POST['motivo'] ?? 'Conversa excessiva';

    try {
        $sql = "INSERT INTO lista_barulhentos (id_usuario, motivo) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$id_usuario, $motivo])) {
            $sucesso = "Ocorrência registrada com sucesso!";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao registrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Barulho</title>
</head>
<body>
    <h2>Registrar Aluno na Lista de Barulho</h2>
    <a href="delegado.php">Voltar</a><br><br>

    <?php if(isset($sucesso)) echo "<p style='color:green'>$sucesso</p>"; ?>
    <?php if(isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>

    <!-- Passo 1: Selecionar Turma -->
    <form method="GET" action="">
        <label>1. Selecione a Turma:</label>
        <select name="id_turma" onchange="this.form.submit()">
            <option value="">-- Escolha --</option>
            <?php foreach($turmas as $t): ?>
                <option value="<?php echo $t['id']; ?>" <?php echo ($turma_selecionada == $t['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($t['nome_turma']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <br>

    <!-- Passo 2: Selecionar Aluno e Motivo -->
    <?php if ($turma_selecionada): ?>
    <form method="POST" action="">
        <input type="hidden" name="id_usuario_turma" value="<?php echo $turma_selecionada; ?>">
        
        <label>2. Selecione o Aluno:</label><br>
        <select name="id_usuario" required>
            <option value="">-- Selecione o Aluno --</option>
            <?php foreach($alunos as $a): ?>
                <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['nome_completo']); ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Motivo/Observação:</label><br>
        <textarea name="motivo" rows="3" style="width: 300px;">Conversa excessiva durante a aula.</textarea>
        <br><br>

        <button type="submit">Adicionar à Lista</button>
    </form>
    <?php endif; ?>

</body>
</html>