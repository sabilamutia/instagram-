<?php

session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user_login = $_SESSION['id_user'];

$error = "";


// =====================================
// UPLOAD STORY
// =====================================

if (isset($_POST['upload_story'])) {

    if (
        isset($_FILES['gambar']) &&
        $_FILES['gambar']['error'] === 0
    ) {

        $nama_file = $_FILES['gambar']['name'];
        $tmp_file  = $_FILES['gambar']['tmp_name'];
        $ukuran    = $_FILES['gambar']['size'];

        $extension = strtolower(
            pathinfo($nama_file, PATHINFO_EXTENSION)
        );

        $extension_valid = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];


        if ($ukuran > 5 * 1024 * 1024) {

            $error = "Ukuran foto maksimal 5 MB.";

        } elseif (!in_array($extension, $extension_valid)) {

            $error = "Format foto tidak didukung.";

        } else {

            // Buat folder uploads jika belum ada
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }


            // Nama file baru
            $nama_baru =
                'story_' .
                uniqid() .
                '.' .
                $extension;


            $lokasi =
                'uploads/' .
                $nama_baru;


            if (move_uploaded_file(
                $tmp_file,
                $lokasi
            )) {

                $query = mysqli_query(
                    $koneksi,
                    "INSERT INTO story
                    (id_user, gambar)
                    VALUES
                    ('$id_user_login', '$nama_baru')"
                );


                if ($query) {

                    header("Location: index.php");
                    exit();

                } else {

                    $error =
                        "Story gagal disimpan ke database.";
                }

            } else {

                $error =
                    "Gagal mengupload foto.";
            }
        }

    } else {

        $error =
            "Silakan pilih foto terlebih dahulu.";
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

    <title>Buat Story</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

</head>


<body>


<div class="app-layout">


    <!-- SIDEBAR -->

    <aside class="sidebar">

        <h1 class="brand-logo">
            Instagram
        </h1>


        <nav class="nav-menu">

            <a
                href="index.php"
                class="nav-item"
            >

                <i class="fa-solid fa-house"></i>

                <span>
                    Beranda
                </span>

            </a>


            <a
                href="pencarian.php"
                class="nav-item"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                <span>
                    Pencarian
                </span>

            </a>


            <a
                href="buat.php"
                class="nav-item active"
            >

                <i class="fa-regular fa-square-plus"></i>

                <span>
                    Buat
                </span>

            </a>


            <a
                href="profil.php"
                class="nav-item"
            >

                <i class="fa-regular fa-user"></i>

                <span>
                    Profil
                </span>

            </a>

        </nav>


        <div class="sidebar-bottom">

            <a
                href="logout.php"
                class="nav-item"
                onclick="return confirm('Apakah kamu yakin ingin logout?');"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>
                    Logout
                </span>

            </a>

        </div>

    </aside>



    <!-- KONTEN -->

    <main class="main-content">

        <div class="story-upload-container">

            <h2>

                <i class="fa-solid fa-camera"></i>

                Buat Story

            </h2>


            <?php if ($error != ""): ?>

                <div class="story-error">

                    <?= htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>


            <form
                action="story.php"
                method="POST"
                enctype="multipart/form-data"
                class="story-upload-form"
            >

                <div class="story-upload-icon">

                    <i class="fa-regular fa-images"></i>

                </div>


                <label>

                    Pilih Foto untuk Story

                </label>


                <input
                    type="file"
                    name="gambar"
                    accept="image/*"
                    required
                    onchange="previewStory(event)"
                >


                <div class="story-preview">

                    <img
                        id="preview"
                        style="display:none;"
                    >

                </div>


                <button
                    type="submit"
                    name="upload_story"
                >

                    <i class="fa-solid fa-paper-plane"></i>

                    Bagikan ke Story

                </button>

            </form>


            <a
                href="index.php"
                class="back-link"
            >

                ← Kembali ke Beranda

            </a>

        </div>

    </main>

</div>


<script>

function previewStory(event) {

    const preview =
        document.getElementById("preview");

    const file =
        event.target.files[0];

    if (file) {

        preview.src =
            URL.createObjectURL(file);

        preview.style.display =
            "block";
    }

}

</script>


</body>

</html>