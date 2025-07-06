<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idEnroll = (int) $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM enrollment WHERE id = ?");
    $stmt->bind_param("i", $idEnroll);
    $stmt->execute();
}

echo "<script>window.location.href = 'http://localhost:8080/SistemPengumpulanTugas/asisten/kelola_enrollment.php'</script>";
exit;
?>
