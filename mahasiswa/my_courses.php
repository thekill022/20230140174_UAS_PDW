<?php
$pageTitle = 'Kumpul Tugas';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$idUser = $_SESSION['user_id'];

$sql = "
    SELECT m.id AS idMatkul, m.nama 
    FROM enrollment e
    JOIN matkul m ON m.id = e.idMatkul
    WHERE e.idMahsiswa = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();

$praktikumList = [];
while ($row = $result->fetch_assoc()) {
    $praktikumList[] = $row;
}

$modulMap = [];
foreach ($praktikumList as $praktikum) {
    $idMatkul = $praktikum['idMatkul'];

    $sqlModul = "
    SELECT mo.*, s.status, s.file_path, s.submitted_at, s.nilai, s.feedback
    FROM modul mo
    LEFT JOIN submission s ON s.idModul = mo.id AND s.idUser = ?
    WHERE mo.idMatkul = ?
    ORDER BY mo.id DESC
    ";
    $stmtModul = $conn->prepare($sqlModul);
    $stmtModul->bind_param("ii", $idUser, $idMatkul);
    $stmtModul->execute();
    $resModul = $stmtModul->get_result();
    $modulMap[$idMatkul] = $resModul->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="bg-white p-6 rounded-xl shadow-md mt-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Pengumpulan Tugas</h2>

    <?php if (count($praktikumList) === 0): ?>
        <p class="text-gray-500">Anda belum mendaftar praktikum manapun.</p>
    <?php else: ?>
        <?php foreach ($praktikumList as $praktikum): ?>
            <div class="mb-8">
                <h3 class="text-2xl text-blue-700 font-semibold mb-4"><?= htmlspecialchars($praktikum['nama']) ?></h3>

                <?php if (!empty($modulMap[$praktikum['idMatkul']])): ?>
                    <div class="grid gap-4">
                        <?php foreach ($modulMap[$praktikum['idMatkul']] as $modul): ?>
                            <div class="p-4 border rounded-xl shadow flex flex-col md:flex-row justify-between items-start md:items-center bg-gray-50 hover:bg-gray-100 transition">
                                <div>
                                    <div class="font-semibold text-lg"><?= htmlspecialchars($modul['judul']) ?></div>
                                    <div class="text-sm text-gray-600 mb-2">
                                        Materi: <a href="../uploads/<?= $modul['path'] ?>" class="text-blue-600 hover:underline" target="_blank"><?= $modul['path'] ?></a>
                                    </div>

                                    <?php if ($modul['status'] === 'selesai'): ?>

                                            <div class="text-sm text-green-600 mb-1">
                                        ✅ Sudah dikumpulkan pada <?= date('d M Y, H:i', strtotime($modul['submitted_at'])) ?><br>
                                        <a href="../uploads/tugas/<?= $modul['file_path'] ?>" class="text-blue-600 hover:underline" target="_blank">Lihat Tugas</a>
                                    </div>

                                    <?php if (!empty($modul['nilai'])): ?>
                                            <div class="mt-2 text-sm text-gray-800">
                                                <span class="font-semibold">Nilai:</span> <?= $modul['nilai'] ?><br>
                                                <span class="font-semibold">Feedback:</span> <?= nl2br(htmlspecialchars($modul['feedback'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex gap-2 mt-2">
                                                <a href="edit_tugas.php?idModul=<?= $modul['id'] ?>" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">Edit</a>
                                                <button 
                                                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-sm openDeleteModal"
                                                    data-id="<?= $modul['id'] ?>" 
                                                    data-nama="<?= htmlspecialchars($modul['judul']) ?>"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        <?php endif; ?>                                    

                                    <?php if (!empty($modul['nilai'])): ?>
                                        <div class="mt-2 text-sm text-gray-800">
                                            <span class="font-semibold">Nilai:</span> <?= $modul['nilai'] ?><br>
                                            <span class="font-semibold">Feedback:</span> <?= nl2br(htmlspecialchars($modul['feedback'])) ?>
                                        </div>
                                    <?php endif ?>
                                    <?php else: ?>
                                        <div class="text-yellow-600 text-sm mb-2">⚠️ Belum dikumpulkan</div>
                                        <a href="upload_tugas.php?idModul=<?= $modul['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm">
                                            Upload Tugas
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500">Belum ada modul untuk praktikum ini.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Hapus -->
<div id="modalDelete" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Hapus</h2>
        <p class="mb-4">Yakin ingin menghapus tugas untuk modul <span id="deleteJudul" class="font-semibold text-red-600"></span>?</p>
        <form action="hapus_tugas.php" method="POST">
            <input type="hidden" name="idModul" id="deleteIdModul">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModalDelete()" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.openDeleteModal').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const nama = button.dataset.nama;

            document.getElementById('deleteIdModul').value = id;
            document.getElementById('deleteJudul').innerText = nama;

            const modal = document.getElementById('modalDelete');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeModalDelete() {
        const modal = document.getElementById('modalDelete');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>


<?php require_once 'templates/footer_mahasiswa.php'; ?>
