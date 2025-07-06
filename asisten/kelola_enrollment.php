<?php
$pageTitle = 'Kelola Pendaftaran';
$activePage = 'akun';
require_once 'templates/header.php';
require_once '../config.php';

$matkulList = $conn->query("SELECT id, nama FROM matkul ORDER BY nama ASC")->fetch_all(MYSQLI_ASSOC);

$selectedMatkul = isset($_GET['matkul']) ? (int) $_GET['matkul'] : 0;

$enrolledList = [];
if ($selectedMatkul > 0) {
    $stmt = $conn->prepare("
        SELECT e.id AS enroll_id, u.id AS user_id, u.nama AS nama_mahasiswa, u.email
        FROM enrollment e
        JOIN users u ON u.id = e.idMahsiswa
        WHERE e.idMatkul = ?
    ");
    $stmt->bind_param("i", $selectedMatkul);
    $stmt->execute();
    $res = $stmt->get_result();
    $enrolledList = $res->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="bg-white p-6 rounded-xl shadow-md mt-10">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Pendaftaran Mahasiswa</h2>
        <a href="tambah_enrollment.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Pendaftaran</a>
    </div>

    <form method="GET" class="mb-6">
        <label for="matkul" class="block text-sm font-semibold mb-2">Filter berdasarkan Mata Kuliah:</label>
        <select name="matkul" id="matkul" onchange="this.form.submit()" class="w-full max-w-sm p-2 border rounded">
            <option value="">-- Pilih Mata Kuliah --</option>
            <?php foreach ($matkulList as $m): ?>
                <option value="<?= $m['id'] ?>" <?= ($selectedMatkul == $m['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['nama']) ?>
                </option>
            <?php endforeach ?>
        </select>
    </form>

    <?php if ($selectedMatkul > 0): ?>
        <table class="min-w-full table-auto text-left border rounded overflow-hidden">
            <thead class="bg-gray-100 text-sm font-semibold text-gray-700">
                <tr>
                    <th class="px-4 py-2">Nama Mahasiswa</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y">
                <?php foreach ($enrolledList as $row): ?>
                    <tr>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="edit_enrollment.php?id=<?= $row['enroll_id'] ?>" class="text-yellow-600 hover:underline">Edit</a>
                            <button 
                                type="button"
                                class="text-red-600 hover:underline openDeleteModal"
                                data-nama="<?= htmlspecialchars($row['nama_mahasiswa'], ENT_QUOTES, 'UTF-8') ?>"
                                data-id="<?= htmlspecialchars($row['enroll_id'], ENT_QUOTES, 'UTF-8') ?>"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-500 mt-4">Silakan pilih mata kuliah untuk melihat daftar mahasiswa.</p>
    <?php endif ?>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="modalDelete" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md mx-auto">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Hapus</h2>
        <p class="mb-4">Yakin ingin menghapus pendaftaran <span id="deleteNama" class="font-semibold text-red-600"></span>?</p>
        <form action="hapus_enrollment.php" method="POST">
            <input type="hidden" name="id" id="deleteId">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModalDelete()" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.openDeleteModal');
        const modal = document.getElementById('modalDelete');
        const deleteNama = document.getElementById('deleteNama');
        const deleteId = document.getElementById('deleteId');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const nama = this.getAttribute('data-nama');
                const id = this.getAttribute('data-id');

                deleteNama.textContent = nama;
                deleteId.value = id;

                modal.classList.remove('hidden');
            });
        });

        window.closeModalDelete = function () {
            modal.classList.add('hidden');
        }
    });
</script>

<?php require_once 'templates/footer.php'; ?>
