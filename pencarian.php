<?php
session_start();

include "koneksi.php";

$keyword = "";
$hasil = [];

// Ambil kata pencarian
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
}

// Jalankan pencarian
if ($keyword !== "") {

    $keyword_db = mysqli_real_escape_string($koneksi, $keyword);

    $sql = "
        SELECT id, username, nama, foto_profil
        FROM users
        WHERE username LIKE '%$keyword_db%'
        OR nama LIKE '%$keyword_db%'
        ORDER BY username ASC
    ";

    $query = mysqli_query($koneksi, $sql);

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $hasil[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pencarian - Instagram</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            color: #262626;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 235px;
            height: 100vh;
            padding: 30px 15px;
            border-right: 1px solid #dbdbdb;
            background: white;
        }

        .logo {
            font-family: cursive;
            font-size: 30px;
            font-weight: bold;
            margin: 0 15px 40px;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px;
            margin-bottom: 8px;
            border-radius: 10px;
            text-decoration: none;
            color: #262626;
            font-size: 16px;
        }

        .menu:hover {
            background: #f2f2f2;
        }

        .menu.active {
            background: #f2f2f2;
            font-weight: bold;
        }

        .icon {
            width: 25px;
            text-align: center;
            font-size: 20px;
        }

        /* MAIN */

        .main {
            margin-left: 235px;
            min-height: 100vh;
            padding: 40px;
        }

        .container {
            width: 600px;
            max-width: 100%;
            margin: 0 auto;
        }

        h1 {
            margin: 0 0 20px;
            font-size: 24px;
        }

        /* SEARCH */

        .search-box {
            display: flex;
            gap: 8px;
            margin-bottom: 25px;
        }

        .search-box input {
            flex: 1;
            height: 42px;
            padding: 0 15px;
            border: none;
            border-radius: 8px;
            background: #efefef;
            font-size: 14px;
            outline: none;
        }

        .search-box input:focus {
            background: #e5e5e5;
        }

        .search-box button {
            height: 42px;
            padding: 0 20px;
            border: none;
            border-radius: 8px;
            background: #0095f6;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #0077cc;
        }

        /* HASIL */

        .hasil-title {
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: bold;
        }

        .hasil {
            border-top: 1px solid #efefef;
        }

        .akun {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 14px 5px;
            border-bottom: 1px solid #efefef;
            text-decoration: none;
            color: #262626;
        }

        .akun:hover {
            background: #fafafa;
        }

        /* FOTO PROFIL */

        .foto-profil {
            width: 56px;
            height: 56px;
            min-width: 56px;
            margin-right: 15px;
            border-radius: 50%;
            object-fit: cover;
            background: #efefef;
        }

        /* INFO */

        .info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .username {
            font-size: 15px;
            font-weight: bold;
        }

        .nama {
            font-size: 14px;
            color: #737373;
        }

        /* TIDAK ADA HASIL */

        .tidak-ada {
            padding: 50px 10px;
            text-align: center;
            color: #737373;
        }

        /* RESPONSIVE */

        @media (max-width: 700px) {

            .sidebar {
                width: 70px;
                padding: 30px 8px;
            }

            .logo {
                font-size: 0;
                text-align: center;
                margin-bottom: 40px;
            }

            .logo::after {
                content: "◎";
                font-size: 30px;
            }

            .menu {
                justify-content: center;
            }

            .menu span {
                display: none;
            }

            .main {
                margin-left: 70px;
                padding: 25px 15px;
            }

            .container {
                width: 100%;
            }
        }
    </style>

</head>

<body>

    <!-- SIDEBAR -->

    <div class="sidebar">

        <div class="logo">
            Instagram
        </div>

        <a href="index.php" class="menu">
            <div class="icon">🏠</div>
            <span>Beranda</span>
        </a>

        <a href="pencarian.php" class="menu active">
            <div class="icon">🔍</div>
            <span>Pencarian</span>
        </a>

        <a href="buat.php" class="menu">
            <div class="icon">＋</div>
            <span>Buat</span>
        </a>

        <a
            href="logout.php"
            class="menu"
            style="margin-top: 250px;"
        >
            <div class="icon">↪</div>
            <span>Logout</span>
        </a>

    </div>


    <!-- MAIN -->

    <div class="main">

        <div class="container">

            <h1>
                Pencarian
            </h1>


            <!-- FORM PENCARIAN -->

            <form
                action="pencarian.php"
                method="GET"
                class="search-box"
            >

                <input
                    type="text"
                    name="keyword"
                    placeholder="Cari akun..."
                    value="<?= htmlspecialchars($keyword) ?>"
                    autocomplete="off"
                    required
                >

                <button type="submit">
                    Cari
                </button>

            </form>


            <?php if ($keyword !== ""): ?>

                <div class="hasil-title">
                    Hasil pencarian untuk
                    "<?= htmlspecialchars($keyword) ?>"
                </div>


                <div class="hasil">

                    <?php if (count($hasil) > 0): ?>

                        <?php foreach ($hasil as $akun): ?>

                            <?php
                            /*
                             * Mengatur alamat foto profil.
                             *
                             * Database kamu menyimpan:
                             * uploads/image.png
                             *
                             * Jadi jangan menambahkan "uploads/"
                             * lagi kalau sudah ada.
                             */

                            if (!empty($akun['foto_profil'])) {

                                $foto = $akun['foto_profil'];

                                // Kalau database hanya menyimpan nama file
                                if (strpos($foto, 'uploads/') !== 0) {
                                    $foto = 'uploads/' . $foto;
                                }

                            } else {

                                $foto = 'uploads/image.png';
                            }
                            ?>


                            <a
                                href="profil.php?id=<?= (int)$akun['id'] ?>"
                                class="akun"
                            >

                                <img
                                    src="<?= htmlspecialchars($foto) ?>"
                                    class="foto-profil"
                                    alt="Foto profil"
                                    onerror="this.src='uploads/image.png';"
                                >


                                <div class="info">

                                    <div class="username">
                                        <?= htmlspecialchars($akun['username']) ?>
                                    </div>

                                    <div class="nama">
                                        <?= htmlspecialchars($akun['nama']) ?>
                                    </div>

                                </div>

                            </a>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <div class="tidak-ada">

                            Tidak ditemukan akun untuk

                            "<strong>
                                <?= htmlspecialchars($keyword) ?>
                            </strong>"

                        </div>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</body>

</html>