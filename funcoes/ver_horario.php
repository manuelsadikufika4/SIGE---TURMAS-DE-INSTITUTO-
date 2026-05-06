<?php
session_start();
require_once 'config.php';

$cargo = $_SESSION['user_cargo'] ?? ''; 
$id_usuario_logado = $_SESSION['user_id'] ?? null;
$id_turma_alvo = null;

// 1. Definição da Turma Alvo por Cargo
if ($cargo === 'coordenador') {
    $id_turma_alvo = $_GET['id_turma'] ?? null;
} elseif ($cargo === 'professor') {
    $id_tentativa = $_GET['id_turma'] ?? null;
    $stmtCheck = $pdo->prepare("SELECT 1 FROM professor_turma WHERE id_professor = ? AND id_turma = ?");
    $stmtCheck->execute([$id_usuario_logado, $id_tentativa]);
    if ($stmtCheck->fetch()) {
        $id_turma_alvo = $id_tentativa;
    } else {
        die("Erro: Você não leciona nesta turma.");
    }
} else {
    $turma_nome = $_SESSION['user_turma'] ?? '';
    $stmtT = $pdo->prepare("SELECT id FROM turmas WHERE nome_turma = ?");
    $stmtT->execute([$turma_nome]);
    $res = $stmtT->fetch();
    $id_turma_alvo = $res['id'] ?? null;
}

if (!$id_turma_alvo) die("Erro: Turma não identificada.");

// 2. Busca do Horário com JOIN para pegar o NOME da Disciplina
try {
    $stmtNome = $pdo->prepare("SELECT nome_turma FROM turmas WHERE id = ?");
    $stmtNome->execute([$id_turma_alvo]);
    $turma_dados = $stmtNome->fetch();

    // Importante: h.disciplina deve conter o ID que liga à tabela disciplinas
    $sql = "SELECT h.aula_numero, h.dia_semana, d.nome_disciplina 
            FROM horarios h
            INNER JOIN disciplinas d ON h.disciplina = d.id
            WHERE h.id_turma = ?
            ORDER BY h.aula_numero, FIELD(h.dia_semana, 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta')";
    
    $stmtH = $pdo->prepare($sql);
    $stmtH->execute([$id_turma_alvo]);
    $lista_horarios = $stmtH->fetchAll(PDO::FETCH_ASSOC);

    $grade = [];
    foreach ($lista_horarios as $item) {
        $grade[$item['aula_numero']][$item['dia_semana']] = $item['nome_disciplina'];
    }

    $dias = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

} catch (PDOException $e) {
    die("Erro no banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Horário Escolar</title>
</head>
<body>

    <h1>Grade de Horários: <?= htmlspecialchars($turma_dados['nome_turma']) ?></h1>
    <p><a href="javascript:history.back()">Voltar ao Painel</a></p>

    <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr style="background: #f0f0f0;">
                <th>Aula</th>
                <?php foreach ($dias as $dia): ?>
                    <th><?= $dia ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <tr>
                    <td style="background: #fafafa;"><strong><?= $i ?>ª Aula</strong></td>
                    <?php foreach ($dias as $dia): ?>
                        <td>
                            <?php 
                                // Mostra o NOME da disciplina se existir no horário
                                if (isset($grade[$i][$dia])) {
                                    echo "<strong>" . htmlspecialchars($grade[$i][$dia]) . "</strong>";
                                } else {
                                    echo "-";
                                }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

</body>
</html>