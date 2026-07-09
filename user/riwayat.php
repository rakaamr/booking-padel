<?php

require_once "auth.php";
require_once "../config/koneksi.php";

$id = $_SESSION['id'];

$data = mysqli_query($conn, "
    SELECT booking.*, lapangan.nama_lapangan
    FROM booking
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    WHERE booking.user_id = '$id'
    ORDER BY booking.id DESC
");

$totalData = mysqli_num_rows($data);

?>

<!DOCTYPE html>
<html>
<head>

<title>Riwayat Booking</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">

</head>

<body class="bg-light">

<div class="container mt-4 mb-5">

<div class="page-header">
    <div>
        <h2>📋 Riwayat Booking</h2>
        <p>Lihat seluruh riwayat booking lapangan padel kamu di sini.</p>
    </div>
    <a href="dashboard.php" class="btn-back">&larr; Kembali</a>
</div>

<?php if (isset($_GET['success'])) { ?>
<div class="alert alert-success shadow-sm">
    <b>🎉 Booking berhasil dikirim!</b>
    Booking kamu akan diproses dan dikonfirmasi oleh admin. Silakan cek status di bawah ini.
</div>
<?php } ?>

<?php if ($totalData == 0) { ?>

<div class="empty-state">
    <span class="icon">🎾</span>
    <h4>Belum Ada Riwayat Booking</h4>
    <p>Kamu belum pernah booking lapangan. Yuk booking lapangan favoritmu sekarang!</p>
    <a href="dashboard.php#lapangan" class="btn btn-success">Booking Sekarang</a>
</div>

<?php } else { ?>

<?php while ($d = mysqli_fetch_assoc($data)) {

    $statusClass = "status-pending";
    $statusIcon  = "⏳";

    if ($d['status'] == "Dikonfirmasi") {
        $statusClass = "status-dikonfirmasi";
        $statusIcon  = "✅";
    } elseif ($d['status'] == "Ditolak") {
        $statusClass = "status-ditolak";
        $statusIcon  = "❌";
    }
?>

<div class="riwayat-card">

    <div class="riwayat-top">
        <h5>🎾 <?php echo htmlspecialchars($d['nama_lapangan']); ?></h5>
        <span class="status-pill <?php echo $statusClass; ?>">
            <?php echo $statusIcon . " " . htmlspecialchars($d['status']); ?>
        </span>
    </div>

    <div class="riwayat-meta">
        <span>📅 <?php echo date("d M Y", strtotime($d['tanggal'])); ?></span>
        <span>⏰ <?php echo htmlspecialchars($d['jam']); ?></span>
        <span>💳 <?php echo htmlspecialchars($d['metode_pembayaran']); ?></span>
    </div>

    <div class="riwayat-actions">

        <?php if (!empty($d['bukti_pembayaran'])) { ?>
        <a href="../assets/bukti/<?php echo htmlspecialchars($d['bukti_pembayaran']); ?>" target="_blank" class="btn btn-info btn-sm">
            🧾 Lihat Bukti Pembayaran
        </a>
        <?php } ?>

        <?php if ($d['status'] == "Pending") { ?>
        <a
        href="batal.php?id=<?php echo $d['id']; ?>"
        class="btn btn-danger btn-sm"
        onclick="return confirm('Yakin ingin membatalkan booking ini?')">
            🗑️ Batalkan
        </a>
        <?php } ?>

    </div>

</div>

<?php } ?>

<?php } ?>

</div>

<script src="../assets/script.js"></script>
</body>
</html>
