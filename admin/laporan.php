<?php

include "auth_admin.php";
include "../config/koneksi.php";

// Total pendapatan keseluruhan (booking terkonfirmasi)
$totalPendapatan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(lapangan.harga) AS total
    FROM booking
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    WHERE booking.status='Dikonfirmasi'
"))['total'];

// Pendapatan bulan ini
$pendapatanBulanIni = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(lapangan.harga) AS total
    FROM booking
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    WHERE booking.status='Dikonfirmasi'
    AND MONTH(booking.tanggal)=MONTH(CURDATE())
    AND YEAR(booking.tanggal)=YEAR(CURDATE())
"))['total'];

// Jumlah booking per status
$jumlahDikonfirmasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status='Dikonfirmasi'"))['total'];
$jumlahPending      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status='Pending'"))['total'];
$jumlahDitolak      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status='Ditolak'"))['total'];

// Pendapatan per lapangan
$perLapangan = mysqli_query($conn, "
    SELECT lapangan.nama_lapangan, COUNT(booking.id) AS jumlah, SUM(lapangan.harga) AS total
    FROM booking
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    WHERE booking.status='Dikonfirmasi'
    GROUP BY lapangan.id
    ORDER BY total DESC
");

$dataLapangan = [];
$maxLapangan = 1;
while ($row = mysqli_fetch_assoc($perLapangan)) {
    $dataLapangan[] = $row;
    if ($row['total'] > $maxLapangan) $maxLapangan = $row['total'];
}

// Pendapatan 6 bulan terakhir
$perBulan = mysqli_query($conn, "
    SELECT DATE_FORMAT(booking.tanggal, '%Y-%m') AS bulan, SUM(lapangan.harga) AS total
    FROM booking
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    WHERE booking.status='Dikonfirmasi'
    AND booking.tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY bulan
    ORDER BY bulan ASC
");

$dataBulan = [];
$maxBulan = 1;
while ($row = mysqli_fetch_assoc($perBulan)) {
    $dataBulan[] = $row;
    if ($row['total'] > $maxBulan) $maxBulan = $row['total'];
}

// Transaksi terkonfirmasi terbaru
$transaksi = mysqli_query($conn, "
    SELECT booking.*, users.nama, lapangan.nama_lapangan, lapangan.harga
    FROM booking
    JOIN users ON booking.user_id = users.id
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    WHERE booking.status='Dikonfirmasi'
    ORDER BY booking.id DESC
    LIMIT 15
");

$namaBulanIndo = [
    '01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
    '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'
];

function formatBulan($bulan, $namaBulanIndo) {
    $parts = explode('-', $bulan);
    return $namaBulanIndo[$parts[1]] . ' ' . $parts[0];
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Laporan Pendapatan</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">

</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
<div class="container">
<a class="navbar-brand">🎾 Dashboard Admin</a>
<div><a href="../logout.php" class="btn btn-danger btn-sm">🚪 Logout</a></div>
</div>
</nav>

<div class="container mt-4 mb-5">

<div class="page-header">
    <div>
        <h2>📊 Laporan Pendapatan</h2>
        <p>Ringkasan pendapatan dan performa booking lapangan padel.</p>
    </div>
    <a href="dashboard.php" class="btn-back">&larr; Kembali</a>
</div>

<div class="row g-4 mb-4">

    <div class="col-md-3 col-6">
        <div class="report-summary-card green">
            <p>💰 Total Pendapatan</p>
            <h3>Rp <?php echo number_format($totalPendapatan ?? 0); ?></h3>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="report-summary-card blue">
            <p>📆 Pendapatan Bulan Ini</p>
            <h3>Rp <?php echo number_format($pendapatanBulanIni ?? 0); ?></h3>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="report-summary-card orange">
            <p>⏳ Booking Pending</p>
            <h3><?php echo $jumlahPending; ?></h3>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="report-summary-card red">
            <p>❌ Booking Ditolak</p>
            <h3><?php echo $jumlahDitolak; ?></h3>
        </div>
    </div>

</div>

<div class="row g-4">

    <div class="col-lg-6">
        <div class="section-card h-100">
            <h5 class="fw-bold text-success mb-4">🏟️ Pendapatan per Lapangan</h5>

            <?php if (empty($dataLapangan)) { ?>
                <p class="text-muted">Belum ada data pendapatan.</p>
            <?php } ?>

            <?php foreach ($dataLapangan as $row) { ?>
            <div class="bar-row">
                <div class="bar-label"><?php echo htmlspecialchars($row['nama_lapangan']); ?></div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: <?php echo round(($row['total'] / $maxLapangan) * 100); ?>%"></div>
                </div>
                <div class="bar-value">Rp <?php echo number_format($row['total']); ?></div>
            </div>
            <?php } ?>

        </div>
    </div>

    <div class="col-lg-6">
        <div class="section-card h-100">
            <h5 class="fw-bold text-success mb-4">📈 Pendapatan 6 Bulan Terakhir</h5>

            <?php if (empty($dataBulan)) { ?>
                <p class="text-muted">Belum ada data pendapatan.</p>
            <?php } ?>

            <?php foreach ($dataBulan as $row) { ?>
            <div class="bar-row">
                <div class="bar-label"><?php echo formatBulan($row['bulan'], $namaBulanIndo); ?></div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: <?php echo round(($row['total'] / $maxBulan) * 100); ?>%"></div>
                </div>
                <div class="bar-value">Rp <?php echo number_format($row['total']); ?></div>
            </div>
            <?php } ?>

        </div>
    </div>

</div>

<div class="section-card mt-4">
    <h5 class="fw-bold text-success mb-3">🧾 Rincian Transaksi Terkonfirmasi Terbaru</h5>

    <div class="table-responsive">
    <table class="table table-modern align-middle">
        <thead>
            <tr>
                <th>User</th>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Metode</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($transaksi) == 0) { ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada transaksi terkonfirmasi.</td></tr>
            <?php } ?>
            <?php while ($t = mysqli_fetch_assoc($transaksi)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($t['nama']); ?></td>
                <td><?php echo htmlspecialchars($t['nama_lapangan']); ?></td>
                <td><?php echo date("d M Y", strtotime($t['tanggal'])); ?></td>
                <td><?php echo htmlspecialchars($t['jam']); ?></td>
                <td><?php echo htmlspecialchars($t['metode_pembayaran']); ?></td>
                <td class="fw-bold text-success">Rp <?php echo number_format($t['harga']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
</div>

</div>

</body>
</html>
