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

// Ambil data penyetoran dari database
$id_nasabah = $_SESSION['id_nasabah'];
$query = "SELECT p.*, s.jenis_sampah 
        FROM penyetoran p 
        JOIN sampah s ON p.id_sampah = s.id_sampah 
        WHERE p.id_nasabah = ? 
        ORDER BY p.tanggal DESC";

// Persiapkan query
$stmt = $koneksi->prepare($query);

// Cek apakah prepare berhasil
if (!$stmt) {
    die("Prepare failed: " . $koneksi->error);
}

// Bind parameter dan eksekusi query
$stmt->bind_param('i', $id_nasabah);
$stmt->execute();

// Ambil hasil query
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>SAMADA - Riwayat Penyetoran</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            font-size: 16px;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #dfdfdf;
        }

        * {
            box-sizing: border-box;
            margin: 0px;
            padding: 0px;
        }

        #leluhur-kontainer {
            display: grid;
            grid-template-rows: auto 1fr;
            min-height: 100vh;
        }

        /* HEADER */
        #header-kontainer {
            display: grid;
            grid-template-columns: auto auto 1fr;
            gap: 1rem;
            background-image: linear-gradient(to right, #B4CECC, #397572);
            padding: 1rem;
        }

        #logo-kontainer {
            width: 60px;
            height: 60px;
            position: relative;
        }

        #logo-kontainer>img {
            width: 200%;
            height: 100%;
            object-fit: contain;
            margin-left: 0.7rem;
        }

        #nama-aplikasi {
            display: flex;
            align-items: center;
            font-size: 1.8rem;
            font-weight: 900;
        }

        #navigasi-kontainer {
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
            align-items: center;
            gap: 1.5rem;
            font-size: 1rem;
        }

        #navigasi-kontainer>a {
            color: white;
            text-decoration: none;
            transition: color 0.2s ease;
            padding: 0.5rem 1rem;
        }

        #navigasi-kontainer>a:hover {
            color: #182322;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        /* Content */
        .content {
            padding: 2rem;
        }

        .content-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            margin-bottom: 1.5rem;
            margin-left: 0.1rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-left img {
            height: 30px;
            width: auto;
        }

        .header-left h2 {
            font-size: 1.1rem;
            font-weight: 900;
        }

        .notification-icon {
            font-size: 1.5rem;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        .notification-icon:hover {
            background-color: rgba(57, 117, 114, 0.1);
        }

        /* Table */
        .table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 0 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 1rem;
            text-align: center
        }

        th {
            background-color: #397572;
            color: white;
            font-weight: 700;
        }

        td {
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 160px;
            right: 70px;
            background-color: #333;
            color: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 300px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .notification h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }

        .notification p {
            margin-top: 6px;
            font-size: 14px;
            text-align: justify;
        }

        .notification .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .notification .close-btn:hover {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="leluhur-kontainer">
        <div id="header-kontainer">
            <div id="logo-kontainer">
                <img src="logo (1).png" alt="Logo SAMADA" />
            </div>
            <div id="nama-aplikasi">
                <p>SAMADA</p>
            </div>
            <nav id="navigasi-kontainer">
                <a href="berandaNasabah.php">BERANDA</a>
                <a href="informasiSampah.php">INFORMASI SAMPAH</a>
                <a href="riwayatPenyetoran.php">RIWAYAT PENYETORAN</a>
                <a href="profilNasabah.php">PROFIL</a>
            </nav>
        </div>

        <div class="content">
            <div class="content-header">
                <div class="header-left">
                    <img src="Logo2.png" alt="Logo Penyetoran" />
                    <h2>RIWAYAT PENSETORAN</h2>
                </div>
                <div class="notification-icon" onclick="toggleNotification()">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis Sampah</th>
                            <th>Berat</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo $no++; ?>.</td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis_sampah']); ?></td>
                                <td><?php echo number_format($row['berat'], 1); ?>kg</td>
                                <td>Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($result->num_rows == 0): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Belum ada riwayat penyetoran</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="notification">
            <h4>Pemberitahuan</h4>
            <p><strong>Informasi Penarikan Dana</strong></p>
            <p>
                Hai, <?php echo htmlspecialchars($_SESSION['nama_nasabah']); ?>!
                Uang hasil setoran sampah anda dapat diambil jika admin sudah menghubungimu!
            </p>
            <i class="fas fa-times close-btn" onclick="toggleNotification()"></i>
        </div>
    </div>

    <script>
        function toggleNotification() {
            const notification = document.querySelector(".notification");
            notification.style.display = notification.style.display === "none" ||
                notification.style.display === "" ? "block" : "none";
        }
    </script>
</body>

</html>
