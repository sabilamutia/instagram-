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
// PROSES TAMBAH KOMENTAR
// =====================================

if (isset($_POST['submit_komentar'])) {

    $id_post = (int) $_POST['id_post'];

    $komentar = mysqli_real_escape_string(
        $koneksi,
        $_POST['komentar']
    );


    mysqli_query(
        $koneksi,
        "INSERT INTO komentar
        (id_user, id_post, komentar)

        VALUES
        ('$id_user_login', '$id_post', '$komentar')"
    );


    header("Location: index.php");
    exit();

}


// =====================================
// PROSES LIKE / UNLIKE
// =====================================

if (
    isset($_GET['action']) &&
    $_GET['action'] == 'like'
) {

    $id_post = (int) $_GET['id_post'];


    $cek_like = mysqli_query(
        $koneksi,

        "SELECT *
         FROM likepost

         WHERE id_user = '$id_user_login'

         AND id_post = '$id_post'"
    );


    if (
        $cek_like &&
        mysqli_num_rows($cek_like) > 0
    ) {

        // UNLIKE

        mysqli_query(
            $koneksi,

            "DELETE FROM likepost

             WHERE id_user = '$id_user_login'

             AND id_post = '$id_post'"
        );

    } else {

        // LIKE

        mysqli_query(
            $koneksi,

            "INSERT INTO likepost
            (id_user, id_post)

            VALUES
            ('$id_user_login', '$id_post')"
        );

    }


    header("Location: index.php");
    exit();

}


// =====================================
// DATA USER YANG LOGIN
// =====================================

$query_user = mysqli_query(
    $koneksi,

    "SELECT *
     FROM user

     WHERE id_user = '$id_user_login'"
);


$user = mysqli_fetch_assoc($query_user);


