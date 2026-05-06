<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'coordenador') {
    die("Acesso negado. <a href='login.php'>Fazer Login</a>");
}

// Busca os relatórios
$query = "
    SELECT 
        re.delegado_id, 
        re.turma, 
        re.data_envio, 
        u.nomeUsuario AS delegado_nome 
    FROM relatorios_entregues re
    INNER JOIN usuarios u ON re.delegado_id = u.id
    ORDER BY re.data_envio DESC
";
$stmt = $pdo->query($query);
$relatorios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Coordenador</title>
</head>
<body>

    <div style="text-align: right;">
        <strong><?php echo $_SESSION['nomeUsuario']; ?></strong> | 
        <a href="perfil.php">Meu Perfil (Alterar Dados)</a> | 
        <a href="logout.php">Sair</a>
    </div>

    <h1>Painel de Controle - Coordenação</h1>

    <hr>

    <p><a href="cadastrar_aluno.php">+ Cadastrar Novo Aluno</a></p>
    <p><a href="lista_barulhentos.php">Ver lista dos Barulhentos</a></p>
    <p><a href="ver_turmas.php">Ver Turmas</a></p>
    <p><a href="cadastrar_horario.php">+ Cadastrar Horário das aulas</a></p>
    <p><a href="remover_vinculo.php">Remover Vínculo do Professor com Turma</a></p>
    <p><a href="vincular_professor.php">Vincular o Professor na sua Turma</a></p>

    <hr>

    <h2>Relatórios de Presença Recebidos</h2>

    <?php if (empty($relatorios)): ?>
        <p>Nenhuma lista foi enviada pelos delegados ainda.</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Data e Hora</th>
                    <th>Turma</th>
                    <th>Delegado Responsável</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($relatorios as $r): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($r['data_envio'])); ?></td>
                    <td><?php echo htmlspecialchars($r['turma']); ?></td>
                    <td><?php echo htmlspecialchars($r['delegado_nome']); ?></td>
                    <td>
                        <a href="detalhesAlunos.php?delegado_id=<?php echo $r['delegado_id']; ?>&turma=<?php echo $r['turma']; ?>&data=<?php echo date('Y-m-d', strtotime($r['data_envio'])); ?>">
                            Ver Lista de Alunos
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>