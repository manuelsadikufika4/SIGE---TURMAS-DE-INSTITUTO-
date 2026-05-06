<?php
session_start();
require_once 'config.php';

if ($_SESSION['user_cargo'] === 'coordenador' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM professor_turma WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}
header("Location: vincular_professor.php");
exit();