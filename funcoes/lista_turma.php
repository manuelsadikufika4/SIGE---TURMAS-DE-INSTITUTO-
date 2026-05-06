<?php
session_start();
require_once 'config.php';

// 1. Verificação de segurança
$cargos_permitidos = ['delegado', 'alunoComun', 'professor', 'coordenador']; // Adicionei coordenador caso precise acessar

if (!isset($_SESSION['user_cargo']) || !in_array($_SESSION['user_cargo'], $cargos_permitidos)) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_cargo = $_SESSION['user_cargo'];

// --- LÓGICA DO LINK DE VOLTAR DINÂMICO ---
switch ($user_cargo) {
    case 'professor':
        $link_voltar = 'professor.php';
        break;
    case 'coordenador':
        $link_voltar = 'coordenador.php';
        break;
    case 'delegado':
        $link_voltar = 'delegado.php';
        break;
    case 'alunoComun':
        $link_voltar = 'alunoComun.php';
        break;
    default:
        $link_voltar = 'login.php'; // Segurança caso o cargo seja estranho
        break;
}
// ------------------------------------------

try {
    if ($user_cargo === 'professor') {
        // Se o professor veio de uma turma específica via GET
        $id_turma_get = $_GET['id_turma'] ?? null;
        
        if ($id_turma_get) {
            $sql = "SELECT u.id, u.nomeUsuario, u.email, u.cargo, t.nome_turma 
                    FROM usuarios u
                    INNER JOIN turmas t ON u.id_turma = t.id
                    WHERE u.id_turma = :id_turma AND u.cargo IN ('alunoComun', 'delegado')
                    ORDER BY u.nomeUsuario ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_turma' => $id_turma_get]);
        } else {
            // Se não houver GET, mostra todos (comportamento atual)
            $sql = "SELECT u.id, u.nomeUsuario, u.email, u.cargo, t.nome_turma 
                    FROM usuarios u
                    INNER JOIN turmas t ON u.id_turma = t.id
                    WHERE u.cargo IN ('alunoComun', 'delegado')
                    ORDER BY t.nome_turma, u.nomeUsuario ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    } else {
        // Aluno e Delegado veem apenas sua própria turma
        $sql = "SELECT u.id, u.nomeUsuario, u.email, u.cargo, t.nome_turma 
                FROM usuarios u
                INNER JOIN turmas t ON u.id_turma = t.id
                WHERE u.id_turma = (SELECT id_turma FROM usuarios WHERE id = :id_user) 
                AND u.cargo IN ('alunoComun', 'delegado')
                ORDER BY u.nomeUsuario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_user', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nomeTurma = (count($alunos) > 0) ? $alunos[0]['nome_turma'] : "Lista de Alunos";

} catch (PDOException $e) {
    die("Erro ao carregar lista: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alunos</title>
    <style>
        .highlight { background-color: #e0f7fa; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; }
    </style>
</head>
<body>

    <a href="<?php echo $link_voltar; ?>">Voltar ao Painel</a>

    <h1>Visualização: <?php echo htmlspecialchars($nomeTurma); ?></h1>
    <p>Logado como: <strong><?php echo ucfirst($user_cargo); ?></strong></p>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Cargo</th>
                <?php if($user_cargo === 'professor' || $user_cargo === 'coordenador'): ?> 
                    <th>Turma</th> 
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (count($alunos) > 0): ?>
                <?php foreach ($alunos as $aluno): ?>
                    <tr <?php echo ($aluno['id'] == $user_id) ? 'class="highlight"' : ''; ?>>
                        <td><?php echo $aluno['id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($aluno['nomeUsuario']); ?>
                            <?php if($aluno['id'] == $user_id) echo " <strong>(Você)</strong>"; ?>
                        </td>
                        <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['cargo']); ?></td>
                        <?php if($user_cargo === 'professor' || $user_cargo === 'coordenador'): ?> 
                            <td><?php echo htmlspecialchars($aluno['nome_turma']); ?></td> 
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nenhum registro encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>