<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['user_id'];
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_nome = $_POST['nomeUsuario'];
    $novo_email = $_POST['email']; // Novo campo de e-mail capturado
    $nova_senha = $_POST['senha'];

    try {
        // Verifica se o novo e-mail já existe para outro usuário (evita duplicidade)
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmtCheck->execute([$novo_email, $id_usuario]);
        
        if ($stmtCheck->fetch()) {
            $mensagem = "Erro: Este e-mail já está sendo usado por outra conta.";
        } else {
            if (!empty($nova_senha)) {
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nomeUsuario = ?, email = ?, senha = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$novo_nome, $novo_email, $senha_hash, $id_usuario]);
            } else {
                $sql = "UPDATE usuarios SET nomeUsuario = ?, email = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$novo_nome, $novo_email, $id_usuario]);
            }

            $_SESSION['nomeUsuario'] = $novo_nome;
            $mensagem = "Dados atualizados com sucesso!";
        }
        
    } catch (PDOException $e) {
        $mensagem = "Erro ao atualizar: " . $e->getMessage();
    }
}

try {
    $stmt = $pdo->prepare("SELECT nomeUsuario, email, cargo FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>
</head>
<body>

    <h2>Configurações do Perfil</h2>

    <?php if(!empty($mensagem)) echo "<p><strong>$mensagem</strong></p>"; ?>

    <form method="POST" action="perfil.php">
        
        <div>
            <label>Nome de Usuário:</label><br>
            <input type="text" name="nomeUsuario" value="<?php echo htmlspecialchars($dados['nomeUsuario']); ?>" required>
        </div>

        <br>

        <div>
            <label>E-mail (Login):</label><br>
            <input type="email" name="email" value="<?php echo htmlspecialchars($dados['email']); ?>" required>
            <br><small>* Se você alterar o e-mail, deverá usar o novo endereço no próximo login.</small>
        </div>

        <br>

        <div>
            <label>Cargo:</label><br>
            <input type="text" value="<?php echo htmlspecialchars($dados['cargo']); ?>" disabled>
        </div>

        <br>

        <div>
            <label>Nova Senha:</label><br>
            <input type="password" name="senha" placeholder="Deixe em branco para não alterar">
        </div>

        <br>

        <button type="submit">Salvar Alterações</button>
    </form>

    <br>
    <hr>
    <a href="coordenador.php">Voltar ao Painel do Coordenador</a>

</body>
</html>