<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header.php';
require_once '../config.php';

$totalModul = $conn->query("SELECT COUNT(*) AS total FROM modul")->fetch_assoc()['total'];

$totalLaporan = $conn->query("SELECT COUNT(*) AS total FROM submission")->fetch_assoc()['total'];

$totalBelumDinilai = $conn->query("SELECT COUNT(*) AS total FROM submission WHERE status != 'selesai'")->fetch_assoc()['total'];

$aktivitas = $conn->query("
    SELECT s.*, u.nama AS nama_user, m.judul AS judul_modul
    FROM submission s
    JOIN users u ON u.id = s.idUser
    JOIN modul m ON m.id = s.idModul
    WHERE s.status = 'selesai'
    ORDER BY s.submitted_at DESC
");
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-blue-100 p-3 rounded-full">
        <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Modul Diajarkan</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalModul ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-100 p-3 rounded-full">
        <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalLaporan ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-100 p-3 rounded-full">
        <svg class="w-6 h-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Laporan Belum Dinilai</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalBelumDinilai ?></p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>

    <div class="space-y-4">
        <?php while($row = $aktivitas->fetch_assoc()): ?>
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                    <span class="font-bold text-gray-500"><?= strtoupper(substr($row['nama_user'], 0, 2)) ?></span>
                </div>
                <div>
                    <p class="text-gray-800">
                        <strong><?= htmlspecialchars($row['nama_user']) ?></strong> mengumpulkan laporan untuk <strong><?= htmlspecialchars($row['judul_modul']) ?></strong>
                    </p>
                    <p class="text-sm text-gray-500"><?= timeAgo($row['submitted_at']) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php
function timeAgo($time) {
    date_default_timezone_set('Asia/Jakarta');
    $time = strtotime($time);
    $diff = time() - $time;

    if ($diff < 0) return "baru saja";

    if ($diff < 60) return "$diff detik lalu";
    if ($diff < 3600) return floor($diff / 60) . " menit lalu";
    if ($diff < 86400) return floor($diff / 3600) . " jam lalu";
    return date('d M Y, H:i', $time);
}

?>
<?php require_once 'templates/footer.php'; ?>
