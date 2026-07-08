<?php

require_once "auth.php";
require_once "../config/koneksi.php";

header("Content-Type: application/json");

$lapangan_id = isset($_GET['lapangan_id']) ? intval($_GET['lapangan_id']) : 0;
$tanggal     = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    echo json_encode(["terisi" => []]);
    exit;
}

$tanggal = mysqli_real_escape_string($conn, $tanggal);

$result = mysqli_query($conn, "
    SELECT jam FROM booking
    WHERE lapangan_id='$lapangan_id'
    AND tanggal='$tanggal'
    AND status != 'Ditolak'
");

$terisi = [];
while ($row = mysqli_fetch_assoc($result)) {
    $terisi[] = $row['jam'];
}

echo json_encode(["terisi" => $terisi]);
