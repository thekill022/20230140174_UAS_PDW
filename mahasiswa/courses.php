<?php
$pageTitle = 'Daftar Praktikum';
$activePage = 'courses';
require_once 'templates/header_mahasiswa.php'; 
require_once '../config.php';

$idUser = $_SESSION['user_id'];

$sql = "SELECT id, nama FROM matkul ORDER BY nama ASC";
$result = $conn->query($sql);
?>

<?php if (isset($_GET['status'])): ?>
    <div class="mb-4">
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                Berhasil mendaftar praktikum.
            </div>
        <?php elseif ($_GET['status'] === 'failed'): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
                Gagal mendaftar praktikum.
            </div>
        <?php elseif ($_GET['status'] === 'exists'): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-2 rounded">
                Anda sudah mendaftar praktikum ini.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>


<div class="bg-white p-6 rounded-xl shadow-md mt-10">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Daftar Praktikum yang Tersedia</h3>

    <?php if ($result->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="border p-4 rounded-lg shadow hover:shadow-lg transition-all">
                    <h4 class="text-xl font-semibold text-blue-700 mb-2"><?= htmlspecialchars($row['nama']) ?></h4>
                    <button 
                        onclick="openModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama']) ?>')" 
                        class="bg-blue-700 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Daftar
                    </button>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada mata praktikum tersedia.</p>
    <?php endif; ?>
</div>

<!-- Modal Konfirmasi -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Pendaftaran</h2>
        <p class="mb-4">Apakah Anda yakin ingin mendaftar praktikum <span id="modalMatkulName" class="font-semibold text-blue-700"></span>?</p>
        <form action="courses_data.php" method="POST">
            <input type="hidden" name="idUser" value="<?= $idUser ?>">
            <input type="hidden" name="idMatkul" id="modalMatkulId">
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                <button type="submit" name="daftar" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Daftar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id, nama) {
    document.getElementById('modalMatkulId').value = id;
    document.getElementById('modalMatkulName').innerText = nama;
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modal').classList.add('flex');
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.getElementById('modal').classList.remove('flex');
}
</script>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
