<?php

session_start();

include "config/koneksi.php";

if(isset($_POST['login'])){

$email=$_POST['email'];

$password=$_POST['password'];

$query=mysqli_query($conn,

"SELECT * FROM users
WHERE email='$email'"

);

$data=mysqli_fetch_assoc($query);

if($data){

if(password_verify($password,$data['password'])){

$_SESSION['id']=$data['id'];

$_SESSION['nama']=$data['nama'];

$_SESSION['role']=$data['role'];

if($data['role']=="admin"){

header("Location: admin/dashboard.php");

}else{

header("Location: user/dashboard.php");

}

exit;

}else{

echo '

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

Swal.fire({
icon:"error",
title:"Login Gagal",
text:"Password Salah",
confirmButtonColor:"#1ea84a"
});

</script>

';

}

}else{

echo '

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

Swal.fire({
icon:"warning",
title:"Email Tidak Ditemukan",
text:"Silakan periksa kembali email Anda",
confirmButtonColor:"#1ea84a"
});

</script>

';

}

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Login</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">

</head>

<body class="login-page">

<div class="auth-card">

<div class="auth-logo">
<h1>🎾 Padel Booking</h1>
<p>Silakan login untuk memesan lapangan favoritmu</p>
</div>

<form method="POST">

<div class="mb-3">
<label>Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">
<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<button
type="submit"
class="auth-btn"
name="login">

Masuk

</button>

</form>

<div class="auth-link">

Belum punya akun?

<a href="register.php">
Daftar Sekarang
</a>

</div>

</div>

<script src="assets/script.js"></script>

</body>

</html>