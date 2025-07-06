<?php
$pageTitle = 'Tambah Pendaftaran';
$activePage = 'akun';
require_once 'templates/header.php';
require_once '../config.php';

$mahasiswa = $conn->query("SELECT id, nama FROM users WHERE role = 'mahasiswa' ORDER BY nama ASC")->fetch_all(MYSQLI_ASSOC);
$matkulList = $conn->query("SELECT id, nama FROM matkul ORDER BY nama ASC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMahasiswa = $_POST['idMahasiswa'];
    $idMatkul = $_POST['idMatkul'];

    $cek = $conn->prepare("SELECT * FROM enrollment WHERE idMahsiswa = ? AND idMatkul = ?");
    $cek->bind_param("ii", $idMahasiswa, $idMatkul);
    $cek->execute();
    $cekResult = $cek->get_result();
    if ($cekResult->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO enrollment (idMahsiswa, idMatkul) VALUES (?, ?)");
        $stmt->bind_param("ii", $idMahasiswa, $idMatkul);
        $stmt->execute();
        echo "<script>window.location.href = 'http://localhost:8080/SistemPengumpulanTugas/asisten/kelola_enrollment.php?matkul=$idMatkul'</script>";
        exit;
    } else {
        echo "<div class='text-red-500 p-4'>Mahasiswa sudah terdaftar di mata kuliah ini.</div>";
    }
}
?>

<div class="bg-white p-6 rounded-xl shadow-md mt-10 max-w-md mx-auto">
    <h2 class="text-xl font-bold mb-4">Tambah Pendaftaran Mahasiswa</h2>
    <form method="POST">
        <label>Mahasiswa:</label>
        <select name="idMahasiswa" required class="w-full mb-3 p-2 border rounded">
            <option value="">-- Pilih Mahasiswa --</option>
            <?php foreach ($mahasiswa as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama']) ?></option>
            <?php endforeach ?>
        </select>

        <label>Mata Kuliah:</label>
        <select name="idMatkul" required class="w-full mb-4 p-2 border rounded">
            <option value="">-- Pilih Mata Kuliah --</option>
            <?php foreach ($matkulList as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama']) ?></option>
            <?php endforeach ?>
        </select>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Daftarkan</button>
        <a href="kelola_enrollment.php" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-400">Kembali</a>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
