<?php
require_once '../config.php';
require_once 'templates/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href = 'laporan_masuk.php';</script>";
    exit;
}

$idSubmission = intval($_GET['id']);

$sql = "
    SELECT s.*, u.nama AS namaMahasiswa, m.judul AS namaModul 
    FROM submission s
    JOIN users u ON s.idUser = u.id
    JOIN modul m ON s.idModul = m.id
    WHERE s.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idSubmission);
$stmt->execute();
$result = $stmt->get_result();
$submission = $result->fetch_assoc();

if (!$submission) {
    echo "<p class='text-red-600'>Data tidak ditemukan!</p>";
    require_once 'templates/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = intval($_POST['nilai']);
    $feedback = trim($_POST['feedback']);

    $stmt = $conn->prepare("UPDATE submission SET nilai = ?, feedback = ? WHERE id = ?");
    $stmt->bind_param("isi", $nilai, $feedback, $idSubmission);
    $stmt->execute();

    $idUserNotif = $submission['idUser'];
    $pesan = "Laporan Anda untuk modul '{$submission['namaModul']}' telah dinilai.";
    $link = "mahasiswa/my_courses.php";
    $stmtNotif = $conn->prepare("INSERT INTO notification (idUser, pesan, type, link, created_at) VALUES (?, ?, 'nilai', ?, NOW())");
    $stmtNotif->bind_param("iss", $idUserNotif, $pesan, $link);
    $stmtNotif->execute();

    echo "<script>window.location.href = 'laporan.php?status=nilai_tersimpan';</script>";
    exit;
}
?>

<div class="bg-white p-6 rounded-lg shadow-md mt-6 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Nilai Laporan</h2>

    <div class="mb-6 space-y-2">
        <p><strong>Nama Mahasiswa:</strong> <?= htmlspecialchars($submission['namaMahasiswa']) ?></p>
        <p><strong>Modul:</strong> <?= htmlspecialchars($submission['namaModul']) ?></p>
        <p><strong>Waktu Pengumpulan:</strong> <?= date('d M Y, H:i', strtotime($submission['submitted_at'])) ?></p>
        <p>
            <strong>File:</strong>
            <a href="../uploads/tugas/<?= htmlspecialchars($submission['file_path']) ?>" target="_blank" class="text-blue-600 underline">Unduh</a>
        </p>
    </div>

    <form method="POST">
        <div class="mb-4">
            <label class="block mb-1 font-medium">Nilai (0–100)</label>
            <input type="number" name="nilai" min="0" max="100" value="<?= htmlspecialchars($submission['nilai'] ?? '') ?>" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-medium">Feedback</label>
            <textarea name="feedback" rows="4" class="w-full border rounded p-2" required><?= htmlspecialchars($submission['feedback'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Nilai</button>
        <a href="laporan_masuk.php" class="ml-4 text-gray-600 hover:underline">Kembali</a>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
