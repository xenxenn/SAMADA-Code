<?php
// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "samada");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    switch ($_POST['action']) {
        case 'add':
            try {
                $jenis_sampah = $koneksi->real_escape_string($_POST['jenis_sampah']);
                $harga_per_kg = $koneksi->real_escape_string($_POST['harga_per_kg']);

                $query = "INSERT INTO sampah (jenis_sampah, harga_per_kg) VALUES (?, ?)";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("ss", $jenis_sampah, $harga_per_kg);

                if ($stmt->execute()) {
                    $newId = $koneksi->insert_id;
                    echo json_encode([
                        'success' => true,
                        'message' => 'DATA SAMPAH BERHASIL DITAMBAHKAN!',
                        'data' => ['id_sampah' => $newId]
                    ]);
                } else {
                    throw new Exception($koneksi->error);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;

        case 'update':
            try {
                $id = $koneksi->real_escape_string($_POST['id_sampah']);
                $jenis_sampah = $koneksi->real_escape_string($_POST['jenis_sampah']);
                $harga_per_kg = $koneksi->real_escape_string($_POST['harga_per_kg']);

                $query = "UPDATE sampah SET jenis_sampah = ?, harga_per_kg = ? WHERE id_sampah = ?";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("ssi", $jenis_sampah, $harga_per_kg, $id);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'DATA SAMPAH BERHASIL DIPERBARUI!']);
                } else {
                    throw new Exception($koneksi->error);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;

        case 'delete':
            try {
                $id = $koneksi->real_escape_string($_POST['id_sampah']);

                // Update status menjadi nonaktif alih-alih menghapus
                $query = "UPDATE sampah SET status = 'nonaktif' WHERE id_sampah = ?";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'DATA SAMPAH BERHASIL DIHAPUS!']);
                } else {
                    throw new Exception($koneksi->error);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
    }
}

// Ambil data sampah yang aktif saja
$sampah_data = $koneksi->query("SELECT * FROM sampah WHERE status = 'aktif'");

