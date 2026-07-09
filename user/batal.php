<?php

require_once "auth.php";
require_once "../config/koneksi.php";

$id = $_GET['id'];

mysqli_query($conn,"
DELETE FROM booking
WHERE id='$id'
AND status='Pending'
");

header("Location: riwayat.php");

?>