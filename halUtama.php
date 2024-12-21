<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=10.0" />
    <link
      href="https://fonts.googleapis.com/css?family=Lexend Deca"
      rel="stylesheet"
    />
    <style>
      html {
        font-size: 16px;
        font-family: "Lexend Deca";
        scroll-behavior: smooth;
      }
      * {
        box-sizing: border-box;
        margin: 0px;
        padding: 0px;
      }
      #leluhur-kontainer {
        display: grid;
        grid-template-rows: 85px calc(100vh - 85px) auto 110px;
        min-height: 100vh;
      }

      #header-kontainer {
        display: grid;
        column-gap: 7px;
        grid-template-columns: 1fr 2fr 7fr;
        background-image: linear-gradient(
          to right,
          #b8b7b7 0%,
          #5e5e5e 48%,
          #404040 100%
        );
      }
      #logo-kontainer {
        position: relative;
      }
      #logo-kontainer > img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: contain;
        margin-left: 30px;
      }
      #nama-aplikasi {
        position: relative;
        font-size: 2rem;
        font-weight: bold;
      }
      #nama-aplikasi > p {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
      }
      #navigasi-kontainer {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        font-size: 1rem;
        gap: 30px;
      }
      #navigasi-kontainer > a {
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        text-decoration: none;
        cursor: pointer;
      }
      #navigasi-kontainer > a:hover {
        color: rgb(94, 160, 167);
      }
      #navigasi-kontainer > div:last-child {
        height: fit-content;
        margin-right: 40px;
        background-color: #367874;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        border-radius: 5px;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
      }
      #navigasi-kontainer > div:last-child:hover {
        background-color: #34a49e;
      }
      #navigasi-kontainer > div:last-child > button {
        background-color: transparent;
        color: white;
        font-weight: bolder;
        font-size: 1rem;
        cursor: pointer;
        border: none;
      }

      #kontainer-ilustrasi-foto {
        position: relative;
        width: 100%;
        height: 100%;
      }

      #kontainer-ilustrasi-foto > img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      #kontainer-konten {
        display: grid;
        grid-template-rows: 100vh 1300px;
      }

      #bagian-pertama {
        background-color: #565656;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        padding: 0;
        margin: 0;
      }

      #kata-kata-hari-ini {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 20%;
        text-align: center;
        padding-bottom: 2rem;
      }

      #deskripsi-kata-kata-hari-ini {
        font-size: 1.5rem;
        margin: 0 20%;
        text-align: center;
        line-height: 1.6;
      }
      #bagian-kedua {
        background-image: linear-gradient(-213deg, #075450 0%, #d1eae8 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-left: 15%;
        padding-right: 15%;
        justify-content: space-evenly;
      }
      #bagian-kedua > p:nth-child(1) {
        font-size: 3rem;
        font-weight: bolder;
        color: white;
      }
      #bagian-kedua > p:nth-child(2) {
        font-family: "Abissynica SIL";
        text-align: center;
        font-size: 2.2rem;
        color: white;
        margin-left: 15%;
        margin-right: 15%;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
      }
      #bagian-kedua > p:nth-child(3) {
        font-size: 3rem;
        font-weight: bolder;
        color: black;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
      }
      #kontainer-poin-poin-misi {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 1.5rem;
      }
      #kontainer-poin-poin-misi #kontainer-logo {
        margin-right: 27%;
      }
      #kontainer-poin-satu,
      #kontainer-poin-dua,
      #kontainer-poin-tiga,
      #kontainer-poin-empat {
        display: grid;
        grid-template-columns: 1fr 4fr;
      }
      #kontainer-logo img {
        object-fit: contain;
        width: 100%;
        height: auto;
      }
      #kontainer-kalimat {
        display: flex;
        flex-direction: column;
        justify-content: flex-center;
        row-gap: 0.5rem;
      }
      #kontainer-kalimat > p:first-child {
        font-size: 1.5rem;
        font-weight: bolder;
      }
      #kontainer-kalimat > p:last-child {
        font-size: 1.25rem;
      }
      #kontainer-logo-whatsapp {
        display: flex;
        flex-direction: column;
        align-items: end;
        width: 120%;
      }
      #kontainer-logo-whatsapp > #sub-kontainer {
        text-align: right;
      }
      #sub-kontainer > img {
        object-fit: contain;
        width: 30%;
        height: auto;
      }
      #sub-kontainer > p {
        font-size: 0.8rem;
      }
      /* kontainer footer */
      #kontainer-footer {
        background-color: #565656;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        color: white;
      }
      #kontainer-footer > p:first-child {
        font-weight: bold;
        font-size: 2rem;
        text-align: center;
      }
      #kontainer-footer > p:last-child {
        font-size: 1.2rem;
        text-align: center;
      }
    </style>
  </head>

  <body>
    <div id="leluhur-kontainer">
      <div id="header-kontainer">
        <div id="logo-kontainer">
          <img src="logo-aplikasi.png" alt="link logo tidak valid" />
        </div>
        <div id="nama-aplikasi">
          <p>SAMADA</p>
        </div>
        <div id="navigasi-kontainer">
          <a href="#">BERANDA</a>
          <a href="#bagian-pertama">TENTANG</a>
          <a href="#kontainer-logo-whatsapp">KONTAK</a>
          <div>
            <button onclick="location.href='login.php'">LOGIN</button>
          </div>
        </div>
      </div>

      <div id="kontainer-ilustrasi-foto">
        <img src="ilustrasi-foto-homepage.png" alt="link foto tidak valid" />
      </div>

      <div id="kontainer-konten">
        <div id="bagian-pertama">
          <p id="kata-kata-hari-ini">
            Membangun Masa Depan Bersih dan Berkelanjutan
          </p>
          <p id="deskripsi-kata-kata-hari-ini">
            Bank Sampah Masa Depan adalah sebuah organisasi yang berdedikasi
            untuk menciptakan masa depan yang bersih dan berkelanjutan melalui
            pengelolaan sampah yang efektif dan pemberdayaan masyarakat.
            Didirikan pada tahun 2016 di Makassar, organisasi ini telah aktif
            melayani masyarakat dalam upaya mengatasi permasalahan sampah kota.
            Bank Sampah Masa Depan fokus pada pengumpulan dan pemilahan sampah
            plastik serta edukasi masyarakat tentang pentingnya pengelolaan
            sampah yang baik.
          </p>
        </div>
        <div id="bagian-kedua">
          <p>VISI</p>
          <p>
            " Mewujudkan masyarakat Kecamatan Rappoccini, Makassar yang sadar
            lingkungan dan mampu mengelola sampah secara mandiri untuk
            menciptakan lingkungan yang bersih, sehat, dan berkelanjutan "
          </p>
          <p>MISI</p>
          <div id="kontainer-poin-poin-misi">
            <div id="kontainer-poin-satu">
              <div id="kontainer-logo">
                <img
                  src="simbol-tempat-sampah.png"
                  alt="link-logo-tidak-valid"
                />
              </div>
              <div id="kontainer-kalimat">
                <p>Pengelolaan Sampah</p>
                <p>
                  Mengumpulkan dan memilah sampah dari masyarakat sekitar secara
                  rutin dan meningkatkan efisiensi proses pemilahan sampah.
                </p>
              </div>
            </div>
            <div id="kontainer-poin-dua">
              <div id="kontainer-logo">
                <img src="simbol-bagan-2.png" alt="link-logo-tidak-valid" />
              </div>
              <div id="kontainer-kalimat">
                <p>Pemberdayaan Ekonomi</p>
                <p>
                  Mengembangkan sistem tabungan sampah yang mudah diakses oleh
                  masyarakat sekitar.
                </p>
              </div>
            </div>
            <div id="kontainer-poin-tiga">
              <div id="kontainer-logo">
                <img src="simbol-gedung.png" alt="link-logo-tidak-valid" />
              </div>
              <div id="kontainer-kalimat">
                <p>Peningkatan Layaan</p>
                <p>
                  Memperluas cakupan layanan dan meningkatkan kapasitas
                  penyimpanan dan pemilahan sampah sesuai dengan pertumbuhan
                  jumlah nasabah.
                </p>
              </div>
            </div>
            <div id="kontainer-poin-empat">
              <div id="kontainer-logo">
                <img src="simbol-kertas.png" alt="link-logo-tidak-valid" />
              </div>
              <div id="kontainer-kalimat">
                <p>Transparansi dan Akuntabilitas</p>
                <p>
                  Melakukan pencatatan transaksi sampah secara teratur dan
                  akurat serta menyampaikan laporan bulanan kepada nasabah dan
                  pemangku kepentingan tentang jumlah sampah yang dikelola.
                </p>
              </div>
            </div>
          </div>
          <div id="kontainer-logo-whatsapp">
            <a
              href="https://wa.me/62822996992376"
              target="_blank"
              style="text-decoration: none"
            >
              <img
                src="logo-whatsapp.png"
                alt="logo tidak valid"
                style="width: 50px; height: 50px"
              />
              <p style="color: black; margin-top: 10px; font-size: 16px">
                Hubungi Kami
              </p>
            </a>
          </div>
        </div>
      </div>

      <div id="kontainer-footer">
        <p>SAMADA</p>
        <p>Bank Sampah Masa Depan</p>
      </div>
    </div>
  </body>
</html>
