<?php
session_start();
require_once 'config.php';

// Segurança: Apenas coordenador
if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] !== 'coordenador') {
    // die("Acesso negado."); 
}

// Lógica para salvar o vínculo completo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vincular'])) {
    $id_professor  = $_POST['id_professor'];
    $id_turma      = $_POST['id_turma'];
    $id_disciplina = $_POST['id_disciplina'];
    $turno         = $_POST['turno'];
    $classe        = $_POST['classe'];

    try {
        $sql = "INSERT INTO professor_turma (id_professor, id_turma, id_disciplina, turno, classe) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_professor, $id_turma, $id_disciplina, $turno, $classe]);
        echo "<script>alert('Vínculo realizado com sucesso!');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Erro ao vincular: " . addslashes($e->getMessage()) . "');</script>";
    }
}

try {
    $professores = $pdo->query("SELECT id, nome_completo, nomeUsuario FROM usuarios WHERE LOWER(cargo) = 'professor' ORDER BY nome_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
    $turmas = $pdo->query("SELECT id, nome_turma FROM turmas ORDER BY nome_turma ASC")->fetchAll(PDO::FETCH_ASSOC);
    $disciplinas = $pdo->query("SELECT * FROM disciplinas ORDER BY nome_disciplina ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro no banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Atribuição de Aulas</title>
</head>
<body>
    <h1>Vincular Professor (Grade Curricular)</h1>
    
    <p>
        <a href="coordenador.php">Voltar</a> | 
        <a href="cadastrar_disciplina.php">Cadastrar Disciplinas</a>
    </p>

    <form method="POST">
        <div>
            <label>Professor:</label><br>
            <select name="id_professor" required>
                <option value="">-- Selecione --</option>
                <?php foreach ($professores as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['nome_completo'] ?: $p['nomeUsuario']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Turma:</label><br>
            <select name="id_turma" required>
                <option value="">-- Selecione --</option>
                <?php foreach ($turmas as $t): ?>
                    <option value="<?= $t['id'] ?>">
                        <?= htmlspecialchars($t['nome_turma']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Disciplina:</label><br>
            <select name="id_disciplina" required>
                <option value="">-- Selecione --</option>
                <?php foreach ($disciplinas as $d): ?>
                    <option value="<?= $d['id'] ?>">
                        <?= htmlspecialchars($d['nome_disciplina']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Turno:</label><br>
            <select name="turno" required>
                <option value="Manhã">Manhã</option>
                <option value="Tarde">Tarde</option>
                <option value="Noite">Noite</option>
            </select>
        </div>

        <br>

        <div>
            <label>Classe/Nível:</label><br>
            <input type="text" name="classe" placeholder="Ex: 10ª Classe">
        </div>

        <br>

        <button type="submit" name="vincular">Salvar Vínculo</button>
    </form>

    <hr>

    <h3>Grade de Professores Ativos</h3>
    
    <table border="1">
        <thead>
            <tr>
                <th>Professor</th>
                <th>Disciplina</th>
                <th>Turma</th>
                <th>Classe</th>
                <th>Turno</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sqlV = "SELECT pt.id, u.nome_completo, u.nomeUsuario, t.nome_turma, d.nome_disciplina, pt.turno, pt.classe 
                     FROM professor_turma pt
                     JOIN usuarios u ON pt.id_professor = u.id
                     JOIN turmas t ON pt.id_turma = t.id
                     JOIN disciplinas d ON pt.id_disciplina = d.id";
            $vinculos = $pdo->query($sqlV)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($vinculos as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['nome_completo'] ?: $v['nomeUsuario']) ?></td>
                    <td><?= htmlspecialchars($v['nome_disciplina']) ?></td>
                    <td><?= htmlspecialchars($v['nome_turma']) ?></td>
                    <td><?= htmlspecialchars($v['classe']) ?></td>
                    <td><?= htmlspecialchars($v['turno']) ?></td>
                    <td>
                        <a href="remover_vinculo.php?id=<?= $v['id'] ?>" onclick="return confirm('Remover?')">Remover</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>