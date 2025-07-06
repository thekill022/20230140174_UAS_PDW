<?php
require_once "../config.php";

$upload_dir = "../uploads/";

function getFilePathById($id) {
    global $conn;
    $query = "SELECT path FROM modul WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($path);
    $stmt->fetch();
    $stmt->close();
    return $path;
}

function inserModulData($judul, $idMatkul, $path) {
    global $conn;

    $query = "INSERT INTO modul(judul, idMatkul, path) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sis", $judul, $idMatkul, $path);

    if ($stmt->execute()) {
        $new_modul_id = $conn->insert_id;

        $sqlEnroll = $conn->prepare("SELECT idMahsiswa FROM enrollment WHERE idMatkul = ?");
        $sqlEnroll->bind_param("i", $idMatkul);
        $sqlEnroll->execute();
        $result = $sqlEnroll->get_result();

        $stmtSub = $conn->prepare("INSERT INTO submission (idUser, idModul, status) VALUES (?, ?, 'belum')");
        while ($row = $result->fetch_assoc()) {
            $idUser = $row['idMahsiswa'];
            $stmtSub->bind_param("ii", $idUser, $new_modul_id);
            $stmtSub->execute();
        }

        header("Location: modul.php?status=success");
        exit();
    } else {
        header("Location: modul.php?status=failed");
        exit();
    }
}


function updateModulData($judul, $idMatkul, $path, $id) {
    global $conn, $upload_dir;

    $oldFile = getFilePathById($id);

    if (!empty($path)) {
        if ($oldFile && file_exists($upload_dir . $oldFile)) {
            unlink($upload_dir . $oldFile);
        }
        $query = "UPDATE modul SET judul = ?, idMatkul = ?, path = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $judul, $idMatkul, $path, $id);
    } else {
        $query = "UPDATE modul SET judul = ?, idMatkul = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $judul, $idMatkul, $id);
    }

    if ($stmt->execute()) {
        header("Location: modul.php?status=success");
        exit();
    } else {
        header("Location: modul.php?status=failed");
        exit();
    }
}

function deleteModulData($id) {
    global $conn, $upload_dir;

    $path = getFilePathById($id);

    $query = "DELETE FROM modul WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($path && file_exists($upload_dir . $path)) {
            unlink($upload_dir . $path);
        }

        header("Location: modul.php?status=success");
        exit();
    } else {
        header("Location: modul.php?status=failed");
        exit();
    }
}

function getModulData() {
    global $conn;
    $query = "SELECT modul.*, matkul.nama FROM modul LEFT JOIN matkul ON modul.idMatkul = matkul.id ORDER BY judul ASC";
    $result = $conn->query($query);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $result->num_rows > 0
        ? ["status" => 200, "data" => $data]
        : ["status" => 500, "message" => "Gagal Mengambil Data Modul"];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST["judul"];
    $idMatkul = $_POST["praktikum"];
    $path = time() . "_" . basename($_FILES['file']['name']);
    $upload_dir = "../uploads/";

    if (!empty($_FILES['file']['name'])) {
        $path = basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $path);
    } else {
        $path = '';
    }

    if (isset($_POST["add"])) {
        inserModulData($judul, $idMatkul, $path);
    } else if (isset($_POST["edit"])) {
        $id = $_POST["id"];
        updateModulData($judul, $idMatkul, $path, $id);
    } else if (isset($_POST["delete"])) {
        $id = $_POST["id"];
        deleteModulData($id);
    }
}
?>
