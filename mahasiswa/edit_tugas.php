<?php
require_once '../config.php';
session_start();

if (!isset($_GET['idModul'])) {
    header("Location: my_courses.php");
    exit;
}

$idModul = (int) $_GET['idModul'];
$idUser = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT mo.judul, m.nama AS nama_matkul FROM modul mo JOIN matkul m ON mo.idMatkul = m.id WHERE mo.id = ?");
$stmt->bind_param("i", $idModul);
$stmt->execute();
$res = $stmt->get_result();
$modul = $res->fetch_assoc();

if (!$modul) {
    echo "Modul tidak ditemukan.";
    exit;
}
?>

<?php include 'templates/header_mahasiswa.php'; ?>

<div class="bg-white max-w-xl mx-auto mt-12 p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Edit Tugas</h2>
    <p class="text-gray-600 mb-1">Praktikum: <strong><?= htmlspecialchars($modul['nama_matkul']) ?></strong></p>
    <p class="text-gray-600 mb-4">Modul: <strong><?= htmlspecialchars($modul['judul']) ?></strong></p>

    <form action="edit_tugas_process.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="idModul" value="<?= $idModul ?>">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Upload File Baru</label>
            <input type="file" name="tugas" accept=".pdf,.docx" required class="w-full px-3 py-2 border rounded">
        </div>
        <div class="flex justify-between">
            <a href="my_courses.php" class="text-gray-600 hover:underline">← Batal</a>
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php include 'templates/footer_mahasiswa.php'; ?>
