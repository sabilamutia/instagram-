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


// =====================================
// CEK ID STORY
// =====================================

if (!isset($_GET['id_story'])) {
    header("Location: index.php");
    exit();
}

$id_story = (int) $_GET['id_story'];


// =====================================
// AMBIL DATA STORY
// =====================================

$query_story = mysqli_query(
    $koneksi,
    "SELECT
        story.id_story,
        story.id_user,
        story.gambar,
        story.dibuat_pada,
        user.username,
        user.foto_profil
     FROM story
     JOIN user
        ON story.id_user = user.id_user
     WHERE story.id_story = '$id_story'
     LIMIT 1"
);


// =====================================
// CEK STORY
// =====================================

if (!$query_story || mysqli_num_rows($query_story) == 0) {

    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Story Tidak Ditemukan</title>
        <style>
            body {
                margin: 0;
                background: #000;
                color: white;
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
            }

            a {
                color: white;
                text-decoration: none;
                background: #333;
                padding: 10px 20px;
                border-radius: 8px;
                display: inline-block;
                margin-top: 15px;
            }
        </style>
    </head>

    <body>

        <div>
            <h2>Story tidak ditemukan</h2>

            <a href='index.php'>
                Kembali ke Beranda
            </a>
        </div>

    </body>
    </html>
    ";

    exit();
}


$story = mysqli_fetch_assoc($query_story);


// =====================================
// DATA STORY
// =====================================

$username = $story['username'];


// =====================================
// FOTO PROFIL
// =====================================

if (!empty($story['foto_profil'])) {

    $foto_profil = $story['foto_profil'];

} else {

    $foto_profil = "https://picsum.photos/150";
}


// =====================================
// GAMBAR STORY
// =====================================

$gambar_story = $story['gambar'];


// Kalau gambar sudah berupa URL
if (filter_var($gambar_story, FILTER_VALIDATE_URL)) {

    $gambar = $gambar_story;

} else {

    $gambar = "uploads/" . ltrim($gambar_story, "/");

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

    <title>
        Story - <?= htmlspecialchars($username); ?>
    </title>


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background: #000;

            color: white;

            font-family: Arial, sans-serif;

            width: 100%;

            height: 100vh;

            overflow: hidden;

        }


        /* =================================
           STORY VIEWER
        ================================= */

        .story-viewer {

            width: 100%;

            height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            position: relative;

            background: #000;

        }


        /* =================================
           GAMBAR STORY
        ================================= */

        .story-image {

            width: 100%;

            height: 100%;

            object-fit: contain;

            display: block;

        }


        /* =================================
           HEADER STORY
        ================================= */

        .story-header {

            position: absolute;

            top: 20px;

            left: 25px;

            z-index: 10;

            display: flex;

            align-items: center;

            gap: 10px;

            background: rgba(0, 0, 0, 0.25);

            padding: 6px 10px;

            border-radius: 20px;

        }


        /* =================================
           FOTO PROFIL
        ================================= */

        .story-profile {

            width: 42px !important;

            height: 42px !important;

            border-radius: 50% !important;

            object-fit: cover !important;

            border: 2px solid white;

            display: block;

        }


        /* =================================
           USERNAME
        ================================= */

        .story-username {

            color: white;

            font-size: 15px;

            font-weight: bold;

        }


        /* =================================
           TOMBOL CLOSE
        ================================= */

        .close-story {

            position: absolute;

            right: 25px;

            top: 18px;

            z-index: 20;

            color: white;

            font-size: 30px;

            text-decoration: none;

            width: 40px;

            height: 40px;

            display: flex;

            justify-content: center;

            align-items: center;

            border-radius: 50%;

            background: rgba(0, 0, 0, 0.3);

        }


        .close-story:hover {

            background: rgba(255, 255, 255, 0.2);

        }


        /* =================================
           JIKA FOTO TIDAK ADA
        ================================= */

        .story-error {

            color: white;

            text-align: center;

        }


        .story-error h2 {

            margin-bottom: 10px;

        }


        .story-error a {

            color: white;

            text-decoration: none;

            background: #333;

            padding: 10px 20px;

            border-radius: 8px;

        }

    </style>

</head>


<body>


<div class="story-viewer">


    <!-- =================================
         FOTO STORY
    ================================= -->

    <?php if (file_exists($gambar) || filter_var($gambar, FILTER_VALIDATE_URL)): ?>

        <img
            src="<?= htmlspecialchars($gambar); ?>"
            class="story-image"
            alt="Story"
        >

    <?php else: ?>

        <div class="story-error">

            <h2>
                Foto Story tidak ditemukan
            </h2>

            <p>
                <?= htmlspecialchars($gambar); ?>
            </p>

            <a href="index.php">
                Kembali ke Beranda
            </a>

        </div>

    <?php endif; ?>


    <!-- =================================
         HEADER USER
    ================================= -->

    <div class="story-header">

        <img
            src="<?= htmlspecialchars($foto_profil); ?>"
            class="story-profile"
            alt="Foto Profil"
            onerror="this.src='https://picsum.photos/150';"
        >

        <span class="story-username">
            <?= htmlspecialchars($username); ?>
        </span>

    </div>


    <!-- =================================
         TOMBOL CLOSE
    ================================= -->

    <a
        href="index.php"
        class="close-story"
        title="Tutup Story"
    >

        <i class="fa-solid fa-xmark"></i>

    </a>


</div>


</body>

</html>