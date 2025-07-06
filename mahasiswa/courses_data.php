<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['daftar'])) {
    $idUser = $_POST['idUser'];
    $idMatkul = $_POST['idMatkul'];

    $check = $conn->prepare("SELECT * FROM enrollment WHERE idMahsiswa = ? AND idMatkul = ?");
    $check->bind_param("ii", $idUser, $idMatkul);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        header("Location: courses.php?status=exists");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO enrollment (idMahsiswa, idMatkul) VALUES (?, ?)");
    $stmt->bind_param("ii", $idUser, $idMatkul);

    if ($stmt->execute()) {
        header("Location: courses.php?status=success");
    } else {
        header("Location: courses.php?status=failed");
    }
    exit;
}
?>
