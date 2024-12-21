<?php
session_start();

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "samada");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Verifikasi admin
        $query_admin = "SELECT * FROM admin WHERE username = ?";
        $stmt_admin = $koneksi->prepare($query_admin);
        $stmt_admin->bind_param('s', $username);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();

        if ($result_admin->num_rows > 0) {
            $admin = $result_admin->fetch_assoc();

            // Verifikasi password
            if ($password === $admin['sandi']) {
                $_SESSION['id_admin'] = $admin['id_admin'];  // Simpan id_admin di session
                $_SESSION['role'] = 'admin';
                $_SESSION['username'] = $admin['username'];
                header("Location: berandaAdmin.php");

                exit();
            } else {
                $error_message = "Username atau password salah";
            }
        } else {
            // Jika bukan admin, cek nasabah
            $query_nasabah = "SELECT * FROM nasabah WHERE username = ?";
            $stmt_nasabah = $koneksi->prepare($query_nasabah);
            $stmt_nasabah->bind_param('s', $username);
            $stmt_nasabah->execute();
            $result_nasabah = $stmt_nasabah->get_result();

            if ($result_nasabah->num_rows > 0) {
                $nasabah = $result_nasabah->fetch_assoc();
                if ($password === $nasabah['sandi']) {
                    $_SESSION['id_nasabah'] = $nasabah['id_nasabah'];
                    $_SESSION['role'] = 'nasabah';
                    $_SESSION['username'] = $nasabah['username'];
                    $_SESSION['nama_nasabah'] = $nasabah['nama_nasabah'];
                    header("Location:berandaNasabah.php");
                    exit();
                } else {
                    $error_message = "Username atau password salah";
                }
            } else {
                $error_message = "Username atau password salah";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- font yang dipakai judul aplikasi (Caprasimo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caprasimo&display=swap" rel="stylesheet">
    <!-- akhir font Caprasimo -->
    <!-- font yang dipakai subjudul aplikasi (Merienda) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caprasimo&family=Merienda:wght@300..900&display=swap"
        rel="stylesheet">
    <!-- akhir font Merienda -->
    <style>
        * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
        }

        /* BLOCK - KONTAINER HALAMAN */
        .kontainer-halaman {
            font-size: 16px;
            display: grid;
            grid-template-columns: 2fr 1.25fr;
        }

        /* ELEMENT */
        .kontainer-halaman__curve {
            position: absolute;
            top: 0;
            left: 0;
            height: 100vh;
            width: 55vw;
        }

        .gradient-stop1 {
            stop-color: #075450;
        }

        .gradient-stop2 {
            stop-color: #066460a8;
        }

        .kontainer-halaman__line-curve {
            position: absolute;
            top: 0;
            left: 0;
            height: 100vh;
            width: 60vw;
            stroke-width: .20rem;
            stroke: #0A5A55;
        }

        .kontainer-halaman__merek-aplikasi {
            padding-block-start: 28vh;
            padding-inline-start: 5vw;
        }

        .kontainer-halaman__tempat-login {
            background-color: #fff;
            margin-block-start: 6rem;
        }

        .kontainer-halaman__logo-tempat-login {
            object-fit: cover;
            width: 100%;
            height: auto;
        }

        .kontainer-halaman__wrapper-judul-aplikasi {
            font-size: 9.5rem;
        }

        .kontainer-halaman__text-subjudul {
            font-size: 1.2rem;
            width: 30rem;
            position: relative;
        }

        .kontainer-halaman__wrapper-logo {
            margin-inline: 10rem;
        }

        .kontainer-halaman__wrapper-field-username {
            margin-inline: 7rem;
            margin-block-start: 1rem;
        }

        .kontainer-halaman___wrapper-field-password {
            margin-inline: 7rem;
            margin-block-start: 1rem;
        }

        .kontainer-halaman__wrapper-tombol-submit {
            margin-block-start: 1.5rem;
            margin-inline: 9rem;
            height: 3.5rem;
        }

        /*DEKORASI */
        .kontainer-halaman__text-subjudul--dekorasi {
            color: white;
            text-align: center;
        }

        /* BLOCK - TEXTBOX (REUSABLE)*/
        .input-field-v1 {
            width: 100%;
            padding-block: 1em;
            padding-inline: .5em;
            background: transparent;
            border: none;
            border-bottom: 1px solid #727171;
            outline: none;
            font-size: 1em;
            color: #000;
        }

        /* BLOCK - TOMBOL (REUSABLE) */
        .tombol-v1 {
            display: block;
            width: 100%;
            height: 100%;
            padding: 10px 20px;
            background-image: linear-gradient(to right, #84C7C3 0%, #2F837E 100%);
            color: #fff;
            border: none;
            border-radius: 0.7em;
            font-size: 1em;
            cursor: pointer;
            text-align: center;
        }

        .tombol-v1:hover {
            font-size: 1.10em;
            background-image: linear-gradient(to right, #84C7C3 0%, #2F837E 70%);
        }

        /* BLOCK - SAMA TEKS (REUSABLE) */
        .samada-teks {
            position: relative;
            font-size: 1em;
            min-height: 1em;
        }

        .samada-teks::before {
            font-size: 1em;
            content: "SAMADA";
            color: black;
            position: absolute;
            top: -.02em;
            left: -.03em;
            filter: blur(.02em);
            -webkit-filter: blur(.02em);
        }

        .samada-teks::after {
            font-size: 1em;
            content: "SAMADA";
            position: absolute;
            background: linear-gradient(to right, #9FD8D5 0%, #308C83 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* BLOCK - UTILITAS DEKORASI FONT (REUSABLE) */
        .font--caprasimo-regular {
            font-family: "Caprasimo", serif;
            font-weight: bold;
            font-style: normal;
        }

        .font--merienda {
            font-family: "Merienda", cursive;
            font-optical-sizing: auto;
            font-weight: bold;
            font-style: normal;
        }

        .alert {
            
            font-size: 0.9em;
            text-align: center;
        }

        .alert-error {
            color: #c62828;
        }

        .alert-success {
            color: #2e7d32;
        }
    </style>
</head>

<body>
    <div class="kontainer-halaman">
        <!-- sisi kiri -->
        <div class="kontainer-halaman__merek-aplikasi">
            <!-- kurva sisi kanan -->
            <svg class="kontainer-halaman__curve" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600"
                preserveAspectRatio="none">
                <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" class="gradient-stop1" />
                        <stop offset="58%" class="gradient-stop2" />
                    </linearGradient>
                </defs>
                <path d="M 550 0 Q 400 100 450 250 Q 550 450 500 600 L 0 600 L 0 0 L 550 0 " fill="url(#gradient)"
                    stroke="none" />
            </svg>
            <svg class="kontainer-halaman__line-curve" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600"
                preserveAspectRatio="none">
                <path d="M 550 0 Q 400 100 450 250 Q 550 450 500 600" fill="none" />
            </svg>
            <div class="kontainer-halaman__wrapper-judul-aplikasi">
                <h1 class="samada-teks font--caprasimo-regular"></h1>
            </div>
            <p class="kontainer-halaman__text-subjudul kontainer-halaman__text-subjudul--dekorasi font--merienda">
                MEMBANGUN MASA DEPAN YANG BERSIH DAN BERKELANJUTAN</p>
        </div>
        <!-- sisi kanan -->
        <div class="kontainer-halaman__tempat-login">
            <div class="kontainer-halaman__wrapper-logo">
                <img src="logo-aplikasi.png" alt="tautan logo bermasalah" class="kontainer-halaman__logo-tempat-login">
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- form login -->
            <form action="login.php" method="POST">
                <div class="kontainer-halaman__wrapper-field-username">
                    <input type="text" name="username" class="input-field-v1" placeholder="Username" required />
                </div>
                <div class="kontainer-halaman___wrapper-field-password">
                    <input type="password" name="password" class="input-field-v1" placeholder="Password" required />
                </div>
                <div class="kontainer-halaman__wrapper-tombol-submit">
                    <button type="submit" class="tombol-v1">Login</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.querySelector('input[name="password"]');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const password = document.querySelector('input[name="password"]').value;

            if (!username || !password) {
                e.preventDefault();
                alert('Username dan password harus diisi');
            }
        });
    </script>
</body>

</html>