$foto_user_login =
    !empty($user['foto_profil'])

    ? $user['foto_profil']

    : 'https://picsum.photos/150';

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
        Instagram Clone
    </title>


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
                class="nav-item active"
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
                class="nav-item"
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

                    onerror="
                        this.src='https://picsum.photos/150';
                    "
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

                onclick="
                    return confirm(
                        'Apakah kamu yakin ingin logout?'
                    );
                "
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


        <div class="feed-wrapper">


            <!-- =================================
                 STORIES
            ================================== -->


            <div class="stories-container">


                <!-- =================================
                     CERITA SAYA
                ================================== -->


                <a
                    href="story.php"
                    class="story-item story-add"
                >


                    <div class="story-ring">


                        <img
                            src="<?= htmlspecialchars($foto_user_login); ?>"

                            onerror="
                                this.src='https://picsum.photos/150';
                            "
                        >


                        <div class="story-plus">
                            +
                        </div>


                    </div>


                    <span class="story-username">

                        Cerita Anda

                    </span>


                </a>



                <?php

                // =====================================
                // AMBIL 1 STORY TERBARU SETIAP USER
                // =====================================

                $query_stories = mysqli_query(

                    $koneksi,

                    "SELECT

                        s.id_story,
                        s.id_user,
                        s.gambar,

                        u.username,
                        u.foto_profil

                     FROM story s

                     JOIN user u

                        ON s.id_user = u.id_user

                     WHERE s.id_story IN (

                        SELECT
                            MAX(s2.id_story)

                        FROM story s2

                        GROUP BY s2.id_user

                     )

                     ORDER BY s.id_story DESC"

                );

                ?>


                <?php

                if (
                    $query_stories &&
                    mysqli_num_rows($query_stories) > 0
                ):

                ?>


                    <?php

                    while (
                        $story =
                        mysqli_fetch_assoc(
                            $query_stories
                        )
                    ):

                    ?>


                        <?php

                        // =================================
                        // GAMBAR STORY
                        // =================================

                        $gambar_story =
                            $story['gambar'];


                        if (
                            filter_var(
                                $gambar_story,
                                FILTER_VALIDATE_URL
                            )
                        ) {

                            $story_src =
                                $gambar_story;

                        } else {

                            $story_src =
                                'uploads/' .
                                ltrim(
                                    $gambar_story,
                                    '/'
                                );

                        }

                        ?>


                        <!-- =================================
                             STORY USER
                        ================================== -->


                        <a
                            href="lihat_story.php?id_story=<?= (int)$story['id_story']; ?>"
                            class="story-item"
                        >


                            <div class="story-ring">


                                <!-- GAMBAR STORY -->

                                <img
                                    src="<?= htmlspecialchars($story_src); ?>"

                                    onerror="
                                        this.src='https://picsum.photos/150';
                                    "
                                >


                            </div>


                            <span class="story-username">

                                <?= htmlspecialchars(
                                    $story['username']
                                ); ?>

                            </span>


                        </a>


                    <?php

                    endwhile;

                    ?>


                <?php endif; ?>


            </div>



            <!-- =================================
                 FEED POSTINGAN
            ================================== -->


            <div class="feed-posts">


                <?php


                $query_post = mysqli_query(

                    $koneksi,

                    "SELECT

                        post.*,

                        user.username,
                        user.foto_profil

                     FROM post

                     JOIN user

                        ON post.id_user =
                           user.id_user

                     ORDER BY
                        post.id_post DESC"

                );


                ?>


                <?php

                if (
                    $query_post &&
                    mysqli_num_rows($query_post) > 0
                ):

                ?>


                    <?php

                    while (
                        $post =
                        mysqli_fetch_assoc(
                            $query_post
                        )
                    ):

                    ?>


                        <?php

                        $id_post =
                            $post['id_post'];


                        // =================================
                        // JUMLAH LIKE
                        // =================================

                        $query_jumlah_like =
                            mysqli_query(

                                $koneksi,

                                "SELECT
                                    COUNT(*) AS total

                                 FROM likepost

                                 WHERE id_post =
                                       '$id_post'"

                            );


                        $total_like =
                            mysqli_fetch_assoc(
                                $query_jumlah_like
                            )['total'];



                        // =================================
                        // CEK LIKE USER
                        // =================================

                        $cek_user_like =
                            mysqli_query(

                                $koneksi,

                                "SELECT *

                                 FROM likepost

                                 WHERE id_user =
                                       '$id_user_login'

                                 AND id_post =
                                     '$id_post'"

                            );


                        $is_liked =
                            $cek_user_like &&
                            mysqli_num_rows(
                                $cek_user_like
                            ) > 0;



                        // =================================
                        // FOTO PEMBUAT POST
                        // =================================

                        $foto_pembuat =

                            !empty(
                                $post['foto_profil']
                            )

                            ? $post['foto_profil']

                            : 'https://picsum.photos/150';



                        // =================================
                        // GAMBAR / VIDEO POST
                        // =================================

                        $gambar =
                            $post['gambar'];


                        $file_extension =
                            strtolower(

                                pathinfo(

                                    $gambar,

                                    PATHINFO_EXTENSION

                                )

                            );


                        $is_video =
                            in_array(

                                $file_extension,

                                [
                                    'mp4',
                                    'webm',
                                    'ogg'
                                ]

                            );


                        if (

                            filter_var(

                                $gambar,

                                FILTER_VALIDATE_URL

                            )

                        ) {

                            $media_src =
                                $gambar;

                        } else {

                            $media_src =
                                'uploads/' .
                                ltrim(
                                    $gambar,
                                    '/'
                                );

                        }

                        ?>


                        <!-- =================================
                             POST
                        ================================== -->


                        <article
                            class="post-card"
                        >


                            <!-- POST HEADER -->

                            <header
                                class="post-header"
                            >


                                <img
                                    src="<?= htmlspecialchars($foto_pembuat); ?>"

                                    class="avatar-small"

                                    onerror="
                                        this.src='https://picsum.photos/150';
                                    "
                                >


                                <span
                                    class="post-author"
                                >

                                    <?= htmlspecialchars(
                                        $post['username']
                                    ); ?>

                                </span>


                            </header>



                            <!-- MEDIA POST -->

                            <div
                                class="post-media"
                            >


                                <?php

                                if ($is_video):

                                ?>


                                    <video
                                        controls
                                        muted
                                        loop
                                    >

                                        <source
                                            src="<?= htmlspecialchars($media_src); ?>"
                                            type="video/<?= htmlspecialchars($file_extension); ?>"
                                        >


                                        Browser kamu tidak
                                        mendukung video.


                                    </video>


                                <?php else: ?>


                                    <img
                                        src="<?= htmlspecialchars($media_src); ?>"

                                        onerror="
                                            this.src='https://picsum.photos/600/400';
                                        "
                                    >


                                <?php endif; ?>


                            </div>



                            <!-- POST BODY -->

                            <div
                                class="post-body"
                            >


                                <!-- AKSI -->

                                <div
                                    class="post-actions"
                                >


                                    <!-- LIKE -->

                                    <a
                                        href="index.php?action=like&id_post=<?= $id_post; ?>"

                                        class="action-icon"
                                    >


                                        <?php

                                        if ($is_liked):

                                        ?>

                                            <i
                                                class="fa-solid fa-heart liked"
                                            ></i>


                                        <?php else: ?>


                                            <i
                                                class="fa-regular fa-heart"
                                            ></i>


                                        <?php endif; ?>


                                    </a>



                                    <!-- KOMENTAR -->

                                    <i
                                        class="fa-regular fa-comment action-icon"
                                    ></i>



                                    <!-- SHARE -->

                                    <i
                                        class="fa-regular fa-paper-plane action-icon"
                                    ></i>


                                </div>



                                <!-- JUMLAH LIKE -->

                                <div
                                    class="likes-count"
                                >

                                    <b>

                                        <?= $total_like; ?>

                                        Suka

                                    </b>

                                </div>



                                <!-- CAPTION -->

                                <div
                                    class="caption"
                                >

                                    <b>

                                        <?= htmlspecialchars(
                                            $post['username']
                                        ); ?>

                                    </b>


                                    <?= htmlspecialchars(
                                        $post['deskripsi'] ?? ''
                                    ); ?>


                                </div>



                                <!-- KOMENTAR -->

                                <div
                                    class="comments-list"
                                >


                                    <?php

                                    $query_komentar =
                                        mysqli_query(

                                            $koneksi,

                                            "SELECT

                                                komentar.*,

                                                user.username

                                             FROM komentar

                                             JOIN user

                                                ON komentar.id_user =
                                                   user.id_user

                                             WHERE
                                                komentar.id_post =
                                                '$id_post'

                                             ORDER BY
                                                komentar.id_komentar ASC"

                                        );

                                    ?>


                                    <?php

                                    if (
                                        $query_komentar
                                    ):

                                    ?>


                                        <?php

                                        while (

                                            $k =
                                            mysqli_fetch_assoc(
                                                $query_komentar
                                            )

                                        ):

                                        ?>


                                            <div
                                                class="comment-item"
                                            >


                                                <b>

                                                    <?= htmlspecialchars(
                                                        $k['username']
                                                    ); ?>

                                                </b>


                                                <?= htmlspecialchars(
                                                    $k['komentar']
                                                ); ?>


                                            </div>


                                        <?php

                                        endwhile;

                                        ?>


                                    <?php endif; ?>


                                </div>



                                <!-- FORM KOMENTAR -->

                                <form
                                    action="index.php"

                                    method="POST"

                                    class="comment-form"
                                >


                                    <input
                                        type="hidden"

                                        name="id_post"

                                        value="<?= $id_post; ?>"
                                    >


                                    <input
                                        type="text"

                                        name="komentar"

                                        placeholder="Tambah komentar..."

                                        required
                                    >


                                    <button
                                        type="submit"

                                        name="submit_komentar"
                                    >

                                        Kirim

                                    </button>


                                </form>


                            </div>


                        </article>


                    <?php

                    endwhile;

                    ?>


                <?php else: ?>


                    <div class="no-post">

                        Belum ada postingan.

                    </div>


                <?php endif; ?>


            </div>


        </div>



        <!-- =====================================
             SIDEBAR KANAN
        ====================================== -->


        <aside
            class="right-sidebar"
        >


            <div
                class="user-profile-card"
            >


                <img
                    src="<?= htmlspecialchars($foto_user_login); ?>"

                    onerror="
                        this.src='https://picsum.photos/150';
                    "
                >


                <div
                    class="user-info"
                >


                    <b>

                        <?= htmlspecialchars(
                            $user['username']
                        ); ?>

                    </b>


                    <span>

                        <?= htmlspecialchars(
                            $user['nama']
                            ?? 'Pengguna Instagram'
                        ); ?>

                    </span>


                </div>


            </div>


        </aside>


    </main>


</div>


</body>

</html>