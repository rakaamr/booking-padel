<?php

include "config/koneksi.php";

if(isset($_POST['register'])){

    $nama = $_POST['nama'];
    $email = $_POST['email'];

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $cek = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE email='$email'"
    );

    if(mysqli_num_rows($cek) > 0){

        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

        <script>

        document.addEventListener('DOMContentLoaded', function(){

            Swal.fire({
                icon: 'warning',
                title: 'Email Sudah Terdaftar',
                text: 'Silakan gunakan email lain',
                confirmButtonColor: '#1ea84a'
            });

        });

        </script>
        ";

    }else{

        mysqli_query(
            $conn,
            "INSERT INTO users (nama,email,password)
            VALUES ('$nama','$email','$password')"
        );

        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

        <script>

        document.addEventListener('DOMContentLoaded', function(){

            Swal.fire({
                icon: 'success',
                title: 'Register Berhasil',
                text: 'Akun berhasil dibuat',
                confirmButtonColor: '#1ea84a'
            }).then(() => {
                window.location='login.php';
            });

        });

        </script>
        ";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Register</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet" href="assets/style.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="login-page">

<div class="auth-card">

    <div class="auth-logo">

        <h1>🎾 Padel Booking</h1>

        <p>
            Buat akun dan mulai booking lapangan favoritmu
        </p>

    </div>

    <form method="POST">

        <div class="mb-3">

            <label>👤 Nama Lengkap</label>

            <input
            type="text"
            name="nama"
            class="form-control"
            placeholder="Masukkan nama lengkap"
            required>

        </div>

        <div class="mb-3">

            <label>📧 Email</label>

            <input
            type="email"
            name="email"
            class="form-control"
            placeholder="Masukkan email"
            required>

        </div>

        <div class="mb-3">

            <label>🔒 Password</label>

            <div class="input-group">

                <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="Masukkan password"
                required>

                <button
                type="button"
                class="btn btn-outline-success"
                onclick="lihatPassword()">

                    👁️

                </button>

            </div>

        </div>

        <button
        type="submit"
        name="register"
        class="auth-btn">

            Daftar Sekarang

        </button>

    </form>

    <div class="auth-link">

        Sudah punya akun?

        <a href="login.php">
            Login
        </a>

    </div>

</div>

<script src="assets/script.js"></script>

<script>

function lihatPassword(){

    let pass =
    document.getElementById("password");

    if(pass.type == "password"){

        pass.type = "text";

    }else{

        pass.type = "password";

    }

}

</script>

</body>

</html>