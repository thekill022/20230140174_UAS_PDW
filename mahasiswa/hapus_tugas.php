<?php
require_once '../config.php';
session_start();

$idUser = $_SESSION['user_id'];
$idModul = $_POST['idModul'] ?? null;

if ($idModul) {
    $stmt = $conn->prepare("DELETE FROM submission WHERE idUser = ? AND idModul = ?");
    $stmt->bind_param("ii", $idUser, $idModul);
    $stmt->execute();
}

header("Location: my_courses.php?hapus=success");
exit;
?>