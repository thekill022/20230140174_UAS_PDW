<?php
$pageTitle = 'Kelola Modul';
$activePage = 'modul';
require 'templates/header.php';
require 'matkul.php';
require 'modul_data.php';

$data = getData();

$data2 = getModulData();

if($data["status"] == 500) {
    $result =[];
} else {
    $result = $data["data"];
}

if($data2["status"] == 500) {
    $result2 =[];
} else {
    $result2 = $data2["data"];
}

?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Kelola Modul / Pertemuan</h2>
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        + Tambah Modul
    </button>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
<table class="w-full table-auto border-collapse">
    <thead>
        <tr class="bg-gray-100 text-left">
            <th class="px-4 py-2 text-center">#</th>
            <th class="px-4 py-2 text-center">Judul Modul</th>
            <th class="px-4 py-2 text-center">Mata Praktikum</th>
            <th class="px-4 py-2 text-center">File Materi</th>
            <th class="px-4 py-2 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-gray-700">
        <?php if (!empty($result2)) : ?>
            <?php $no = 1; foreach ($result2 as $row) : ?>
                <tr class="border-t">
                    <td class="px-4 py-2 text-center"><?= $no++ ?></td>
                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row["judul"]) ?></td>
                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row["nama"]) ?></td>
                    <td class="px-4 py-2 text-center">
                        <a href="../uploads/<?= $row["path"]?>" class="text-blue-600 hover:underline" target="_blank">
                            <?= htmlspecialchars($row["path"]) ?>
                        </a>
                    </td>
                    <td class="px-4 py-2 space-x-2 text-center">
                        <button class="text-yellow-600 hover:underline btnEdit"
                            data-id="<?= $row['id'] ?>"
                            data-judul="<?= htmlspecialchars($row['judul']) ?>"
                            data-praktikum="<?= $row['idMatkul'] ?>"
                            onclick="openEdit(this)">Edit</button>
                        <button class="text-red-600 hover:underline btnDelete"
                            data-id="<?= $row['id'] ?>"
                            onclick="openDelete(this)">Hapus</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr class="border-t">
                <td class="px-4 py-2 text-center" colspan="5">Tidak ada data modul</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>

<!-- Modal Tambah Modul -->
<div id="modalAdd" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Tambah Modul</h3>
        <form method="POST" action="modul_data.php" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Modul</label>
                <input type="text" name="judul" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Praktikum</label>
                <select name="praktikum" id="matkul" class="w-full p-2 rounded-md border border-slate-200">
                    <option value="">-</option>
                    <?php foreach ($result as $row) :?>
                        <option value="<?php echo $row["id"] ?>"><?php echo $row["nama"] ?></option>
                </select>
                <?php endforeach ?>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Materi (PDF/DOCX)</label>
                <input type="file" name="file" accept=".pdf,.docx" class="w-full  px-3 py-2 file:border-none file:bg-blue-700">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button type="submit" name="add" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Modul -->
<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Modul</h3>
        <form method="POST" action="modul_data.php" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Modul</label>
                <input type="text" name="judul" id="editJudul" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Praktikum</label>
                <select name="praktikum" id="editPraktikum" class="w-full border rounded-lg px-3 py-2">
                    <?php foreach ($result as $row): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File Baru</label>
                <input type="file" name="file" class="w-full border px-3 py-2">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="submit" name="edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan</button>
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Modul -->
<div id="modalDelete" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Hapus Modul</h3>
        <form method="POST" action="modul_data.php">
            <input type="hidden" name="id" id="deleteId">
            <p>Yakin ingin menghapus modul ini?</p>
            <div class="flex justify-end space-x-2 mt-4">
                <button type="submit" name="delete" class="bg-red-600 text-white px-4 py-2 rounded-lg">Hapus</button>
                <button type="button" onclick="document.getElementById('modalDelete').classList.add('hidden')" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(btn) {
    document.getElementById('editId').value = btn.dataset.id;
    document.getElementById('editJudul').value = btn.dataset.judul;
    document.getElementById('editPraktikum').value = btn.dataset.praktikum;
    document.getElementById('modalEdit').classList.remove('hidden');
}

function openDelete(btn) {
    document.getElementById('deleteId').value = btn.dataset.id;
    document.getElementById('modalDelete').classList.remove('hidden');
}
</script>

<?php
require_once 'templates/footer.php';
?>
