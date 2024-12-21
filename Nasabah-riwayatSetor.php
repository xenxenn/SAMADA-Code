<?php
include "koneksi.php";

// session_start();
//   $id_nasabah = $_SESSION['id_nasabah']; // Pastikan sesi ini telah diatur saat login

  // Query data penyetoran dengan join ke tabel sampah untuk mendapatkan harga
  $query = "
      SELECT 
          p.id_penyetoran, 
          p.tgl_penyetoran, 
          p.jenis_sampah, 
          p.berat, 
          s.harga_per_kg, 
          (p.berat * s.harga_per_kg) AS total_harga 
      FROM 
          penyetoran p 
      INNER JOIN 
          sampah s 
      ON 
          p.jenis_sampah = s.jenis_sampah 
      WHERE 
          p.id_nasabah = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $id_nasabah);
  $stmt->execute();
  $result = $stmt->get_result();
?>

<html>
  <head>
    <title>Riwayat Penyetoran</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f0f0f0;
      }
      .header {
        background: linear-gradient(to right, #b4cecc, #397572);
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .header .logo-container {
        display: flex;
        align-items: center;
      }
      .header img {
        height: 50px;
      }
      .header .logo-text {
        color: black;
        font-size: 24px;
        font-weight: bold;
        margin-left: 10px;
      }
      .header .nav {
        display: flex;
        gap: 20px;
      }
      .header .nav a {
        color: white;
        text-decoration: none;
        font-weight: bold;
      }
      .header .nav a:hover {
        text-decoration: underline;
      }
      .container {
        padding: 20px;
      }
      .title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 24px;
        font-weight: bold;
      }
      .title i {
        font-size: 24px;
      }
      .title img {
        height: 35px;
        position: relative;
        bottom: 3px;
      }
      .bell-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 30px;
        margin-left: 0;
        cursor: pointer;
      }
      .table-container {
        margin-top: 20px;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }
      table {
        width: 100%;
        border-collapse: collapse;
      }
      th,
      td {
        padding: 10px;
        text-align: left;
      }
      th {
        background-color: #397572;
        color: white;
      }
      td {
        border-bottom: 1px solid #ddd;
      }
      .notification {
        position: absolute;
        top: 150px;
        right: 20px;
        background-color: #333;
        color: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 250px;
        display: none;
      }
      .notification h4 {
        margin: 0 0 10px 0;
        font-size: 16px;
      }
      .notification p {
        margin: 0;
        font-size: 14px;
      }
      .notification i {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
      }
    </style>
    <script>
      function toggleNotification() {
        var notification = document.querySelector(".notification");
        if (
          notification.style.display === "none" ||
          notification.style.display === ""
        ) {
          notification.style.display = "block";
        } else {
          notification.style.display = "none";
        }
      }
    </script>
  </head>
  <body>
    <div class="header">
      <div class="logo-container">
        <img alt="Logo" height="50" src="logo (1).png" width="50" />
        <span class="logo-text"> SAMADA </span>
      </div>
      <div class="nav">
                <a href="berandaNasabah.php">BERANDA</a>
                <a href="informasiSampah.php">INFORMASI SAMPAH</a>
                <a href="riwayatPenyetoran.php">RIWAYAT PENYETORAN</a>
                <a href="profilNasabah.php">PROFIL</a>
      </div>
    </div>
    <div class="container">
      <div class="title">
        <img src="Logo2.png" />
        <span> Riwayat Penyetoran </span>
        <div
          class="bell-icon"
          onclick="toggleNotification()"
          style="margin-left: auto"
        >
          <i class="fas fa-bell"> </i>
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
              <th>Total Harga</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $no = 1;
          $grand_total = 0;
          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $grand_total += $row['total_harga']; // Menjumlahkan total harga keseluruhan
                  echo "<tr>";
                  echo "<td>" . $no++ . "</td>";
                  echo "<td>" . htmlspecialchars($row['tgl_penyetoran']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['jenis_sampah']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['berat']) . "</td>";
                  echo "<td>" . number_format($row['total_harga'], 2, ',', '.') . "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='6'>Tidak ada data penyetoran.</td></tr>";
          }
          ?>
        </tbody>
        </table>
      </div>
    </div>
    <div class="notification">
      <h4>Pemberitahuan</h4>
      <p>
        <strong> Informasi Penarikan Dana </strong>
      </p>
      <p>
        Hai, Nasabah! Uang hasil setoran sampah anda sudah dapat diambil. Admin
        akan segera menghubungi Anda terkait proses penarikan saldo.
      </p>
      <i class="fas fa-times" onclick="toggleNotification()"> </i>
    </div>
  </body>
</html>
