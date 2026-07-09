<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "booking_padel";

$conn = mysqli_connect($host, $user, $pass, $db);

if($conn){
    echo "Koneksi Database Berhasil";
}else{
    echo "Koneksi Database Gagal : " . mysqli_connect_error();
}

?>