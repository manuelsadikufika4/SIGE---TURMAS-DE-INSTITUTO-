CREATE DATABASE IF NOT EXISTS turma_db;
USE escola;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomeUsuario VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cargo ENUM('delegado', 'professor', 'alunoComun', 'coordenador') NOT NULL
);


ALTER TABLE usuarios
ADD COLUMN nomeCompleto VARCHAR(100) NOT NULL DEFAULT '',
ADD COLUMN numeroInterno VARCHAR(20) NOT NULL DEFAULT '',
ADD COLUMN turma VARCHAR(20) NOT NULL DEFAULT '';

INSERT INTO usuarios (username, password, role, nome_completo, matricula, turma) VALUES
('ana', '$2y$10$...hash...', 'aluno_comun', 'Ana Carolina', '2024003', '3° Ano A'),
('carlos', '$2y$10$...hash...', 'aluno_comun', 'Carlos Eduardo', '2024004', '3° Ano B');
```

# Baixar a biblioteca `phpqrcode`

Faça o download do arquivo `phpqrcode.php` em:
[https://sourceforge.net/projects/phpqrcode/](https://sourceforge.net/projects/phpqrcode/)


<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'delegado') {
    header('Location: login.php');
    exit();
}
require_once 'config.php';

// Buscar todos os alunos (role = 'aluno_comun')
$stmt = $pdo->prepare("SELECT id, username, nome_completo, matricula, turma FROM usuarios WHERE role = 'aluno_comun' ORDER BY nome_completo");
$stmt->execute();
$alunos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Delegado - Lista de Alunos</title>
</head>
<body>
    <h1>Bem-vindo, Delegado <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <h2>Gerar QR Code para Perfil do Aluno</h2>
    
    <?php if (empty($alunos)): ?>
        <p>Nenhum aluno cadastrado.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Turma</th>
                <th>Ação</th>
            </tr>
            <?php foreach ($alunos as $aluno): ?>
            <tr>
                <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                <td><?php echo htmlspecialchars($aluno['matricula']); ?></td>
                <td><?php echo htmlspecialchars($aluno['turma']); ?></td>
                <td>
                    <!-- Botão que chama o script gerador de QR Code -->
                    <form method="get" action="gerar_qr.php" target="_blank">
                        <input type="hidden" name="id" value="<?php echo $aluno['id']; ?>">
                        <button type="submit">📱 Gerar QR Code</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
 
    <p><a href="logout.php">Sair</a></p>
</body>
</html>

## 4. Script para Gerar QR Code (`gerar_qr.php`)

Este script recebe o `id` do aluno, monta a URL completa do perfil e gera a imagem do QR Code.

```php
<?php
require_once 'config.php';
require_once 'phpqrcode.php'; // inclui a biblioteca

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID inválido.');
}

$id_aluno = (int)$_GET['id'];

// Buscar dados do aluno (apenas para garantir que existe)
$stmt = $pdo->prepare("SELECT id, username FROM usuarios WHERE id = ? AND role = 'aluno_comun'");
$stmt->execute([$id_aluno]);
$aluno = $stmt->fetch();

if (!$aluno) {
    die('Aluno não encontrado.');
}

// Montar a URL completa do perfil do aluno
// Altere o domínio e caminho conforme seu ambiente
$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . $host . dirname($_SERVER['SCRIPT_NAME']);
$perfil_url = $base_url . '/perfil_aluno.php?id=' . $id_aluno;

// Gerar QR Code com a URL
// Parâmetros: texto, arquivo de saída (null para enviar diretamente), nível de correção de erro, tamanho do pixel, margem
QRcode::png($perfil_url, false, QR_ECLEVEL_L, 6, 2);
exit;
?>
```

> **Importante:** A biblioteca `phpqrcode` envia diretamente o cabeçalho `Content-Type: image/png`. Por isso, este script **não pode** ter nenhum espaço ou saída antes de `<?php`.

---

## 5. Página de Perfil do Aluno (`perfil_aluno.php`)

Quando o QR Code for escaneado, o navegador abrirá esta página, exibindo os dados do aluno.

```php
<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID do aluno inválido.');
}

$id_aluno = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT nome_completo, matricula, turma, username FROM usuarios WHERE id = ? AND role = 'aluno_comun'");
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
    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($aluno['username']); ?></p>
    <p><strong>Matrícula:</strong> <?php echo htmlspecialchars($aluno['matricula']); ?></p>
    <p><strong>Turma:</strong> <?php echo htmlspecialchars($aluno['turma']); ?></p>
    <hr>
    <p><a href="javascript:history.back()">Voltar</a></p>
</body>
</html>
```

---

## 6. Como testar o fluxo completo

1. **Configure o ambiente**  
   - Execute os comandos SQL para criar/alterar a tabela `usuarios`.  
   - Insira alguns alunos com dados reais (nome, matrícula, turma).  
   - Coloque o arquivo `phpqrcode.php` na raiz do projeto.

2. **Acesse o sistema**  
   - Faça login como **delegado** (usuário `delegado` / senha `delegado123`).  
   - Você verá uma lista de alunos. Para cada aluno, há um botão **Gerar QR Code**.

3. **Gere o QR Code**  
   - Clique em "Gerar QR Code". Uma nova aba será aberta mostrando a imagem do QR Code.  
   - Você pode salvar a imagem ou exibi-la diretamente.

4. **Escanear com o celular**  
   - Use qualquer aplicativo leitor de QR Code no celular.  
   - Aponte para a tela do computador (ou salve a imagem e escaneie do próprio celular).  
   - O leitor abrirá a URL: `http://seudominio/perfil_aluno.php?id=X`.  
   - A página do perfil do aluno será exibida com todos os dados.

---

## 7. Observações finais

- **Segurança:**  
  A página de perfil é pública (qualquer um com a URL pode ver). Se quiser restringir, você pode exigir que o usuário esteja logado e tenha papel de **delegado** ou **professor** para visualizar. Basta adicionar no início de `perfil_aluno.php` a verificação de sessão.

- **Personalização do QR Code:**  
  Você pode ajustar o tamanho (parâmetro `6` em `QRcode::png`) e a margem (`2`). Quanto maior o tamanho, mais fácil a leitura.

- **Sem biblioteca externa?**  
  Se não puder usar `phpqrcode`, é possível gerar QR Codes via API online (ex: `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=...`). Porém, a biblioteca local é mais confiável e não depende de internet.

- **Compatibilidade com o sistema anterior:**  
  As funcionalidades existentes (login, logout, páginas dos outros papéis) continuam funcionando normalmente. Apenas o delegado ganhou a nova opção.

Caso queira que o QR Code seja exibido **dentro da própria página** (sem abrir nova aba), você pode modificar o botão para mostrar a imagem via JavaScript. Porém, o requisito original é apenas gerar o QR Code e, ao escanear, mostrar o perfil – o que já está atendido.