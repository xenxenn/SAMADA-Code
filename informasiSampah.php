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

// Handle pencarian
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $koneksi->real_escape_string($_GET['search']);
    $query = "SELECT * FROM sampah WHERE status = 'aktif' AND jenis_sampah LIKE '%$search_query%' ORDER BY id_sampah";
} else {
    $query = "SELECT * FROM sampah WHERE status = 'aktif' ORDER BY id_sampah";
}

$sampah_data = $koneksi->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>SAMADA - Informasi Sampah</title>
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
            padding: 3rem;
        }

        .content-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            margin-left: 1.2rem;
        }

        .content-header i {
            font-size: 1.3rem;
            margin-right: 1rem;
            color: rgb(10, 11, 11);
        }

        .content-header h2 {
            font-size: 1.1rem;
            font-weight: 900;
        }

        /* Search Bar */
        .search-container {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            max-width: 400px;
        }

        .search-container input {
            background-color: #dfdfdf;
            width: 100%;
            padding: 0.7rem;
            border: 1.5px solid #397572;
            border-radius: 8px;
            font-size: 1rem;
            margin-left: 1rem;
        }

        .search-container input:focus {
            outline: none;
            border-color: #276d68;
            box-shadow: 0 0 5px rgba(57, 117, 114, 0.2);
        }

        /* Table */
        .table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-left: 1rem;
            margin-right: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #397572;
            color: white;
            font-weight: 700;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        tr:last-child td {
            border-bottom: none;
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
                <i class="far fa-file-alt"></i>
                <h2>INFORMASI SAMPAH</h2>
            </div>

            <div class="search-container">
                <input
                    type="text"
                    placeholder="Cari Jenis Sampah..."
                    id="searchInput"
                    value="<?php echo htmlspecialchars($search_query); ?>" />
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Sampah</th>
                            <th>Harga per Kg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($sampah_data && $sampah_data->num_rows > 0) {
                            while ($row = $sampah_data->fetch_assoc()) {
                        ?>
                            <tr>
                                <td width="10%"><?php echo $no++; ?>.</td>
                                <td><?php echo htmlspecialchars($row['jenis_sampah']); ?></td>
                                <td>Rp <?php echo number_format($row['harga_per_kg'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php 
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">Tidak ada data sampah yang ditemukan</td>
                            </tr>
                        <?php 
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Live search dengan debouncing
        let timeoutId;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                window.location.href = `informasiSampah.php?search=${this.value}`;
            }, 500); // Menunggu 500ms setelah user selesai mengetik
        });
    </script>
</body>
</html>