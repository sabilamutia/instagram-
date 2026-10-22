<?php
session_start();
include 'koneksi.php';


// =========================================
// CEK LOGIN
// =========================================

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user_login = (int) $_SESSION['id_user'];


// =========================================
// TENTUKAN PROFIL YANG DIBUKA
// =========================================

if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $id_user_profil = (int) $_GET['id'];

} elseif (isset($_GET['id_user']) && is_numeric($_GET['id_user'])) {

    $id_user_profil = (int) $_GET['id_user'];

} else {

    $id_user_profil = $id_user_login;
}


// =========================================
// DATA USER YANG LOGIN
// =========================================

$query_login = mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE id = '$id_user_login'"
);

$user_login = mysqli_fetch_assoc($query_login);

if (!empty($user_login['foto_profil'])) {
    $foto_user_login = $user_login['foto_profil'];
} else {
    $foto_user_login = 'uploads/image.png';
}


// =========================================
// DATA PROFIL
// =========================================

$query_profil = mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE id = '$id_user_profil'"
);

$profil = mysqli_fetch_assoc($query_profil);

if (!$profil) {
    echo "Akun tidak ditemukan.";
    exit();
}


// =========================================
// FOTO PROFIL
// =========================================

if (!empty($profil['foto_profil'])) {
    $foto_profil = $profil['foto_profil'];
} else {
    $foto_profil = 'uploads/image.png';
}


// =========================================
// DATA PROFIL
// =========================================

// Jumlah postingan sementara 0
// karena postingan sedang tidak ditampilkan
$jumlah_post = 0;

// Pengikut dan mengikuti sementara 0
// karena tabel follower belum dibuat
$jumlah_pengikut = 0;
$jumlah_mengikuti = 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($profil['username']); ?> - Profil
    </title>

    <link rel="stylesheet" href="style.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <style>

        /* =====================================
           FOTO SIDEBAR
        ===================================== */

        .sidebar .avatar-nav {
            width: 35px !important;
            height: 35px !important;
            min-width: 35px !important;
            max-width: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;

            border-radius: 50% !important;
            object-fit: cover !important;
            display: block !important;
        }


        /* =====================================
           CONTAINER PROFIL
        ===================================== */

        .profile-container {
            max-width: 650px;
            margin: 0 auto;
        }


        /* =====================================
           HEADER PROFIL
        ===================================== */

        .profile-header {
            display: flex;
            align-items: center;
            gap: 40px;
            padding: 30px;
        }


        /* FOTO PROFIL UTAMA */

        .profile-header > img {
            width: 120px !important;
            height: 120px !important;

            min-width: 120px !important;
            max-width: 120px !important;

            min-height: 120px !important;
            max-height: 120px !important;

            border-radius: 50% !important;
            object-fit: cover !important;
            display: block !important;
        }


        /* =====================================
           INFO PROFIL
        ===================================== */

        .profile-info {
            flex: 1;
        }


        .profile-info h2 {
            margin: 0 0 15px 0;
            font-size: 24px;
        }


        .profile-info p {
            margin: 0 0 15px 0;
            font-size: 15px;
            color: #555;
        }


        /* =====================================
           STATISTIK INSTAGRAM
        ===================================== */

        .profile-stats {
            display: flex;
            gap: 28px;
            margin-top: 10px;
        }


        .profile-stat {
            font-size: 14px;
            color: #262626;
        }


        .profile-stat strong {
            font-size: 16px;
        }


        /* =====================================
           KETERANGAN / BIO
        ===================================== */

        .profile-bio {
            padding: 0 30px 25px 30px;
            border-bottom: 1px solid #dbdbdb;
        }


        .profile-bio-title {
            font-weight: bold;
            margin-bottom: 5px;
        }


        .profile-bio-text {
            color: #555;
            font-size: 14px;
        }


        /* =====================================
           AREA POSTINGAN
        ===================================== */

        .empty-post {
            text-align: center;
            padding: 70px 20px;
        }


        .empty-post-icon {
            width: 65px;
            height: 65px;

            border: 2px solid #262626;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 20px auto;

            font-size: 28px;
        }


        .empty-post h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
        }


        .empty-post p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }


        /* =====================================
           HP
        ===================================== */

        @media (max-width: 600px) {

            .profile-header {
                gap: 20px;
                padding: 20px;
            }

            .profile-header > img {
                width: 90px !important;
                height: 90px !important;

                min-width: 90px !important;
                max-width: 90px !important;

                min-height: 90px !important;
                max-height: 90px !important;
            }

            .profile-info h2 {
                font-size: 20px;
            }

            .profile-stats {
                gap: 15px;
            }

            .profile-bio {
                padding: 0 20px 20px 20px;
            }

        }

    </style>

