<?php

require_once "../config.php";

    function inserData($nama) {
        global $conn;

        $query = "INSERT INTO matkul(nama) VALUES(?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nama);
        
        if ($stmt->execute()) {
            header("Location: praktikum.php?status=success");
            exit();
        } else {
            header("Location: praktikum.php?status=failed");
            exit();
        }
        
    }

    function updateData($nama, $id) {
        global $conn;

        $query = "UPDATE matkul SET nama = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nama, $id);
        
        if ($stmt->execute()) {
            header("Location: praktikum.php?status=success");
            exit();
        } else {
            header("Location: praktikum.php?status=failed");
            exit();
        }
        
    }

    function deleteData($id) {
        global $conn;

        $query = "DELETE FROM matkul WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: praktikum.php?status=success");
            exit();
        } else {
            header("Location: praktikum.php?status=failed");
            exit();
        }
        
    }

    function getData() {
        global $conn;

        $query = "SELECT * FROM matkul ORDER BY id ASC";

        $result = $conn->query($query);

        $data = [];
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if ($result->num_rows > 0) {
            return array(
                "status" => 200,
                "data" => $data
            );
        } else {
            return array(
                "status" => 500,
                "message" => "Gagal Mengambil Data Mata Praktikum"
            );
        }

    }

    if (isset($_POST["add"])) {
        $nama = $_POST["nama"];
        inserData($nama);
    }
    else if (isset($_POST["edit"])) {
        $nama = $_POST["nama"];
        $id = $_POST["id"];
        updateData($nama, $id);
    }
    else if (isset($_POST["delete"])) {
        $id = $_POST["id"];
        deleteData($id);
    }

?>