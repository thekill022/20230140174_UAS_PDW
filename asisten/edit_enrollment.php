<?php
$pageTitle = 'Edit Pendaftaran';
$activePage = 'enrollment';
require_once 'templates/header.php';
require_once '../config.php';

if (!isset($_GET['id'])) {
    header("Location: kelola_enrollment.php");
    exit;
}

$idEnroll = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT e.*, u.nama AS nama_mahasiswa
    FROM enrollment e
    JOIN users u ON u.id = e.idMahsiswa
    WHERE e.id = ?
");
$stmt->bind_param("i", $idEnroll);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='text-red-500 p-4'>Pendaftaran tidak ditemukan.</div>";
    exit;
}

$enrollment = $result->fetch_assoc();

$matkulList = $conn->query("SELECT id, nama FROM matkul ORDER BY nama ASC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMatkul = $_POST['idMatkul'];

    $stmt = $conn->prepare("UPDATE enrollment SET idMatkul = ? WHERE id = ?");
    $stmt->bind_param("ii", $idMatkul, $idEnroll);
    $stmt->execute();

    echo "<script>window.location.href = 'http://localhost:8080/SistemPengumpulanTugas/asisten/kelola_enrollment.php?matkul=$idMatkul'</script>";
    exit;
}
?>

<div class="bg-white p-6 rounded-xl shadow-md mt-10 max-w-md mx-auto">
    <h2 class="text-xl font-bold mb-4">Edit Pendaftaran Mahasiswa</h2>

    <form method="POST">
        <div class="mb-3">
            <label class="block mb-1 font-semibold">Nama Mahasiswa:</label>
            <input type="text" class="w-full p-2 border rounded bg-gray-100" value="<?= htmlspecialchars($enrollment['nama_mahasiswa']) ?>" readonly>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Mata Kuliah:</label>
            <select name="idMatkul" required class="w-full p-2 border rounded">
                <option value="">-- Pilih Mata Kuliah --</option>
                <?php foreach ($matkulList as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $enrollment['idMatkul'] == $m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nama']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update</button>
        <a href="kelola_enrollment.php" class="ml-3 text-gray-600 hover:underline">Batal</a>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
