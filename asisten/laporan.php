<?php
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once 'templates/header.php';
require_once '../config.php';

$filterModul = $_GET['modul'] ?? '';
$filterMahasiswa = $_GET['mahasiswa'] ?? '';
$filterStatus = $_GET['status'] ?? '';

$modulList = $conn->query("SELECT m.id, m.judul FROM modul m ORDER BY judul ASC")->fetch_all(MYSQLI_ASSOC);
$mahasiswaList = $conn->query("SELECT u.id, u.nama FROM users u WHERE role = 'mahasiswa' ORDER BY nama ASC")->fetch_all(MYSQLI_ASSOC);

$sql = "
    SELECT s.*, u.nama AS namaMahasiswa, mo.judul AS namaModul
    FROM submission s
    JOIN users u ON s.idUser = u.id
    JOIN modul mo ON s.idModul = mo.id
    WHERE 1=1
";

$params = [];
$types = "";

if (!empty($filterModul)) {
    $sql .= " AND s.idModul = ?";
    $params[] = $filterModul;
    $types .= "i";
}

if (!empty($filterMahasiswa)) {
    $sql .= " AND s.idUser = ?";
    $params[] = $filterMahasiswa;
    $types .= "i";
}

if (!empty($filterStatus)) {
    $sql .= " AND s.status = ?";
    $params[] = $filterStatus;
    $types .= "s";
}

$sql .= " ORDER BY s.submitted_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$dataLaporan = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="bg-white p-6 rounded-xl shadow-md mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Laporan Masuk Mahasiswa</h2>

    <form method="GET" class="mb-6 grid md:grid-cols-3 gap-4">
        <select name="modul" class="border p-2 rounded">
            <option value="">-- Semua Modul --</option>
            <?php foreach ($modulList as $modul): ?>
                <option value="<?= $modul['id'] ?>" <?= ($filterModul == $modul['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($modul['judul']) ?>
                </option>
            <?php endforeach ?>
        </select>

        <select name="mahasiswa" class="border p-2 rounded">
            <option value="">-- Semua Mahasiswa --</option>
            <?php foreach ($mahasiswaList as $mhs): ?>
                <option value="<?= $mhs['id'] ?>" <?= ($filterMahasiswa == $mhs['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mhs['nama']) ?>
                </option>
            <?php endforeach ?>
        </select>

        <select name="status" class="border p-2 rounded">
            <option value="">-- Semua Status --</option>
            <option value="selesai" <?= ($filterStatus == 'selesai') ? 'selected' : '' ?>>Selesai</option>
            <option value="belum" <?= ($filterStatus == 'belum') ? 'selected' : '' ?>>Belum</option>
        </select>

        <div class="col-span-full text-right">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Terapkan Filter</button>
        </div>
    </form>

    <?php if (count($dataLaporan) > 0): ?>
        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Mahasiswa</th>
                        <th class="px-4 py-2 border">Modul</th>
                        <th class="px-4 py-2 border">File</th>
                        <th class="px-4 py-2 border">Waktu</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Aksi</th>
                        <th class="px-4 py-2 border">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($dataLaporan as $row): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 text-center"><?= $no++ ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['namaMahasiswa']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['namaModul']) ?></td>
                            <td class="px-4 py-2">
                                <a href="../uploads/tugas/<?= $row['file_path'] ?>" target="_blank" class="text-blue-600 hover:underline">
                                    Lihat File
                                </a>
                            </td>
                            <td class="px-4 py-2"><?= date('d M Y, H:i', strtotime($row['submitted_at'])) ?></td>
                            <td class="px-4 py-2">
                                <?php if ($row['status'] === 'selesai'): ?>
                                    <span class="bg-green-100 text-green-700 text-sm px-2 py-1 rounded">Selesai</span>
                                <?php else: ?>
                                    <span class="bg-yellow-100 text-yellow-700 text-sm px-2 py-1 rounded">Belum</span>
                                <?php endif ?>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a href="nilai_laporan.php?id=<?= $row['id'] ?>" 
                                class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">
                                    Nilai
                                </a>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <?= $row['nilai'] !== null ? $row['nilai'] : '<span class="text-gray-400 italic">Belum Dinilai</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Tidak ada laporan ditemukan dengan filter ini.</p>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
