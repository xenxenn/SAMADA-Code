<?php
// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "samada");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil id_nasabah dari parameter URL
$id_nasabah = isset($_GET['id']) ? $_GET['id'] : null;

// Ambil data nasabah
$nasabah = null;
if ($id_nasabah) {
    $query = "SELECT * FROM nasabah WHERE id_nasabah = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_nasabah);
    $stmt->execute();
    $nasabah = $stmt->get_result()->fetch_assoc();
}

// Ambil data sampah untuk dropdown

$query_sampah = "SELECT * FROM sampah WHERE status = 'aktif'";
$sampah_result = $koneksi->query($query_sampah);
$sampah_options = [];
while ($row = $sampah_result->fetch_assoc()) {
    $sampah_options[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    switch ($_POST['action']) {
        case 'add':
            $id_sampah = $koneksi->real_escape_string($_POST['jenis_sampah']);
            $berat = $koneksi->real_escape_string($_POST['berat']);
            $tanggal = $koneksi->real_escape_string($_POST['tanggal']);

            // Hitung total harga
            $query_harga = "SELECT harga_per_kg FROM sampah WHERE id_sampah = ?";
            $stmt = $koneksi->prepare($query_harga);
            $stmt->bind_param("i", $id_sampah);
            $stmt->execute();
            $harga_result = $stmt->get_result()->fetch_assoc();
            $total_harga = $harga_result['harga_per_kg'] * $berat;

            // Insert data penyetoran
            $query = "INSERT INTO penyetoran (id_nasabah, id_sampah, berat, total_harga, tanggal) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("iidds", $id_nasabah, $id_sampah, $berat, $total_harga, $tanggal);

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'DATA PENYETORAN BERHASIL DITAMBAHKAN!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menambahkan data: ' . $koneksi->error
                ]);
            }
            exit;
    }
}

