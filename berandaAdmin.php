<?php
session_start();

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "samada");
// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
// Periksa apakah admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}
$id_admin = $_SESSION['id_admin'];
// Query untuk mendapatkan nama admin berdasarkan id_admin
$query = "SELECT nama_admin FROM admin WHERE id_admin = '$id_admin'";
$result = mysqli_query($koneksi, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $nama_admin = $row['nama_admin'];
} else {
    $nama_admin = "Admin";
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Lexend Deca' rel='stylesheet'>
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

        /* KONTEN*/

        #wraper-section {
            display: grid;
            grid-template-rows: 1fr 10fr 9fr;
            margin-left: 60px;
            margin-right: 60px;
            height: 100%;
        }

        #wraper-section>#bagian-atas {
            display: inline-flex;
            justify-content: flex-start;
            align-items: center;
            padding-left: 2%;
            font-weight: 900;
        }

        #wraper-section>#bagian-tengah {
            display: grid;
            grid-template-columns: 1.5fr 1.5fr;
            background-image: linear-gradient(to right, #b2dcda, #339893);
            border-radius: 25px;
            margin-left: 1rem;
            margin-right: 1rem;
        }

        #wraper-section>#bagian-tengah>#bagian-kiri {
    display: flex;
    flex-direction: column; /* Keep vertical layout for separate lines */
    justify-content: center;
    padding-left: 10%;
}

#wraper-section>#bagian-tengah>#bagian-kiri>#sapaan-user {
    font-size: 4rem;
    font-weight: 900;
    margin-bottom: 0.5rem; /* Adjust margin between name and subtitle */
    white-space: nowrap; /* Prevent wrapping of name */
}

        #wraper-section>#bagian-tengah>#bagian-kiri>#sub-sapaan-user {
            font-size: 1.2rem;
            font-weight: 900;
        }

        #wraper-section>#bagian-tengah>#bagian-kanan {
            position: relative;
        }

        #wraper-section>#bagian-tengah>#bagian-kanan>img {
            position: absolute;
            right: 0;
            /* Menempatkan elemen di sisi kanan kontainer */
            bottom: 0;
            /* Menempatkan elemen di sisi bawah kontainer */
            height: 116%;
        }

        #wraper-section>#bagian-bawah {
            display: grid;
            margin-top: 2.5rem;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 3fr 1fr;
            margin-left: 15rem;
            margin-right: 15rem;
            column-gap: 2rem;
        }

        .menu-card {
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: linear-gradient(to left, #72BFBB, #0A5A55);
            border-radius: 20px;
            color: #fff;
            font-size: 1.5rem;
            font-weight: 900;
            padding: 2rem;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div id="leluhur-kontainer">
        <!-- header -->
        <div id="header-kontainer">
            <div id="logo-kontainer">
                <img src="logo-aplikasi.png" alt="link logo tidak valid" />
            </div>
            <div id="nama-aplikasi">
                <p>SAMADA</p>
            </div>
            <div id="navigasi-kontainer">
                <a href="berandaAdmin.php">BERANDA</a>
                <a href="daftarNasabah.php">DAFTAR NASABAH</a>
                <a href="dataSampah.php">DATA SAMPAH</a>
                <a href="profilAdmin.php">PROFIL</a>
            </div>
        </div>
        <!-- end of header -->
        <!-- konten -->
        <div id="konten-kontainer">
            <div id="wraper-section">
                <section id="bagian-atas"></section>
                <section id="bagian-tengah">
                    <div id="bagian-kiri">
                        <p id="sapaan-user">Hi, <?php echo $nama_admin; ?>!</p>
                        <p id="sub-sapaan-user">Selamat datang di Bank Sampah Masa Depan</p>
                    </div>
                    <div id="bagian-kanan">
                        <img src="gambar-manusia-main-komputer.png" alt="link gambar tidak valid" />
                    </div>
                </section>
                <section id="bagian-bawah">
                    <a href="daftarNasabah.php" class="menu-card">DAFTAR NASABAH</a>
                    <a href="dataSampah.php" class="menu-card">DATA SAMPAH</a>
                </section>
            </div>
        </div>
        <!-- end of konten -->
    </div>
</body>

</html>