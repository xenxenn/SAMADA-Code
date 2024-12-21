<?php
session_start();

// Cek apakah user sudah login sebagai nasabah
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'nasabah') {
    header("Location: ../login.php");
    exit();
}

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "samada");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data nasabah dari database berdasarkan id_nasabah dalam session
$id_nasabah = $_SESSION['id_nasabah'];
$query = "SELECT * FROM nasabah WHERE id_nasabah = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param('i', $id_nasabah);
$stmt->execute();
$result = $stmt->get_result();
$nasabah = $result->fetch_assoc();

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location:homepage.html"); // Arahkan ke homepage
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Profil Nasabah</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #333333;
            /* Warna abu-abu tua */
            font-family: Arial, sans-serif;
        }

        .profile-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-header i {
            font-size: 24px;
            margin-right: 10px;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .profile-content {
            display: flex;
            align-items: center;
        }

        .profile-image {
            flex: 1;
            text-align: center;
        }

        .profile-details {
            flex: 2;
            padding-left: 20px;
        }

        .profile-details div {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        .profile-details label {
            font-weight: bold;
            width: 100px;
        }

        .profile-details span {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            flex: 1;
            text-align: left;
        }

        .logout-button {
            display: block;
            width: 100px;
            margin: 20px auto 0;
            padding: 10px;
            background-color: #008080;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #006666;
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <div class="profile-header">
            <a href="berandaNasabah.php" style="color: black; text-decoration: none;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2>Profil</h2>
        </div>
        <div class="profile-content">
            <div class="profile-image">
                <img alt="Profile image placeholder" height="100" src="profile (1).png" width="150" />
                <form method="POST">
                    <button type="submit" name="logout" class="logout-button">LOG OUT</button>
                </form>
            </div>
            <div class="profile-details">
                <div>
                    <label>Nama</label>
                    <span><?php echo htmlspecialchars($nasabah['nama_nasabah']); ?></span>
                </div>
                <div>
                    <label>NIK</label>
                    <span><?php echo htmlspecialchars($nasabah['nik']); ?></span>
                </div>
                <div>
                    <label>Username</label>
                    <span><?php echo htmlspecialchars($nasabah['username']); ?></span>
                </div>
                <div>
                    <label>No. Telp</label>
                    <span><?php echo htmlspecialchars($nasabah['no_telp']); ?></span>
                </div>
                <div>
                    <label>Alamat</label>
                    <span><?php echo htmlspecialchars($nasabah['alamat']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Konfirmasi sebelum logout
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin keluar?')) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>