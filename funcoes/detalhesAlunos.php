<?php
session_start();
require_once 'config.php';

// 1. Verificação de segurança (Só o coordenador acessa esta página)
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

// 2. Pegar os dados via GET (Enviados pelo link do painel anterior)
$id_delegado_envio = $_GET['delegado_id'] ?? null; 
$turma = $_GET['turma'] ?? null;
$data = $_GET['data'] ?? null;

if (!$id_delegado_envio || !$turma || !$data) {
    die("Informações insuficientes para gerar o relatório.");
}

// 3. Buscar o nome do Delegado que fez o envio
$stmtDel = $pdo->prepare("SELECT nome_completo FROM usuarios WHERE id = ?");
$stmtDel->execute([$id_delegado_envio]);
$delegado = $stmtDel->fetch();

// 4. Buscar os alunos daquela TURMA que tiveram presença no DIA selecionado
$query = "
    SELECT u.nome_completo, u.numeroInterno, f.data_registro 
    FROM frequencia f
    INNER JOIN usuarios u ON f.aluno_id = u.id
    WHERE u.turma = ? 
    AND DATE(f.data_registro) = ?
    ORDER BY u.nome_completo ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$turma, $data]);
$alunos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Presença - Coordenação</title>
</head>
<body>

    <h2>Relatório de Chamada</h2>
    <p><strong>Delegado Responsável:</strong> <?php echo htmlspecialchars($delegado['nome_completo'] ?? 'Não encontrado'); ?></p>
    <p><strong>Turma:</strong> <?php echo htmlspecialchars($turma); ?></p>
    <p><strong>Data da Chamada:</strong> <?php echo date('d/m/Y', strtotime($data)); ?></p>

    <hr>

    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr style="background-color: #eee;">
                <th>Nº Interno</th>
                <th>Nome do Aluno</th>
                <th>Hora do Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($alunos) > 0): ?>
                <?php foreach ($alunos as $a): ?>
                <tr>
                    <td><?php echo htmlspecialchars($a['numeroInterno']); ?></td>
                    <td><?php echo htmlspecialchars($a['nome_completo']); ?></td>
                    <td><?php echo date('H:i:s', strtotime($a['data_registro'])); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhum registro de presença encontrado para esta turma nesta data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="coordenador.php">Voltar ao Painel Principal</a>
    <button onclick="window.print()">Imprimir Relatório</button>

</body>
</html>