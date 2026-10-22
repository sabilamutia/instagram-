<?php
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

$error_msg = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string(
        $koneksi,
        $_POST['email']
    );

    $password = $_POST['password'];

    $result = mysqli_query(
        $koneksi,
        "SELECT * FROM user WHERE email = '$email'"
    );

    if ($result && mysqli_num_rows($result) === 1) {

        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {

            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['username'] = $row['username'];

            header("Location: index.php");
            exit();

        } else {

            $error_msg = "Password salah!";

        }

    } else {

        $error_msg = "Email tidak ditemukan!";

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

    <title>Login - Instagram</title>


    <!-- CSS LOGIN -->
    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #fafafa;

            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;
        }


        .auth-container {
            width: 350px;

            background: white;

            border: 1px solid #ddd;

            border-radius: 10px;

            padding: 40px;

            text-align: center;

            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }


        .auth-container h2 {
            font-family: cursive;

            font-size: 32px;

            margin-top: 0;

            margin-bottom: 30px;

            color: #111;
        }


        .auth-container form {
            width: 100%;
        }


        .auth-container input {
            width: 100%;

            padding: 12px;

            margin-bottom: 12px;

            border: 1px solid #ccc;

            border-radius: 6px;

            background: #fafafa;

            font-size: 14px;

            outline: none;
        }


        .auth-container input:focus {
            border-color: #999;

            background: white;
        }


        .auth-container button {
            width: 100%;

            padding: 12px;

            border: none;

            border-radius: 6px;

            background: #0095f6;

            color: white;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;

            margin-top: 5px;
        }


        .auth-container button:hover {
            background: #0077cc;
        }


        .error-message {
            color: red;

            font-size: 14px;

            margin-bottom: 15px;
        }


        .auth-container p {
            font-size: 14px;

            margin-top: 25px;

            color: #555;
        }


        .auth-container a {
            color: #0095f6;

            text-decoration: none;

            font-weight: bold;
        }


        .auth-container a:hover {
            text-decoration: underline;
        }

    </style>

</head>


<body>


    <div class="auth-container">

        <h2>
            Instagram
        </h2>


        <?php if (!empty($error_msg)): ?>

            <div class="error-message">
                <?= htmlspecialchars($error_msg); ?>
            </div>

        <?php endif; ?>


        <form
            action="login.php"
            method="POST"
        >

            <input
                type="email"
                name="email"
                placeholder="Email"
                required
            >


            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >


            <button
                type="submit"
                name="login"
            >
                Masuk
            </button>

        </form>


        <p>
            Belum punya akun?
            <a href="register.php">
                Daftar di sini
            </a>
        </p>

    </div>


</body>

</html>