// Ambil data penyetoran
$query = "SELECT p.*, s.jenis_sampah, s.harga_per_kg 
          FROM penyetoran p 
          JOIN sampah s ON p.id_sampah = s.id_sampah 
          WHERE p.id_nasabah = ? AND s.status = 'aktif'
          ORDER BY p.tanggal DESC";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_nasabah);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

        /* KONTEN */
        #konten-kontainer {
            padding: 2rem;
        }

        #wraper-section {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-rows: auto auto 1fr;
            gap: 1rem;
        }

        #bagian-atas {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem;
            margin-left: 0.7rem;
        }

        #bagian-atas i {
            font-size: 1rem;
            color: rgb(17, 31, 30);
        }

        #bagian-atas p {
            font-size: 1rem;
            font-weight: 900;
            color: rgb(16, 28, 28);
        }

        #setor-sampah {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
        }

        #setor-sampah p {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        #simbol-tambah {
            background-color: #348B86;
            color: white;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #simbol-tambah:hover {
            background-color: #014d49;
        }

        /* Tabel */
        #bagian-tabel {
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 0.8rem;
            padding: 0 1rem;
        }

        #header-data {
            display: grid;
            grid-template-columns: 80px minmax(200px, 1fr) minmax(150px, 1fr) 180px;
            background-image: linear-gradient(to right, #339893, #113230);
            color: white;
            border-radius: 10px;
            overflow: hidden;
            font-weight: bolder;
        }

        .header-tanggal {
            padding: 1rem;
            text-align: left;
            margin-left: 3.4rem;
        }

        .header-jenis {
            padding: 1rem;
            text-align: left;
            margin-left: 16.5rem;
        }

        .header-berat {
            padding: 1rem;
            text-align: left;
            margin-left: 11rem;
        }

        .header-total {
            padding: 1rem;
            margin-left: 2.8rem;
        }

        #list-data {
            background-color: #ffff;
            border-radius: 10px;
            padding: 0.4rem;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
        }

        .custom-table tr {
            display: grid;
            grid-template-columns: 80px minmax(200px, 1fr) minmax(150px, 1fr) 80px;
            padding: 0.5rem;
        }

        .custom-table td {
            padding: 0.5rem;
            display: flex;
            align-items: center;
        }

        .list-tanggal {
            justify-content: center;
            margin-left: 5.2rem;
        }

        .list-jenis {
            justify-content: center;
            margin-left: 5.3rem;
        }

        .list-berat {
            justify-content: center;
            margin-right: 9.3rem;
        }

        .list-total {
            justify-content: center;
            margin-right: 22rem;
        }

        /* Modal */
        #kontainer-pop-up {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #kontainer-isi-data {
            background-color: white;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        #judul-pop-up {
            font-size: 1.5rem;
            color: #075450;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .text-box-pop-up {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            border: 2px solid #ACACAC;
            border-radius: 8px;
            font-size: 1rem;
        }

        #button-data-pop-up {
            width: 100%;
            padding: 0.8rem;
            background-color: #348B86;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #button-data-pop-up:hover {
            background-color: #014d49;
        }

        .close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #000;
        }

        @media screen and (max-width: 768px) {
            .header-cell {
                padding: 1rem;
                font-size: 0.9rem;
            }

            .custom-table td {
                padding: 1rem;
                font-size: 0.9rem;
            }

            #kontainer-isi-data {
                width: 95%;
                padding: 1.5rem;
            }
        }

        .alert {
            padding: 1rem;
            margin: 0.5rem;
            border-radius: 8px;
            display: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        /* Styling untuk select dropdown */
        .select-sampah {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            border: 2px solid #ACACAC;
            border-radius: 8px;
            font-size: 1rem;
            background-color: white;
        }
    </style>
</head>

<body>
    <div id="leluhur-kontainer">
        <div id="header-kontainer">
            <div id="logo-kontainer">
                <img src="logo-aplikasi.png" alt="Logo SAMADA" />
            </div>
            <div id="nama-aplikasi">
                <p>SAMADA</p>
            </div>
            <nav id="navigasi-kontainer">
                <a href="berandaAdmin.php">BERANDA</a>
                <a href="daftarNasabah.php">DAFTAR NASABAH</a>
                <a href="dataSampah.php">DATA SAMPAH</a>
                <a href="profilAdmin.php">PROFIL</a>
            </nav>
        </div>

        <div id="konten-kontainer">
            <div id="wraper-section">
                <section id="bagian-atas">
                    <i class="fas fa-users"></i>
                    <p>DAFTAR NASABAH</p>
                </section>

                <!-- Alert untuk feedback -->
                <div id="alert" class="alert"></div>

                <section id="setor-sampah">
                    <p>Penyetoran Sampah - <?php echo htmlspecialchars($nasabah['nama_nasabah']); ?></p>
                    <div id="simbol-tambah" onclick="showModal()">+</div>
                </section>

                <section id="bagian-tabel">
                    <div id="header-data">
                        <div class="header-tanggal">Tanggal</div>
                        <div class="header-jenis">Jenis Sampah</div>
                        <div class="header-berat">Berat (Kg)</div>
                        <div class="header-total">Total</div>
                    </div>
                    <div id="list-data">
                        <table class="custom-table">
                            <tbody>
                                <?php
                                if ($result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                ?>
                                        <tr>
                                            <td class="list-tanggal"><?php echo isset($row['tanggal']) ? date('d/m/Y', strtotime($row['tanggal'])) : '-'; ?></td>
                                            <td class="list-jenis"><?php echo htmlspecialchars($row['jenis_sampah']); ?></td>
                                            <td class="list-berat"><?php echo number_format($row['berat'], 1); ?> Kg</td>
                                            <td class="list-total">Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center;" just>Belum ada data penyetoran</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="kontainer-pop-up">
        <div id="kontainer-isi-data">
            <span class="close" onclick="hideModal()">&times;</span>
            <p id="judul-pop-up">Input Penyetoran Sampah</p>
            <form id="penyetoranForm" onsubmit="savePenyetoran(event)">
                <input type="hidden" name="action" value="add">
                <input type="date" name="tanggal" class="text-box-pop-up" required>
                <select name="jenis_sampah" class="select-sampah" required>
                    <option value="">Pilih Jenis Sampah</option>
                    <?php foreach ($sampah_options as $sampah): ?>
                        <option value="<?php echo $sampah['id_sampah']; ?>">
                            <?php echo htmlspecialchars($sampah['jenis_sampah']); ?> -
                            Rp<?php echo number_format($sampah['harga_per_kg'], 0, ',', '.'); ?>/kg
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="berat" step="0.1" class="text-box-pop-up"
                    placeholder="Berat (Kg)" required>
                <button type="submit" id="button-data-pop-up">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk menampilkan alert
        function showAlert(message, isSuccess) {
            const alert = document.getElementById('alert');
            alert.className = `alert ${isSuccess ? 'alert-success' : 'alert-error'}`;
            alert.textContent = message;
            alert.style.display = 'block';
            setTimeout(() => alert.style.display = 'none', 3000);
        }
        async function updateTable() {
    try {
        const response = await fetch(window.location.href);
        const text = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(text, 'text/html');
        const newTableBody = doc.querySelector('.custom-table');
        if (newTableBody) {
            document.querySelector('.custom-table').innerHTML = newTableBody.innerHTML;
            // Reinisialisasi event listener untuk dots menu
            initializeDotMenus();
        }
    } catch (error) {
        console.error('Error updating table:', error);
    }
}
        async function savePenyetoran(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, true);
                    hideModal();
                    await updateTable();
                    // Delay reload to allow user to see the success message
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert(result.message || 'Terjadi kesalahan saat menyimpan data', false);
                }
            } catch (error) {
                showAlert('Terjadi kesalahan pada sistem', false);
                console.error('Error:', error);
            }
        }

        function showModal() {
            document.getElementById('kontainer-pop-up').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function hideModal() {
            document.getElementById('kontainer-pop-up').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('kontainer-pop-up').addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideModal();
            }
        });
    </script>
</body>

</html>