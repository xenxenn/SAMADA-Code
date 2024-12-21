<?php
// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "samada");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Handle pencarian
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $koneksi->real_escape_string($_POST['search']);
    $query = "SELECT * FROM nasabah WHERE nama_nasabah LIKE '%$search_query%' OR nik LIKE '%$search_query%'";
} else {
    $query = "SELECT * FROM nasabah";
}

// Ambil data nasabah
$result = $koneksi->query($query);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $nama = $koneksi->real_escape_string($_POST['nama_nasabah']);
                $nik = $koneksi->real_escape_string($_POST['nik']);
                $alamat = $koneksi->real_escape_string($_POST['alamat']);
                $noTelp = $koneksi->real_escape_string($_POST['no_telp']);
                $username = $koneksi->real_escape_string($_POST['username']);
                $sandi = $koneksi->real_escape_string($_POST['sandi']);

                $query = "INSERT INTO nasabah (nama_nasabah, nik, alamat, no_telp, username, sandi) 
                        VALUES ('$nama', '$nik', '$alamat', '$noTelp', '$username', '$sandi')";

                if ($koneksi->query($query)) {
                    $newId = $koneksi->insert_id;
                    echo json_encode([
                        'success' => true,
                        'message' => 'NASABAH BERHASIL DITAMBAHKAN!',
                        'id' => $newId
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => $koneksi->error
                    ]);
                }
                exit;

            case 'update':
                $id = $koneksi->real_escape_string($_POST['id_nasabah']);
                $nama = $koneksi->real_escape_string($_POST['nama_nasabah']);
                $nik = $koneksi->real_escape_string($_POST['nik']);
                $alamat = $koneksi->real_escape_string($_POST['alamat']);
                $noTelp = $koneksi->real_escape_string($_POST['no_telp']);
                $username = $koneksi->real_escape_string($_POST['username']);
                $sandi = $koneksi->real_escape_string($_POST['sandi']);

                $query = "UPDATE nasabah SET 
                        nama_nasabah='$nama', 
                        nik='$nik', 
                        alamat='$alamat', 
                        no_telp='$noTelp', 
                        username='$username', 
                        sandi='$sandi' 
                        WHERE id_nasabah='$id'";

                if ($koneksi->query($query)) {
                    echo json_encode(['success' => true, 'message' => 'NASABAH BERHASIL DIPERBARUI!']);
                } else {
                    echo json_encode(['success' => false, 'message' => $koneksi->error]);
                }
                exit;

            case 'delete':
                $id = $koneksi->real_escape_string($_POST['id_nasabah']);
                $query = "DELETE FROM nasabah WHERE id_nasabah='$id'";

                if ($koneksi->query($query)) {
                    echo json_encode(['success' => true, 'message' => 'NASABAH BERHASIL DIHAPUS!']);
                } else {
                    echo json_encode(['success' => false, 'message' => $koneksi->error]);
                }
                exit;
        }
    }
}
?>
<html>