</head>


<body>


<div class="app-layout">


    <!-- =====================================
         SIDEBAR
    ===================================== -->

    <aside class="sidebar">

        <h1 class="brand-logo">
            Instagram
        </h1>


        <nav class="nav-menu">


            <!-- BERANDA -->

            <a href="index.php" class="nav-item">

                <i class="fa-solid fa-house"></i>

                <span>
                    Beranda
                </span>

            </a>


            <!-- PENCARIAN -->

            <a href="pencarian.php" class="nav-item">

                <i class="fa-solid fa-magnifying-glass"></i>

                <span>
                    Pencarian
                </span>

            </a>


            <!-- BUAT -->

            <a href="buat.php" class="nav-item">

                <i class="fa-regular fa-square-plus"></i>

                <span>
                    Buat
                </span>

            </a>


            <!-- PROFIL -->

            <a href="profil.php" class="nav-item active">

                <img
                    src="<?= htmlspecialchars($foto_user_login); ?>"
                    class="avatar-nav"
                    onerror="this.src='uploads/image.png';"
                    alt="Foto profil"
                >

                <span>
                    Profil
                </span>

            </a>


        </nav>


        <!-- LOGOUT -->

        <div class="sidebar-bottom">

            <a
                href="logout.php"
                onclick="return confirm('Apakah kamu yakin ingin logout?');"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>
                    Logout
                </span>

            </a>

        </div>

    </aside>


    <!-- =====================================
         MAIN
    ===================================== -->

    <main class="main-content">


        <div class="profile-container">


            <!-- =====================================
                 HEADER PROFIL
            ===================================== -->

            <div class="profile-header">


                <!-- FOTO -->

                <img
                    src="<?= htmlspecialchars($foto_profil); ?>"
                    onerror="this.src='uploads/image.png';"
                    alt="Foto Profil"
                >


                <!-- INFO -->

                <div class="profile-info">


                    <!-- USERNAME -->

                    <h2>
                        <?= htmlspecialchars($profil['username']); ?>
                    </h2>


                    <!-- STATISTIK -->

                    <div class="profile-stats">


                        <div class="profile-stat">

                            <strong>
                                <?= $jumlah_post; ?>
                            </strong>

                            postingan

                        </div>


                        <div class="profile-stat">

                            <strong>
                                <?= $jumlah_pengikut; ?>
                            </strong>

                            pengikut

                        </div>


                        <div class="profile-stat">

                            <strong>
                                <?= $jumlah_mengikuti; ?>
                            </strong>

                            mengikuti

                        </div>


                    </div>


                </div>


            </div>


            <!-- =====================================
                 KETERANGAN / BIO
            ===================================== -->

            <div class="profile-bio">

                <div class="profile-bio-title">

                    <?= htmlspecialchars($profil['nama']); ?>

                </div>


                <div class="profile-bio-text">

                    Belum ada keterangan.

                </div>

            </div>


            <!-- =====================================
                 BELUM ADA POSTINGAN
            ===================================== -->

            <div class="empty-post">


                <div class="empty-post-icon">

                    <i class="fa-regular fa-image"></i>

                </div>


                <h3>
                    Belum ada postingan
                </h3>


                <p>
                    Akun ini belum membuat postingan.
                </p>


            </div>


        </div>


    </main>


</div>


</body>

</html>