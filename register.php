<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

// Jika tombol Daftar diklik
if (isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $nama     = trim($_POST['nama']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Foto profil default
    $foto_profil = 'uploads/image.png';

    // Simpan data ke tabel users
    $query = "INSERT INTO users (username, nama, foto_profil, password)
              VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($koneksi, $query);

    if (!$stmt) {
        $error_msg = "Query gagal: " . mysqli_error($koneksi);
    } else {

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $username,
            $nama,
            $foto_profil,
            $password
        );

        if (mysqli_stmt_execute($stmt)) {

            // Berhasil daftar → ke halaman login
            header("Location: login.php");
            exit();

        } else {

            $error_msg = "Gagal mendaftar: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Instagram</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="auth-container">

        <h2>Register Instagram</h2>

        <?php if (isset($error_msg)) : ?>

            <p style="color: red; font-size: 14px;">
                <?= htmlspecialchars($error_msg); ?>
            </p>

        <?php endif; ?>


        <form action="register.php" method="POST">

            <input
                type="text"
                name="username"
                placeholder="Username"
                required
            >

            <br><br>


            <input
                type="text"
                name="nama"
                placeholder="Nama Lengkap"
                required
            >

            <br><br>


            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >

            <br><br>


            <button type="submit" name="register">
                Daftar
            </button>

        </form>


        <p>
            Sudah punya akun?
            <a href="login.php">
                Login di sini
            </a>
        </p>

    </div>

</body>

</html>