<head>
    <title>Daftar Nasabah</title>
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

        .content {
            padding: 20px;
        }

        .content h2 {
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            margin-left: 1rem;
            font-weight: 900;
            margin-top: 1rem;
        }

        .content h2 i {
            margin-left: 1.2rem;
            margin-right: 1rem;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            margin-top: 1.2rem;
            margin-left: 2rem;
        }

        .search-bar input {
            padding: 7px;
            font-size: 16px;
            border: 1.5px solid #276d68;
            border-radius: 15px;
            width: 350px;
            background-color: #DFDFDF;
        }

        .search-bar button {
            padding: 12px;
            font-size: 18px;
            border: none;
            background-color: #397572;
            color: #fff;
            border-radius: 5px;
            margin-left: 12px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .search-bar button:hover {
            transform: scale(1.10);
        }

        .table-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        .table-actions {
            position: relative;
            text-align: right;
        }

        .table-actions i {
            cursor: pointer;
            transition: color 0.3s ease;
            transition: 0.2rem 0.5rem;
        }

        .table-actions i:hover {
            color: #348B86;
        }

        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #339893;
            z-index: 1;
            min-width: 120px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dropdown a {
            display: block;
            padding: 10px;
            color: #fff;
            text-decoration: none;
        }

        .dropdown a:hover {
            background-color: #2e5d5a;
        }

        .dropdown.show {
            display: block;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        /* Modal Content */
        .modal-content {
            position: relative;
            background: white;
            width: 28rem;
            max-width: 500px;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            /* Memastikan modal tidak terlalu tinggi */
            max-height: 90vh;
            overflow-y: auto;
            /* Animasi smooth */
            animation: modalAppear 0.3s ease-out;
        }

        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }


        .modal-content h2 {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #075450;
            font-weight: bold;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
        }

        .modal-content form input {
            margin-bottom: 10px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content form .input-group {
            display: flex;
            justify-content: space-between;
        }

        .modal-content form .input-group input {
            width: 48%;
        }

        .modal-content button {
            margin-top: 20px;
            padding: 10px;
            font-size: 16px;
            background-color: #397572;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            float: right;
            width: 100%;
        }

        .modal-content button:hover {
            background-color: rgb(55, 131, 126);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Responsif untuk layar kecil */
        @media screen and (max-width: 768px) {
            .modal-content {
                width: 95%;
                padding: 1.5rem;
                margin: 1rem;
            }

            .input-group {
                flex-direction: column;
                gap: 0.5rem;
            }

            .form-input {
                padding: 0.7rem;
            }
        }

        .password-cell {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .password-text {
            flex: 1;
        }

        .password-toggle-icon {
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .password-toggle-icon:hover {
            color: #397572;
        }

        .alert {
            display: none;
            padding: 15px;
            margin: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
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
    <div class="content">
        <h2>
            <i class="fas fa-users"> </i>
            DAFTAR NASABAH
        </h2>

        <!-- Alert untuk feedback -->
        <div id="alert" class="alert"></div>

        <div class="search-bar">
            <input id="searchInput" placeholder="    Cari Nama Nasabah..." type="text" />
            <button onclick="openModal()">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. Telp</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="nasabahTableBody">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?php echo $row['id_nasabah']; ?>">
                            <td><?php echo htmlspecialchars($row['nik']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_nasabah']); ?></td>
                            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                            <td><?php echo htmlspecialchars($row['no_telp']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="password-cell">
                                <span class="password-text"><?php echo str_repeat('*', strlen($row['sandi'])); ?></span>
                                <i class="fas fa-eye password-toggle-icon"
                                    onclick="togglePasswordVisibility(this, '<?php echo htmlspecialchars($row['sandi']); ?>')"
                                    data-hidden="true"></i>
                            </td>
                            <td class="table-actions">
                                <i class="fas fa-ellipsis-v" onclick="toggleDropdown(this)"></i>
                                <div class="dropdown">
                                    <a href="penyetoran.php?id=<?php echo $row['id_nasabah']; ?>">Penyetoran</a>
                                    <a href="#" onclick="editNasabah(<?php echo $row['id_nasabah']; ?>)">Edit</a>
                                    <a href="#" onclick="deleteNasabah(<?php echo $row['id_nasabah']; ?>)">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="nasabahModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Input Data Nasabah</h2>
            <form id="nasabahForm" onsubmit="saveNasabah(event)">
                <input type="hidden" id="nasabahId" name="id_nasabah" />
                <input type="hidden" id="formAction" name="action" value="add" />
                <input type="text" id="namaNasabah" name="nama_nasabah" placeholder="Nama Nasabah" required />
                <div class="input-group">
                    <input type="text" id="nik" name="nik" placeholder="NIK" required />
                    <input type="text" id="noTelp" name="no_telp" placeholder="No. Telp" required />
                </div>
                <input type="text" id="alamat" name="alamat" placeholder="Alamat" required />
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" required />
                    <input type="password" id="sandi" name="sandi" placeholder="Password" required />

                </div>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>
    <script>
        // Fungsi untuk menampilkan alert
        function showAlert(message, isSuccess) {
            if (isSuccess) { // Hanya tampilkan alert jika sukses
                const alert = document.getElementById('alert');
                alert.className = 'alert alert-success';
                alert.textContent = message;
                alert.style.display = 'block';
                setTimeout(() => alert.style.display = 'none', 3000);
            }
        }

        function togglePasswordVisibility(icon, password) {
            const passwordSpan = icon.previousElementSibling;
            const isHidden = icon.getAttribute('data-hidden') === 'true';

            if (isHidden) {
                // Tampilkan password
                passwordSpan.textContent = password;
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                icon.setAttribute('data-hidden', 'false');
            } else {
                // Sembunyikan password
                passwordSpan.textContent = '*'.repeat(password.length);
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                icon.setAttribute('data-hidden', 'true');
            }
        }

        function openModal() {
            const modal = document.getElementById('nasabahModal');
            modal.style.display = 'flex'; // Menggunakan flex untuk centering
            document.getElementById('modalTitle').textContent = 'Input Data Nasabah';
            document.getElementById('nasabahForm').reset();
            document.getElementById('formAction').value = 'add';
            document.body.style.overflow = 'hidden'; // Mencegah scroll pada background
        }

        function closeModal() {
            const modal = document.getElementById('nasabahModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';

            // Reset password type ke hidden
            const passwordInput = document.getElementById('sandi');
            const passwordIcon = passwordInput.nextElementSibling;
            passwordInput.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }

        // Menutup modal saat klik di luar
        window.onclick = function(event) {
            const modal = document.getElementById('nasabahModal');
            if (event.target === modal) {
                closeModal();
            }
        };

        // Menutup dengan tombol Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Fungsi untuk memperbarui baris tabel
        function updateTableRow(data, isNew = false) {
            const tableBody = document.getElementById('nasabahTableBody');

            if (isNew) {
                // Tambah baris baru
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-id', data.id_nasabah);
                newRow.innerHTML = `
            <td>${escapeHtml(data.nik)}</td>
            <td>${escapeHtml(data.nama_nasabah)}</td>
            <td>${escapeHtml(data.alamat)}</td>
            <td>${escapeHtml(data.no_telp)}</td>
            <td>${escapeHtml(data.username)}</td>
            <td class="password-cell">
                <span class="password-text">${'*'.repeat(data.sandi.length)}</span>
                <i class="fas fa-eye password-toggle-icon" 
                onclick="togglePasswordVisibility(this, '${escapeHtml(data.sandi)}')" 
                data-hidden="true"></i>
            </td>
            <td class="table-actions">
                <i class="fas fa-ellipsis-v" onclick="toggleDropdown(this)"></i>
                <div class="dropdown">
                    <a href="penyetoran.php?id=${data.id_nasabah}">Penyetoran</a>
                    <a href="#" onclick="editNasabah(${data.id_nasabah})">Edit</a>
                    <a href="#" onclick="deleteNasabah(${data.id_nasabah})">Hapus</a>
                </div>
            </td>
        `;
                tableBody.insertBefore(newRow, tableBody.firstChild);
            } else {
                // Update baris yang ada
                const existingRow = document.querySelector(`tr[data-id="${data.id_nasabah}"]`);
                if (existingRow) {
                    const cells = existingRow.getElementsByTagName('td');
                    cells[0].textContent = data.nik;
                    cells[1].textContent = data.nama_nasabah;
                    cells[2].textContent = data.alamat;
                    cells[3].textContent = data.no_telp;
                    cells[4].textContent = data.username;

                    // Update password cell
                    const passwordCell = cells[5];
                    passwordCell.innerHTML = `
                <span class="password-text">${'*'.repeat(data.sandi.length)}</span>
                <i class="fas fa-eye password-toggle-icon" 
                onclick="togglePasswordVisibility(this, '${escapeHtml(data.sandi)}')" 
                data-hidden="true"></i>
            `;
                }
            }
        }

        // Helper function untuk escape HTML
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Update fungsi saveNasabah
        async function saveNasabah(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const isNewRecord = formData.get('action') === 'add';

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    // Buat objek data dari form
                    const data = {
                        id_nasabah: isNewRecord ? result.id : formData.get('id_nasabah'),
                        nik: formData.get('nik'),
                        nama_nasabah: formData.get('nama_nasabah'),
                        alamat: formData.get('alamat'),
                        no_telp: formData.get('no_telp'),
                        username: formData.get('username'),
                        sandi: formData.get('sandi')
                    };
                    // Update tabel
                    updateTableRow(data, isNewRecord);
                    showAlert(result.message, true);
                    closeModal();
                } else {
                    showAlert(result.message, false);
                }
            } catch (error) {
                closeModal();
            }
        }

        // Fungsi edit nasabah
        function editNasabah(id) {
            const modal = document.getElementById('nasabahModal');
            const row = document.querySelector(`tr[data-id="${id}"]`);
            const cells = row.getElementsByTagName('td');

            document.getElementById('nasabahId').value = id;
            document.getElementById('nik').value = cells[0].textContent;
            document.getElementById('namaNasabah').value = cells[1].textContent;
            document.getElementById('alamat').value = cells[2].textContent;
            document.getElementById('noTelp').value = cells[3].textContent;
            document.getElementById('username').value = cells[4].textContent;

            // Reset password field karena keamanan
            document.getElementById('sandi').value = '';

            document.getElementById('modalTitle').textContent = 'Edit Data Nasabah';
            document.getElementById('formAction').value = 'update';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Update fungsi deleteNasabah
        async function deleteNasabah(id) {
            if (confirm('Apakah Anda yakin ingin menghapus nasabah ini? Anda akan kehilangan data yang terkait.')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id_nasabah', id);

                try {
                    const response = await fetch('', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        // Hapus baris dari tabel
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.remove();
                        }
                        showAlert(result.message, true);
                    } else {
                        showAlert(result.message, false);
                    }
                } catch (error) {}
            }
        }

        // Fungsi untuk toggle dropdown
        function toggleDropdown(element) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                if (dropdown !== element.nextElementSibling) {
                    dropdown.classList.remove('show');
                }
            });
            element.nextElementSibling.classList.toggle('show');
        }

        // Menutup dropdown saat klik di luar
        window.onclick = function(event) {
            if (!event.target.matches('.fa-ellipsis-v')) {
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        };

        // Live search
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const formData = new FormData();
            formData.append('search', e.target.value);
            fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    document.getElementById('nasabahTableBody').innerHTML =
                        doc.getElementById('nasabahTableBody').innerHTML;
                });
        });
    </script>
</body>

</html>