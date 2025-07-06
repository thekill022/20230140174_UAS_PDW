<?php
$pageTitle = 'Kelola Mata Praktikum';
$activePage = 'praktikum';
require 'templates/header.php';
require 'matkul.php';

$data = getData();

if($data["status"] == 500) {
    $result =[];
} else {
    $result = $data["data"];
}

?>

<div class="flex justify-between items-center mb-6">
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        + Tambah Modul
    </button>
</div>

    <?php if (isset($_GET["status"])) : ?>
        <?php if ($_GET["status"] == "success") : ?>
            <div id="success" class="bg-green-300 opacity-75 p-2 pr-5 rounded-md my-2 flex justify-between items-center">
                <span class="text-white">Berhasil Mengubah Data</span>
                <span class="text-white cursor-pointer" onclick="document.getElementById('success').remove()">x</span>
            </div>
        <?php else : ?>
            <div id="failed" class="bg-red-300 opacity-75 p-2 pr-5 rounded-md my-2 flex justify-between items-center">
                <span class="text-white">Gagal Mengubah Data</span>
                <span class="text-white cursor-pointer" onclick="document.getElementById('failed').remove()">x</span>
            </div>
        <?php endif; endif; ?>
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2 text-center">ID</th>
                <th class="px-4 py-2 text-center">Mata Praktikum</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php 
                    if (!empty($result)) :
             ?>
             <?php foreach ($result as $row) : ?>
            <tr class="border-t">
                <td class="px-4 py-2 text-center"><?php echo $row["id"] ?></td>
                <td class="px-4 py-2 text-center"><?php echo $row["nama"] ?></td>
                <td class="px-4 py-2 space-x-2 text-center">
                    <button class="btnEdit text-yellow-600 hover:underline" data-id="<?php echo $row["id"] ?>" data-nama="<?php echo $row["nama"] ?>" data-modal-target="modalEdit" data-modal-toggle="modalEdit">Edit</button>
                    <button class="btnDelete text-red-600 hover:underline" data-id="<?php echo $row["id"] ?>" data-modal-target="modalDelete" data-modal-toggle="modalDelete">Hapus</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else :?>
            <tr class="border-t">
                <td class="px-4 py-2 text-center" colspan="3">Tidak ada data mata praktikum</td>
            </tr>
            <?php endif;?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Modul -->
<div id="modalAdd" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Tambah Mata Praktikum</h3>
        <form method="POST" action="matkul.php">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Praktikum</label>
                <input type="text" name="nama" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="flex justify-end space-x-2">
            <button type="submit" name="add" class="px-4 w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            <button type="button" class="px-4 w-full py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700" 
            onclick="document.getElementById('modalAdd').classList.add('hidden')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Modul -->
<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Mata Praktikum</h3>
        <form method="POST" action="matkul.php">
        <input type="hidden" name="id" id="idEdit">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Praktikum</label>
                <input type="text" name="nama" id="editData" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="flex justify-end space-x-2">
            <button type="submit" name="edit" class="px-4 w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            <button type="button" class="px-4 w-full py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700" data-modal-target="modalEdit" data-modal-toggle="modalEdit">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete Modul -->
<div id="modalDelete" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Hapus Mata Praktikum</h3>
        <form method="POST" action="matkul.php">
        <input type="hidden" name="id" id="idDelete">
            <div class="mb-4">
                Apakah anda yakin ingin menghapus data ?
            </div>
            <div class="flex justify-end space-x-2">
            <button type="submit" name="delete" class="px-4 w-full py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Yakin</button>
            <button type="button" class="px-4 w-full py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700" data-modal-target="modalDelete" data-modal-toggle="modalDelete">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    const edit = document.getElementById("editData");
    const id = document.getElementById("idEdit");
    const btn = document.getElementsByClassName("btnEdit");
    const btnDel = document.getElementsByClassName("btnDelete");
    const idDel = document.getElementById("idDelete");


    Array.from(btn).forEach(b => {
    b.addEventListener("click", () => {
        edit.value = b.dataset.nama
        id.value = b.dataset.id
    });

    Array.from(btnDel).forEach(b => {
        b.addEventListener("click", () => {
            idDel.value = b.dataset.id
        })
    })

});

</script>

<?php
require_once 'templates/footer.php';
?>