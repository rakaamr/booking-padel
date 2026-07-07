<?php

$host="localhost";
$user="root";
$pass="";
$db="booking_padel";

$conn=mysqli_connect(
$host,
$user,
$pass,
$db
);

if(!$conn){

die("Koneksi Gagal");

}

?>