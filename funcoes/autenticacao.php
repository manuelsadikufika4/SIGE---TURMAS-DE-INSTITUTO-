<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeUsuario = trim($_POST['txtnomeUsuario'] ?? '');
    $senha = $_POST['txtsenha'] ?? '';

    if (empty($nomeUsuario) || empty($senha)) {
        header('Location: login.php?erro=1');
        exit();
    }

    $stmt = $pdo->prepare("SELECT id,nomeUsuario, senha, cargo,turma FROM usuarios WHERE nomeUsuario = ?");
    $stmt->execute([$nomeUsuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_cargo'] = $user['cargo'];
        $_SESSION['nomeUsuario'] = $user['nomeUsuario'];
        $_SESSION['user_turma']= $user['turma'];
        switch ($user['cargo']) {
            case 'delegado':
            $destino = 'delegado.php'; 
            break;
            case 'professor':
            $destino = 'professor.php'; break;
            case 'alunoComun': 
                 $destino = 'alunoComun.php'; break;
            case 'coordenador':  
                $destino = 'coordenador.php'; break;
            default:
             $destino = '../login.php?erro=1';
        }
        header("Location: $destino");
        exit();
    } else {
        header('Location: ../login.php?erro=1');
        exit();
    }
} else {
    header('Location: ../login.php');
    exit();
}
?>