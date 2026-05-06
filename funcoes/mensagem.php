<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_cargo'])) {
    die("Erro: Sessão expirada.");
}

$user_id = $_SESSION['user_id'];
$user_cargo = $_SESSION['user_cargo'];
$contato_selecionado = isset($_GET['com']) ? (int)$_GET['com'] : null;

// 1. Atualizar Status Online
try {
    $pdo->prepare("UPDATE usuarios SET ultima_atividade = NOW() WHERE id = ?")->execute([$user_id]);
} catch (Exception $e) {}

// 2. Envio de Mensagem
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar']) && $contato_selecionado) {
    $texto = $_POST['texto'];
    $arquivo_url = null;
    $tipo_arquivo = 'texto';

    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
        $nome_arq = md5(uniqid()) . "." . $ext;
        $dir = "../uploads/";

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $dir .= "imagens/"; $tipo_arquivo = 'imagem';
        } elseif (in_array($ext, ['mp3', 'wav', 'm4a'])) {
            $dir .= "audios/"; $tipo_arquivo = 'audio';
        }

        if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $dir . $nome_arq)) {
            $arquivo_url = "uploads/" . ($tipo_arquivo == 'imagem' ? 'imagens/' : 'audios/') . $nome_arq;
        }
    }

    if (!empty($texto) || $arquivo_url) {
        $stmt = $pdo->prepare("INSERT INTO mensagens (id_remetente, id_destinatario, mensagem, arquivo_url, tipo_arquivo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $contato_selecionado, $texto, $arquivo_url, $tipo_arquivo]);
        header("Location: mensagem.php?com=" . $contato_selecionado);
        exit();
    }
}

// 3. Filtro de Contatos
$where = "";
if ($user_cargo === 'delegado') $where = "WHERE cargo IN ('professor', 'coordenador', 'alunoComun')";
elseif ($user_cargo === 'professor') $where = "WHERE cargo IN ('delegado', 'coordenador')";
elseif ($user_cargo === 'alunoComun') $where = "WHERE cargo = 'delegado'";
else $where = "WHERE id != $user_id";

$contatos = $pdo->query("SELECT id, nomeUsuario, cargo, ultima_atividade FROM usuarios $where ORDER BY nomeUsuario ASC")->fetchAll();

// 4. Funções Auxiliares
function taOnline($data) {
    return $data && (time() - strtotime($data)) <= 300;
}

function mostrarMidia($m) {
    if (!$m['arquivo_url']) return;
    $path = "../" . $m['arquivo_url'];
    if ($m['tipo_arquivo'] == 'imagem') echo "<br><img src='$path' width='200'><br>";
    if ($m['tipo_arquivo'] == 'audio') echo "<br><audio controls><source src='$path'></audio><br>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat Simples</title>
</head>
<body>

    <h2>Sistema de Chat</h2>
    <a href="javascript:history.back()">Voltar</a>
    <hr>

    <h3>Seus Contatos</h3>
    <ul>
        <?php foreach ($contatos as $c): ?>
            <li>
                <a href="?com=<?= $c['id'] ?>">
                    <strong><?= htmlspecialchars($c['nomeUsuario']) ?></strong> 
                    (<?= ucfirst($c['cargo']) ?>) - 
                    <?= taOnline($c['ultima_atividade']) ? 'ONLINE' : 'OFFLINE' ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <hr>

    <?php if ($contato_selecionado): 
        $stmtUser = $pdo->prepare("SELECT nomeUsuario FROM usuarios WHERE id = ?");
        $stmtUser->execute([$contato_selecionado]);
        $user_chat = $stmtUser->fetch();

        $stmtMsg = $pdo->prepare("
            SELECT * FROM mensagens 
            WHERE (id_remetente = ? AND id_destinatario = ?) 
            OR (id_remetente = ? AND id_destinatario = ?) 
            ORDER BY data_envio ASC
        ");
        $stmtMsg->execute([$user_id, $contato_selecionado, $contato_selecionado, $user_id]);
        $historico = $stmtMsg->fetchAll();
    ?>
        <h3>Conversa com: <?= htmlspecialchars($user_chat['nomeUsuario']) ?></h3>

        <div>
            <?php foreach ($historico as $m): ?>
                <p>
                    <strong><?= ($m['id_remetente'] == $user_id) ? 'Você' : htmlspecialchars($user_chat['nomeUsuario']) ?>:</strong><br>
                    <?= nl2br(htmlspecialchars($m['mensagem'])) ?>
                    <?php mostrarMidia($m); ?>
                    <br>
                    <small><?= date('d/m/Y H:i', strtotime($m['data_envio'])) ?></small>
                </p>
                <hr width="30%" align="left">
            <?php endforeach; ?>
        </div>

        <br>
        <form method="POST" enctype="multipart/form-data">
            <label>Escrever Mensagem:</label><br>
            <textarea name="texto" rows="4" cols="50"></textarea><br><br>
            
            <label>Enviar Imagem ou Áudio:</label><br>
            <input type="file" name="arquivo" accept="image/*, audio/*"><br><br>
            
            <button type="submit" name="enviar">Enviar Mensagem</button>
        </form>

    <?php else: ?>
        <p>Selecione um contato acima para ler as mensagens.</p>
    <?php endif; ?>

</body>
</html>