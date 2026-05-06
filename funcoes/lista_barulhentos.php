<?php
session_start();
require_once 'config.php';

// Segurança: Apenas coordenador acessa
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado.");
}

try {
    // Consulta SQL: Conta quantas vezes cada aluno aparece na lista de barulhentos
    $sql = "SELECT u.nome_completo, t.nome_turma, COUNT(b.id) as total_ocorrencias, MAX(b.data_registro) as ultima_vez
            FROM lista_barulhentos b
            INNER JOIN usuarios u ON b.id_usuario = u.id
            INNER JOIN turmas t ON u.id_turma = t.id
            GROUP BY u.id
            ORDER BY total_ocorrencias DESC, ultima_vez DESC";

    $stmt = $pdo->query($sql);
    $barulhentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar lista: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alunos Barulhentos</title>
    <style>
        .alerta { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <a href="coordenador.php">Voltar ao Painel</a>
    <h1>Relatório de Alunos Barulhentos</h1>

    <table>
        <thead>
            <tr>
                <th>Nome Completo</th>
                <th>Turma</th>
                <th>Qtd. de Ocorrências</th>
                <th>Último Registro</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($barulhentos) > 0): ?>
                <?php foreach ($barulhentos as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_turma']); ?></td>
                        <td><?php echo $aluno['total_ocorrencias']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($aluno['ultima_vez'])); ?></td>
                        <td>
                            <?php if ($aluno['total_ocorrencias'] >= 3): ?>
                                <span class="alerta">Reincidente (Chamar Coordenação)</span>
                            <?php else: ?>
                                <span>Aviso Verbal</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nenhum aluno registrado na lista de barulho.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>