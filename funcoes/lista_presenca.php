<?php
session_start();
require_once 'config.php';

// 1. Verificação de Segurança e Sessão
if (!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'delegado') {
    header('Location: login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_delegado = $_SESSION['user_id'];
    $turma = $_SESSION['user_turma'];

    try {
        // Registra o envio da lista no banco
        $stmt = $pdo->prepare("INSERT INTO relatorios_entregues (delegado_id, turma) VALUES (?, ?)");
        
        if ($stmt->execute([$id_delegado, $turma])) {
            echo "<h2> Sucesso!</h2>";
            echo "<p>O relatório da turma $turma foi enviado ao painel do coordenador.</p>";
            echo "<a href='painel_delegado.php'>Voltar para o Início</a>";
        }
    } catch (PDOException $e) {
        echo "Erro ao registrar envio: " . $e->getMessage();
    }
}
// 2. Captura a turma do delegado que está logado
// Certifique-se de que 'user_turma' foi definido no seu login.php
$minha_turma = $_SESSION['user_turma'];

// 3. Consulta SQL filtrando pela turma do delegado e pela data de hoje
$query = "
    SELECT 
        u.nome_completo, 
        u.numeroInterno, 
        f.data_registro 
    FROM frequencia f
    INNER JOIN usuarios u ON f.aluno_id = u.id
    WHERE u.turma = ? 
    AND DATE(f.data_registro) = CURDATE()
    ORDER BY u.nome_completo ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$minha_turma]);
$alunos_presentes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Minha Turma - Presença</title>
</head>
<body>

    <h2>Lista de Presença - Turma: <?php echo htmlspecialchars($minha_turma); ?></h2>
    <p>Data: <?php echo date('d/m/Y'); ?></p>

    <table border="1">
        <thead>
            <tr>
                <th>Nº Interno</th>
                <th>Nome do Aluno</th>
                <th>Hora da Leitura</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($alunos_presentes) > 0): ?>
                <?php foreach ($alunos_presentes as $aluno): ?>
                <tr>
                    <td><?php echo htmlspecialchars($aluno['numeroInterno']); ?></td>
                    <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                    <td><?php echo date('H:i:s', strtotime($aluno['data_registro'])); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhum aluno da sua turma marcou presença hoje.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="delegado.php">Voltar</a>
    
    <br><br>

    <?php if (count($alunos_presentes) > 0): ?>
<hr>
<form action="confirmar_envio.php" method="POST">
    <p>Ao clicar abaixo, a lista da turma <strong><?php echo $_SESSION['user_turma']; ?></strong> será enviada ao coordenador.</p>
    <button type="submit" style="padding: 10px; cursor: pointer;">
        Finalizar e Enviar para Coordenador
    </button>
</form>
    <?php endif; ?>

</body>
</html>