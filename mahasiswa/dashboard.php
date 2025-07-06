<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$idUser = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) FROM enrollment WHERE idMahsiswa = ?");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$stmt->bind_result($praktikumDiikuti);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM submission WHERE idUser = ? AND status = 'selesai'");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$stmt->bind_result($tugasSelesai);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM submission WHERE idUser = ? AND status = 'belum'");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$stmt->bind_result($tugasBelum);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM notification WHERE idUser = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?= htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?= $praktikumDiikuti ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?= $tugasSelesai ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?= $tugasBelum ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        <?php if (!empty($notifications)) : ?>
            <?php foreach ($notifications as $notif): ?>
                <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
                    <span class="text-xl mr-4">
                        <?php
                        echo match ($notif['type']) {
                            'nilai' => '🎓',
                            'batas_waktu' => '⏰',
                            'pendaftaran' => '✅',
                            default => '🔔'
                        };
                        ?>
                    </span>
                    <div>
                        <?= htmlspecialchars($notif['pesan']) ?>
                        <?php if (!empty($notif['link'])): ?>
                            - <a href="http://localhost:8080/SistemPengumpulanTugas/<?= htmlspecialchars($notif['link']) ?>" class="font-semibold text-blue-600 hover:underline">Lihat</a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach ?>
        <?php else: ?>
            <li class="text-gray-500">Belum ada notifikasi.</li>
        <?php endif; ?>
    </ul>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
