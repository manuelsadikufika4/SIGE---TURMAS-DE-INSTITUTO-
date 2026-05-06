<?php
session_start();
require_once 'config.php';

// Segurança: Apenas o coordenador acessa
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

// 1. Buscar turmas para o menu de seleção
try {
    $stmtTurmas = $pdo->query("SELECT id, nome_turma FROM turmas ORDER BY nome_turma ASC");
    $listaTurmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar turmas: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeCompleto = $_POST['nome_completo']; 
    $nomeUsuario  = $_POST['nomeUsuario']; // O login do aluno
    $numero       = $_POST['numeroInterno'];
    $id_turma     = $_POST['id_turma']; 
    $senha        = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $cargo        = 'alunoComun';

    try {
        // SQL com nome_completo E nomeUsuario
        $sql = "INSERT INTO usuarios (nome_completo, nomeUsuario, numeroInterno, id_turma, senha, cargo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nomeCompleto, $nomeUsuario, $numero, $id_turma, $senha, $cargo])) {
            $sucesso = "O aluno <strong>$nomeCompleto</strong> foi cadastrado com o usuário <strong>$nomeUsuario</strong>!";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Aluno</title>
</head>
<body>
    <h2>Cadastrar Novo Aluno</h2>
    
    <?php if(isset($sucesso)) echo "<p style='color:green'>$sucesso</p>"; ?>
    <?php if(isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>

    <form method="POST">
        <label>Nome Completo:</label><br>
        <input type="text" name="nome_completo" placeholder="ex: Manuel Sadi Kufika" required style="width: 300px;"><br><br>

        <label>Nome de Usuário (Login):</label><br>
        <input type="text" name="nomeUsuario" placeholder="ex:  manuelsadiKufika" required><br><br>

        <label>Número Interno (QR Code):</label><br>
        <input type="text" name="numeroInterno" required><br><br>

        <label>Turma:</label><br>
        <select name="id_turma" required>
            <option value="">-- Selecione a Turma --</option>
            <?php foreach($listaTurmas as $t): ?>
                <option value="<?php echo $t['id']; ?>">
                    <?php echo htmlspecialchars($t['nome_turma']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Senha Inicial:</label><br>
        <input type="password" name="senha" required><br><br>

        <button type="submit">Finalizar Cadastro</button>
    </form>
    
    <br>
    <a href="coordenador.php">Voltar ao Painel</a>
</body>
</html>