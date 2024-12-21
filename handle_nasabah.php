<?php
// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "samada");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            try {
                $nama = $koneksi->real_escape_string($_POST['nama_nasabah']);
                $nik = $koneksi->real_escape_string($_POST['nik']);
                $alamat = $koneksi->real_escape_string($_POST['alamat']);
                $noTelp = $koneksi->real_escape_string($_POST['no_telp']);
                $username = $koneksi->real_escape_string($_POST['username']);
                $sandi = $koneksi->real_escape_string($_POST['sandi']);

                $query = "INSERT INTO nasabah (nama_nasabah, nik, alamat, no_telp, username, sandi) 
                        VALUES (?, ?, ?, ?, ?, ?)";

                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("ssssss", $nama, $nik, $alamat, $noTelp, $username, $sandi);

                if ($stmt->execute()) {
                    $newId = $koneksi->insert_id;

                    // Ambil data yang baru diinsert
                    $query = "SELECT * FROM nasabah WHERE id_nasabah = ?";
                    $stmt = $koneksi->prepare($query);
                    $stmt->bind_param("i", $newId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $newData = $result->fetch_assoc();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Data nasabah berhasil ditambahkan',
                        'data' => $newData
                    ]);
                } else {
                    throw new Exception($koneksi->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            break;

        case 'update':
            try {
                $id = $koneksi->real_escape_string($_POST['id_nasabah']);
                $nama = $koneksi->real_escape_string($_POST['nama_nasabah']);
                $nik = $koneksi->real_escape_string($_POST['nik']);
                $alamat = $koneksi->real_escape_string($_POST['alamat']);
                $noTelp = $koneksi->real_escape_string($_POST['no_telp']);
                $username = $koneksi->real_escape_string($_POST['username']);

                $query = "UPDATE nasabah SET 
                        nama_nasabah = ?, 
                        nik = ?, 
                        alamat = ?, 
                        no_telp = ?, 
                        username = ?";

                $params = [$nama, $nik, $alamat, $noTelp, $username];
                $types = "sssss";

                if (!empty($_POST['sandi'])) {
                    $sandi = $koneksi->real_escape_string($_POST['sandi']);
                    $query .= ", sandi = ?";
                    $params[] = $sandi;
                    $types .= "s";
                }

                $query .= " WHERE id_nasabah = ?";
                $params[] = $id;
                $types .= "i";

                $stmt = $koneksi->prepare($query);
                $stmt->bind_param($types, ...$params);

                if ($stmt->execute()) {
                    // Ambil data yang sudah diupdate
                    $query = "SELECT * FROM nasabah WHERE id_nasabah = ?";
                    $stmt = $koneksi->prepare($query);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $updatedData = $result->fetch_assoc();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Data nasabah berhasil diperbarui',
                        'data' => $updatedData
                    ]);
                } else {
                    throw new Exception($koneksi->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            break;

        case 'delete':
            try {
                $id = $koneksi->real_escape_string($_POST['id_nasabah']);
                $query = "DELETE FROM nasabah WHERE id_nasabah = ?";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Data nasabah berhasil dihapus'
                    ]);
                } else {
                    throw new Exception($koneksi->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            break;
    }
}
