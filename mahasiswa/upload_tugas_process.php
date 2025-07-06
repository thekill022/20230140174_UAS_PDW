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

    $check = $conn->prepare("SELECT id FROM submission WHERE idUser = ? AND idModul = ?");
    $check->bind_param("ii", $idUser, $idModul);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE submission SET file_path = ?, status = 'selesai', submitted_at = NOW() WHERE idUser = ? AND idModul = ?");
        $stmt->bind_param("sii", $newName, $idUser, $idModul);
    } else {
        $stmt = $conn->prepare("INSERT INTO submission (idUser, idModul, file_path, status, submitted_at) VALUES (?, ?, ?, 'selesai', NOW())");
        $stmt->bind_param("iis", $idUser, $idModul, $newName);
    }

    $stmt->execute();
}

header("Location: my_courses.php?upload=success");
exit;
