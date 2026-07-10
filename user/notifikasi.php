<?php

require_once "auth.php";
require_once "../config/koneksi.php";

$id = $_SESSION['id'];

$data = mysqli_query($conn, "
    SELECT booking.*, lapangan.nama_lapangan
    FROM booking
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    WHERE booking.user_id = '$id'
    AND booking.status IN ('Dikonfirmasi', 'Ditolak')
    ORDER BY booking.id DESC
");

$totalData = mysqli_num_rows($data);

// Tandai semua notifikasi sebagai sudah dibaca
mysqli_query($conn, "
    UPDATE booking
    SET notifikasi='Sudah Dibaca'
    WHERE user_id='$id'
");

?>

<!DOCTYPE html>
<html>
<head>

<title>Notifikasi</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">

</head>

<body class="bg-light">

<div class="container mt-4 mb-5">

<div class="page-header">
    <div>
        <h2>🔔 Notifikasi</h2>
        <p>Informasi terbaru seputar status booking lapangan padel kamu.</p>
    </div>
    <a href="dashboard.php" class="btn-back">&larr; Kembali</a>
</div>

<?php if ($totalData == 0) { ?>

<div class="empty-state">
    <span class="icon">🔕</span>
    <h4>Belum Ada Notifikasi</h4>
    <p>Notifikasi akan muncul di sini setelah admin memproses booking kamu.</p>
</div>

<?php } else { ?>

<?php while ($d = mysqli_fetch_assoc($data)) {

    $isRejected = $d['status'] == "Ditolak";

?>

<div class="notif-item <?php echo $isRejected ? 'notif-rejected' : ''; ?>">

    <div class="notif-icon">
        <?php echo $isRejected ? "❌" : "✅"; ?>
    </div>

    <div class="notif-body">
        <h6>
            <?php echo $isRejected ? "Booking Ditolak" : "Booking Dikonfirmasi"; ?>
        </h6>
        <p>
            Booking lapangan <b><?php echo htmlspecialchars($d['nama_lapangan']); ?></b>
            pada tanggal <b><?php echo date("d M Y", strtotime($d['tanggal'])); ?></b>
            jam <b><?php echo htmlspecialchars($d['jam']); ?></b>
            <?php echo $isRejected
                ? "telah <b>ditolak</b> oleh admin. Silakan hubungi admin untuk info lebih lanjut."
                : "telah <b>dikonfirmasi</b> oleh admin. Selamat bermain! 🎾"; ?>
        </p>
    </div>

    <span class="notif-badge">
        <?php echo $isRejected ? "Ditolak" : "Dikonfirmasi"; ?>
    </span>

</div>

<?php } ?>

<?php } ?>

</div>

<script src="../assets/script.js"></script>
</body>
</html>
