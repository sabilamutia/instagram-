<?php

session_start();
include 'koneksi.php';

// =====================================
// CEK LOGIN
// =====================================

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user_login = $_SESSION['id_user'];


// =====================================
// DATA USER YANG LOGIN
// =====================================

$query_user = mysqli_query(
    $koneksi,
    "SELECT * FROM user WHERE id_user = '$id_user_login'"
);

$user = mysqli_fetch_assoc($query_user);

$foto_user_login = !empty($user['foto_profil'])
    ? $user['foto_profil']
    : 'https://picsum.photos/150';


// =====================================
// PESAN
// =====================================

$pesan = "";
$jenis_pesan = "";


// =====================================
// PROSES POSTING
// =====================================

if (isset($_POST['posting'])) {

    $deskripsi = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['deskripsi'])
    );


    // =================================
    // CEK GAMBAR
    // =================================

    if (
        isset($_FILES['gambar']) &&
        $_FILES['gambar']['error'] === 0
    ) {

        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];
        $ukuran_file = $_FILES['gambar']['size'];


        // =================================
        // CEK EKSTENSI
        // =================================

        $ekstensi = strtolower(
            pathinfo(
                $nama_file,
                PATHINFO_EXTENSION
            )
        );


        $format_valid = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];


        if (!in_array($ekstensi, $format_valid)) {

            $pesan = "Format gambar tidak diperbolehkan.";
            $jenis_pesan = "error";

        } else {


            // =================================
            // BATAS UKURAN 5 MB
            // =================================

            if ($ukuran_file > 5 * 1024 * 1024) {

                $pesan = "Ukuran gambar maksimal 5 MB.";
                $jenis_pesan = "error";

            } else {


                // =================================
                // CEK FOLDER UPLOADS
                // =================================

                if (!is_dir('uploads')) {

                    mkdir('uploads', 0777, true);

                }


                // =================================
                // BUAT NAMA FILE BARU
                // =================================

                $nama_baru = uniqid('post_')
                    . '.'
                    . $ekstensi;


                $lokasi = 'uploads/' . $nama_baru;


                // =================================
                // PINDAHKAN FILE
                // =================================

                if (move_uploaded_file($tmp_file, $lokasi)) {


                    // =================================
                    // SIMPAN KE DATABASE
                    // =================================

                    $query = mysqli_query(
                        $koneksi,
                        "INSERT INTO post
                        (id_user, gambar, deskripsi)
                        VALUES
                        ('$id_user_login', '$nama_baru', '$deskripsi')"
                    );


                    if ($query) {

                        header("Location: index.php");
                        exit();

                    } else {

                        // Hapus gambar jika database gagal
                        if (file_exists($lokasi)) {
                            unlink($lokasi);
                        }

                        $pesan = "Gagal menyimpan postingan: "
                            . mysqli_error($koneksi);

                        $jenis_pesan = "error";
                    }

                } else {

                    $pesan = "Gagal mengupload gambar.";
                    $jenis_pesan = "error";

                }
            }
        }

    } else {

        $pesan = "Silakan pilih foto terlebih dahulu.";
        $jenis_pesan = "error";

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Buat Postingan - Instagram Clone</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

</head>


<body>


<div class="app-layout">


    <!-- =====================================
         SIDEBAR
    ====================================== -->

    <aside class="sidebar">

        <h1 class="brand-logo">
            Instagram
        </h1>


        <nav class="nav-menu">


            <!-- BERANDA -->

            <a
                href="index.php"
                class="nav-item"
            >

                <i class="fa-solid fa-house"></i>

                <span>
                    Beranda
                </span>

            </a>


            <!-- PENCARIAN -->

            <a
                href="pencarian.php"
                class="nav-item"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                <span>
                    Pencarian
                </span>

            </a>


            <!-- BUAT -->

            <a
                href="buat.php"
                class="nav-item active"
            >

                <i class="fa-regular fa-square-plus"></i>

                <span>
                    Buat
                </span>

            </a>


            <!-- PROFIL -->

            <a
                href="profil.php"
                class="nav-item"
            >

                <img
                    src="<?= htmlspecialchars($foto_user_login); ?>"
                    class="avatar-nav"
                    onerror="this.src='https://picsum.photos/150';"
                    alt="Foto Profil"
                >

                <span>
                    Profil
                </span>

            </a>

        </nav>


        <!-- =====================================
             LOGOUT
        ====================================== -->

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
         KONTEN UTAMA
    ====================================== -->

    <main class="main-content">


        <div class="buat-container">


            <h2>
                Buat Postingan
            </h2>


            <p class="buat-subtitle">
                Bagikan foto kamu ke Instagram Clone
            </p>


            <!-- =================================
                 PESAN ERROR
            ================================== -->

            <?php if ($pesan != ""): ?>

                <div class="pesan <?= $jenis_pesan; ?>">

                    <?= htmlspecialchars($pesan); ?>

                </div>

            <?php endif; ?>



            <!-- =================================
                 FORM POSTING
            ================================== -->

            <form
                action="buat.php"
                method="POST"
                enctype="multipart/form-data"
                class="form-post"
            >


                <!-- FOTO -->

                <label for="gambar">
                    Pilih Foto
                </label>


                <div class="upload-box">

                    <i class="fa-regular fa-image"></i>

                    <p>
                        Pilih foto dari komputer
                    </p>

                    <span>
                        JPG, JPEG, PNG, GIF, WEBP
                    </span>


                    <input
                        type="file"
                        name="gambar"
                        id="gambar"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        required
                    >

                </div>



                <!-- PREVIEW -->

                <div id="preview-container">

                    <img
                        id="preview"
                        src=""
                        alt="Preview"
                    >

                </div>



                <!-- DESKRIPSI -->

                <label for="deskripsi">
                    Caption
                </label>


                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    placeholder="Tulis caption..."
                    rows="5"
                    maxlength="1000"
                ></textarea>


                <small>
                    Maksimal 1000 karakter
                </small>



                <!-- BUTTON -->

                <button
                    type="submit"
                    name="posting"
                    class="btn-posting"
                >

                    <i class="fa-solid fa-paper-plane"></i>

                    Posting

                </button>


            </form>


            <!-- KEMBALI -->

            <a
                href="index.php"
                class="back-link"
            >

                ← Kembali ke Beranda

            </a>


        </div>


    </main>


</div>



<!-- =====================================
     PREVIEW FOTO
====================================== -->

<script>

const inputGambar = document.getElementById("gambar");
const preview = document.getElementById("preview");
const previewContainer = document.getElementById("preview-container");

inputGambar.addEventListener("change", function () {

    const file = this.files[0];

    if (file) {

        const reader = new FileReader();

        reader.onload = function (e) {

            preview.src = e.target.result;

            previewContainer.style.display = "block";

        };

        reader.readAsDataURL(file);

    } else {

        previewContainer.style.display = "none";

    }

});

</script>


</body>
</html>