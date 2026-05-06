<?php
session_start();
require_once 'config.php';

// 1. BLOQUEIO DE SEGURANÇA
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

// 2. BUSCAR DADOS PARA OS MENUS
try {
    $turmas = $pdo->query("SELECT id, nome_turma FROM turmas ORDER BY nome_turma ASC")->fetchAll(PDO::FETCH_ASSOC);
    $disciplinas = $pdo->query("SELECT id, nome_disciplina FROM disciplinas ORDER BY nome_disciplina ASC")->fetchAll(PDO::FETCH_ASSOC);
    $professores = $pdo->query("SELECT id, nomeUsuario FROM usuarios WHERE cargo = 'professor' ORDER BY nomeUsuario ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}

// 3. PROCESSAR O CADASTRO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_turma    = $_POST['id_turma'];
    $dia_semana  = $_POST['dia_semana'];
    $aula_numero = $_POST['aula_numero'];
    $id_disciplina = $_POST['id_disciplina'];
    $professor_nome = $_POST['professor_nome'];

    try {
        $pdo->beginTransaction();

        $sqlDelete = "DELETE FROM horarios WHERE id_turma = ? AND dia_semana = ? AND aula_numero = ?";
        $pdo->prepare($sqlDelete)->execute([$id_turma, $dia_semana, $aula_numero]);

        $sql = "INSERT INTO horarios (id_turma, dia_semana, aula_numero, disciplina, professor_nome) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id_turma, $dia_semana, $aula_numero, $id_disciplina, $professor_nome])) {
            $pdo->commit();
            $sucesso = "Horário atualizado com sucesso!";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Horário</title>
</head>
<body>

    <h2>Gerenciar Horário</h2>

    <?php if(isset($sucesso)) echo "<p><strong>$sucesso</strong></p>"; ?>
    <?php if(isset($erro)) echo "<p><strong>$erro</strong></p>"; ?>

    <form method="POST">
        <div>
            <label>Turma:</label><br>
            <select name="id_turma" required>
                <option value="">Selecione...</option>
                <?php foreach($turmas as $t): ?>
                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['nome_turma']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Dia da Semana:</label><br>
            <select name="dia_semana" required>
                <option value="Segunda">Segunda-feira</option>
                <option value="Terça">Terça-feira</option>
                <option value="Quarta">Quarta-feira</option>
                <option value="Quinta">Quinta-feira</option>
                <option value="Sexta">Sexta-feira</option>
            </select>
        </div>

        <br>

        <div>
            <label>Horário (Aula):</label><br>
            <select name="aula_numero" required>
                <?php for($i=1; $i<=6; $i++) echo "<option value='$i'>{$i}ª Aula</option>"; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Disciplina:</label><br>
            <select name="id_disciplina" required>
                <option value="">Selecione...</option>
                <?php foreach($disciplinas as $d): ?>
                    <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['nome_disciplina']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Professor:</label><br>
            <select name="professor_nome" required>
                <option value="">Selecione...</option>
                <?php foreach($professores as $p): ?>
                    <option value="<?php echo htmlspecialchars($p['nomeUsuario']); ?>"><?php echo htmlspecialchars($p['nomeUsuario']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <button type="submit">Salvar Horário</button>
    </form>

    <br>
    <a href="coordenador.php">Voltar ao Painel</a>

</body>
</html>