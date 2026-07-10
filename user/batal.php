<?php
/**
 * batal.php
 * -----------------------------------------------------------------------
 * Modul  : Booking Lapangan (Sisi User)
 * Dikerjakan oleh : Anggota 2 (Booking & Interaktivitas JavaScript)
 *
 * Membatalkan (Delete) booking milik user sendiri, hanya jika statusnya
 * masih Pending.
 * -----------------------------------------------------------------------
 */

include "auth.php";
include "config/koneksi.php";

$id      = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['id'];

mysqli_query($conn, "
    DELETE FROM booking
    WHERE id = '$id'
    AND user_id = '$user_id'
    AND status = 'Pending'
");

header("Location: riwayat.php");
exit;
