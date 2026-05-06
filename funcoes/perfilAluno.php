<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID do aluno inválido.');
}

$id_aluno = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT nome_completo, numeroInterno, turma, nomeUsuario FROM usuarios WHERE id = ? AND cargo = 'alunoComun'");
$stmt->execute([$id_aluno]);
$aluno = $stmt->fetch();

if (!$aluno) {
    die('Aluno não encontrado.');
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Aluno</title>
</head>
<body>
    <h1>Perfil do Aluno</h1>
    <p><strong>Nome:</strong> <?php echo htmlspecialchars($aluno['nome_completo']); ?></p>
    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($aluno['nomeUsuario']); ?></p>
    <p><strong>Matrícula:</strong> <?php echo htmlspecialchars($aluno['numeroInterno']); ?></p>
    <p><strong>Turma:</strong> <?php echo htmlspecialchars($aluno['turma']); ?></p>
    <hr>
    <p><a href="javascript:history.back()">Voltar</a></p>
    <p><a href="registrar_presenca.php?id=<?php echo $id_aluno; ?>">Registrar Presença</a></p>
</body>
</html>