// Cek apakah ada pencarian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search = $koneksi->real_escape_string($_POST['search']);
    $sampah_data = $koneksi->query("SELECT * FROM sampah WHERE status = 'aktif' AND jenis_sampah LIKE '%$search%'");
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            gap: 1.3rem;
        }

        /* Bagian Atas dengan Ikon */
        #bagian-atas {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem;
        }

        #bagian-atas i {
            font-size: 1.2rem;
            color: #000000;
            margin-left: 1rem;
        }

        #bagian-atas p {
            font-size: 1rem;
            font-weight: 900;
            color: #000000;
        }

        /* Pencarian dan Tambah */
        #bagian-pencarian {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding: 0 1rem;
        }

        #kontainer-field-pencarian {
            flex: 1;
            max-width: 450px;
            display: flex;
            align-items: center;
            background-color: #DFDFDF;
            border: 1.5px solid #075450;
            border-radius: 15px;
            padding: 0rem 4rem;
        }

        .transparent-input {
            width: 80%;
            padding: 0.5rem;
            border: none;
            background: transparent;
            outline: none;
            font-size: 1rem;
        }

        #simbol-tambah i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            background-color: #348B86;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.5rem;
            transition: transform 0.2s ease;
        }

        #simbol-tambah:hover {
            transform: scale(1.10);
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
            grid-template-columns: 80px minmax(200px, 1fr) minmax(150px, 1fr) 80px;
            background-image: linear-gradient(to right, #339893, #113230);
            color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .header-nomor {
            padding: 1rem 1rem;
            text-align: left;
            margin-left: 2.1rem;
        }

        .header-jenis {
            padding: 1rem 1rem;
            text-align: left;
            margin-left: 2.4rem;
        }

        .header-harga {
            padding: 1rem 1rem;
            text-align: left;
            margin-left: 1.9rem;
        }

        #list-data {
            background-color: #ffff;
            border-radius: 10px;
            padding: 0.5rem;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table tr {
            display: grid;
            grid-template-columns: 80px minmax(200px, 1fr) minmax(150px, 1fr) 80px;
            padding: 0.5rem 0;
        }

        .custom-table td {
            padding: 0.5rem 3.1rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
        }

        .custom-table td:first-child {
            justify-content: center;
        }

        .custom-table td:last-child {
            justify-content: center;
        }

        .dots-container {
            position: relative;
            display: inline-block;
        }

        .dots {
            cursor: pointer;
            font-size: 1.5rem;
            color: #666;
            transition: color 0.3s ease;
            transition: 0.2rem 0.5rem;
        }

        .dots:hover {
            color: #348B86;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: #339893;
            min-width: 120px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            z-index: 1;
            overflow: hidden;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 0.8rem 1rem;
            color: #333;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #4db2ad;
        }

        .dropdown-item {
            color: #ffffff;
        }

        /* Pop-up styling tetap sama seperti sebelumnya */
        #kontainer-pop-up,
        #kontainer-pop-up-edit {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1100;
        }

        #kontainer-isi-data {
            background-color: white;
            padding: 2rem;
            border-radius: 12px;
            width: 29rem;
            max-width: 600px;
            position: relative;
        }

        .close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        #judul-pop-up {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #075450;
            font-weight: bold;
        }

        #kontainer-isi-data form input,
        #kontainer-isi-data form button {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px solid #ACACAC;
            border-radius: 8px;
            font-size: 1rem;
        }

        #kontainer-isi-data form button {
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            background-color: #348B86;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            float: right;
            width: 10rem;
            font-weight: bolder;
        }

        #kontainer-isi-data form button:hover {
            background-color: rgb(67, 128, 125);
        }

        @media screen and (max-width: 768px) {
            #header-kontainer {
                grid-template-columns: auto 1fr auto;
            }

            .header-cell {
                padding: 1rem;
            }

            .custom-table td {
                padding: 1rem;
            }
        }

        @media screen and (max-width: 480px) {
            #bagian-pencarian {
                flex-direction: column;
                align-items: stretch;
            }

            #kontainer-field-pencarian {
                max-width: none;
            }

            #simbol-tambah {
                align-self: flex-end;
            }
        }

        /* Styling untuk alert */
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
                    <i class="fas fa-clipboard-list"></i>
                    <p>LIHAT DATA SAMPAH</p>
                </section>

                <div id="alert" class="alert"></div>

                <section id="bagian-pencarian">
                    <form id="searchForm" method="POST">
                        <div id="kontainer-field-pencarian">
                            <input id="searchInput" name="search" type="text" placeholder="Cari Jenis Sampah..." class="transparent-input">
                        </div>
                        <button type="submit" style="display:none;">Cari</button> <!-- Tombol pencarian yang tidak terlihat -->
                    </form>
                    <div class="simbol-tambah" id="simbol-tambah" onclick="showPopup()"><i class="fas fa-plus"></i></div>
                </section>

                <!-- Tabel data sampah -->
                <section id="bagian-tabel">
                    <div id="header-data">
                        <div class="header-nomor" id="No">No</div>
                        <div class="header-jenis" id="Jenis-Sampah">Jenis Sampah</div>
                        <div class="header-harga" id="Harga-perKg">Harga per Kg</div>
                    </div>
                    <div id="list-data">
                        <table class="custom-table" id="nasabahTableBody">
                            <?php
                            $no = 1;
                            while ($row = $sampah_data->fetch_assoc()) {
                                echo "<tr data-id=\"{$row['id_sampah']}\">
                                    <td>{$no}</td>
                                    <td>{$row['jenis_sampah']}</td>
                                    <td>{$row['harga_per_kg']}</td>
                                    <td>
                                        <div class='dots-container'>
                                            <i class='dots fa fa-ellipsis-v'></i>
                                            <div class='dropdown-menu'>
                                                <a href='javascript:void(0)' onclick='deleteSampah({$row['id_sampah']})' class='dropdown-item'>Hapus</a>
                                                <a href='javascript:void(0)' class='dropdown-item' onclick='showEditForm({$row['id_sampah']}, \"{$row['jenis_sampah']}\", \"{$row['harga_per_kg']}\")'>Edit</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>";
                                $no++;
                            }
                            ?>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Pop-up Form -->
    <div id="kontainer-pop-up">
        <div id="kontainer-isi-data">
            <span class="close" onclick="document.getElementById('kontainer-pop-up').style.display = 'none'">&times;</span>
            <h2 id="judul-pop-up">Input Data Sampah</h2>
            <form method="POST" onsubmit="saveSampah(event)">
                <input type="text" name="jenis_sampah" placeholder="Jenis Sampah" required />
                <input type="text" name="harga_per_kg" placeholder="Harga per KG" required />
                <button type="submit" name="tambah">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Modal Pop-up Edit -->
    <div id="kontainer-pop-up-edit">
        <div id="kontainer-isi-data">
            <span class="close" onclick="document.getElementById('kontainer-pop-up-edit').style.display = 'none'">&times;</span>
            <h2 id="judul-pop-up">Edit Data Sampah</h2>
            <form method="POST" onsubmit="editSampah(event)">
                <input type="hidden" name="id_sampah" id="id_sampah">
                <label for="jenis_sampah">Jenis Sampah:</label>
                <input type="text" id="jenis_sampah" name="jenis_sampah" required>

                <label for="harga_per_kg">Harga per kg:</label>
                <input type="text" id="harga_per_kg" name="harga_per_kg" required>

                <button type="submit" name="edit">Simpan</button>
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

        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const tableBody = doc.querySelector('.custom-table');
                    if (tableBody) {
                        document.querySelector('.custom-table').innerHTML = tableBody.innerHTML;
                        // Reinitialize dots event listeners
                        initializeDotMenus();
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Tambahkan event listener untuk input langsung (real-time search)
        document.getElementById('searchInput').addEventListener('input', function() {
            document.getElementById('searchForm').dispatchEvent(new Event('submit'));
        });

        // Fungsi untuk menginisialisasi menu titik-titik
        function initializeDotMenus() {
            document.querySelectorAll('.dots').forEach(dot => {
                dot.addEventListener('click', function() {
                    const menu = this.nextElementSibling;
                    menu.classList.toggle('show');
                });
            });
        }

        // Fungsi untuk menyimpan data
        async function saveSampah(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'add');

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, true);
                    hidePopup();

                    // Tambah data baru ke tabel
                    const tableBody = document.querySelector('.custom-table tbody');
                    const newRow = document.createElement('tr');
                    const rowCount = tableBody.children.length + 1;

                    newRow.innerHTML = `
                <td>${rowCount}</td>
                <td>${formData.get('jenis_sampah')}</td>
                <td>${formData.get('harga_per_kg')}</td>
                <td>
                    <div class='dots-container'>
                        <i class='dots fa fa-ellipsis-v'></i>
                        <div class='dropdown-menu'>
                            <a href='javascript:void(0)' onclick='deleteSampah(${result.data.id_sampah})' class='dropdown-item'>Hapus</a>
                            <a href='javascript:void(0)' onclick='showEditForm(${result.data.id_sampah}, "${formData.get('jenis_sampah')}", "${formData.get('harga_per_kg')}")' class='dropdown-item'>Edit</a>
                        </div>
                    </div>
                </td>
            `;
                    tableBody.insertBefore(newRow, tableBody.firstChild);
                } else {
                    showAlert(result.message, false);
                }
            } catch (error) {
                showAlert('Terjadi kesalahan', false);
            }
        }

        // Fungsi untuk edit sampah
        async function editSampah(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'update');

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, true);
                    hidePopup();

                    // Update row di tabel
                    const id = formData.get('id_sampah');
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.children[1].textContent = formData.get('jenis_sampah');
                        row.children[2].textContent = formData.get('harga_per_kg');
                    } else {
                        location.reload(); // Jika row tidak ditemukan, reload halaman
                    }
                } else {
                    showAlert(result.message, false);
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat mengedit data', false);
            }
        }

        // Fungsi untuk delete sampah
        async function deleteSampah(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id_sampah', id);

                try {
                    const response = await fetch('', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        showAlert(result.message, true);
                        // Hapus row dari tabel
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.remove();
                            // Update nomor urut
                            updateRowNumbers();
                        } else {
                            location.reload();
                        }
                    } else {
                        showAlert(result.message, false);
                    }
                } catch (error) {
                    showAlert('Terjadi kesalahan saat menghapus data', false);
                }
            }
        }

        // Fungsi untuk update nomor urut
        function updateRowNumbers() {
            const rows = document.querySelectorAll('.custom-table tr');
            rows.forEach((row, index) => {
                const numberCell = row.querySelector('td:first-child');
                if (numberCell) {
                    numberCell.textContent = index + 1;
                }
            });
        }

        function showPopup() {
            document.getElementById('kontainer-pop-up').style.display = 'flex';
        }

        // Fungsi untuk menampilkan form edit dengan data yang sudah diisi
        function showEditForm(id, jenis, harga) {
            document.getElementById('id_sampah').value = id;
            document.getElementById('jenis_sampah').value = jenis;
            document.getElementById('harga_per_kg').value = harga;
            document.getElementById('kontainer-pop-up-edit').style.display = 'flex';
        }

        function hidePopup() {
            document.getElementById('kontainer-pop-up').style.display = 'none';
            document.getElementById('kontainer-pop-up-edit').style.display = 'none';
        }

        // Close popup when clicking outside
        document.getElementById('kontainer-pop-up').addEventListener('click', function(e) {
            if (e.target === this) {
                hidePopup();
            }
        });

        document.getElementById('kontainer-pop-up-edit').addEventListener('click', function(e) {
            if (e.target === this) {
                hidePopup();
            }
        });

        document.querySelectorAll('.dots').forEach(dot => {
            dot.addEventListener('click', function() {
                const menu = this.nextElementSibling;
                menu.classList.toggle('show'); // Menampilkan dan menyembunyikan dropdown
            });
        });
    </script>
</body>

</html>