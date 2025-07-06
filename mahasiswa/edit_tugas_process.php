<?php
require_once '../config.php';
session_start();

$idUser = $_SESSION['user_id'];
$idModul = $_POST['idModul'];
$file = $_FILES['tugas'];

if ($file['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = uniqid() . '.' . $ext;
    $target = '../uploads/tugas/' . $newName;

    if (!is_dir('../uploads/tugas')) {
        mkdir('../uploads/tugas', 0777, true);
    }

    move_uploaded_file($file['tmp_name'], $target);

    $check = $conn->prepare("SELECT file_path FROM submission WHERE idUser = ? AND idModul = ?");
    $check->bind_param("ii", $idUser, $idModul);
    $check->execute();
    $res = $check->get_result();
    if ($row = $res->fetch_assoc()) {
        $oldPath = '../uploads/tugas/' . $row['file_path'];
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    $stmt = $conn->prepare("UPDATE submission SET file_path = ?, submitted_at = NOW() WHERE idUser = ? AND idModul = ?");
    $stmt->bind_param("sii", $newName, $idUser, $idModul);
    $stmt->execute();
}

header("Location: my_courses.php?edit=success");
exit;
