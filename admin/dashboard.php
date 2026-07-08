<?php

include "auth_admin.php";
include "../config/koneksi.php";

$totalUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='user'"))['total'];
$totalLapangan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lapangan"))['total'];
$totalPending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status='Pending'"))['total'];

$pendapatanBulanIni = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(lapangan.harga) AS total
    FROM booking
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    WHERE booking.status='Dikonfirmasi'
    AND MONTH(booking.tanggal) = MONTH(CURDATE())
    AND YEAR(booking.tanggal) = YEAR(CURDATE())
"))['total'];

$bookingTerbaru = mysqli_query($conn, "
    SELECT booking.*, users.nama, lapangan.nama_lapangan
    FROM booking
    JOIN users ON booking.user_id = users.id
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    ORDER BY booking.id DESC
    LIMIT 5
");

?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard Admin</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">

</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
<div class="container">

<a class="navbar-brand">🎾 Dashboard Admin</a>

<div>
<a href="../logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
</div>

</div>
</nav>

<div class="container mt-4 mb-5">

<div class="page-header">
    <div>
        <h2>Selamat Datang, Admin 👋</h2>
        <p>Kelola lapangan, booking, laporan, dan pengguna dari satu tempat.</p>
    </div>
</div>

<div class="row g-4 mb-4">

    <div class="col-md-3 col-6">
        <div class="admin-stat-card blue">
            <div class="admin-stat-icon">👥</div>
            <div>
                <div class="admin-stat-value"><?php echo $totalUser; ?></div>
                <div class="admin-stat-label">Total User</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="admin-stat-card">
            <div class="admin-stat-icon">🎾</div>
            <div>
                <div class="admin-stat-value"><?php echo $totalLapangan; ?></div>
                <div class="admin-stat-label">Total Lapangan</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="admin-stat-card orange">
            <div class="admin-stat-icon">⏳</div>
            <div>
                <div class="admin-stat-value"><?php echo $totalPending; ?></div>
                <div class="admin-stat-label">Booking Pending</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="admin-stat-card purple">
            <div class="admin-stat-icon">💰</div>
            <div>
                <div class="admin-stat-value">Rp <?php echo number_format($pendapatanBulanIni ?? 0); ?></div>
                <div class="admin-stat-label">Pendapatan Bulan Ini</div>
            </div>
        </div>
    </div>

</div>

<h2 class="mb-3">Menu Admin</h2>

<div class="row g-4 mb-4">

    <div class="col-md-3 col-6">
        <a href="lapangan.php" class="admin-menu-card card-lapangan">
            <div class="admin-menu-icon">🏟️</div>
            <h6>Kelola Lapangan</h6>
            <p>Tambah, edit, hapus lapangan</p>
        </a>
    </div>

    <div class="col-md-3 col-6">
        <a href="booking.php" class="admin-menu-card card-booking">
            <div class="admin-menu-icon">📅</div>
            <h6>Data Booking</h6>
            <p>Konfirmasi & kelola booking</p>
        </a>
    </div>

    <div class="col-md-3 col-6">
        <a href="laporan.php" class="admin-menu-card card-laporan">
            <div class="admin-menu-icon">📊</div>
            <h6>Laporan</h6>
            <p>Pendapatan & statistik</p>
        </a>
    </div>

    <div class="col-md-3 col-6">
        <a href="user.php" class="admin-menu-card card-user">
            <div class="admin-menu-icon">👤</div>
            <h6>Kelola User</h6>
            <p>Lihat & kelola pengguna</p>
        </a>
    </div>

</div>

<div class="section-card">
    <h2>Booking Terbaru</h2>

    <div class="table-responsive">
    <table class="table table-modern align-middle">
        <thead>
            <tr>
                <th>User</th>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($bookingTerbaru) == 0) { ?>
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data booking.</td></tr>
        <?php } ?>
        <?php while ($b = mysqli_fetch_assoc($bookingTerbaru)) {
            $cls = "status-pending";
            if ($b['status'] == "Dikonfirmasi") $cls = "status-dikonfirmasi";
            if ($b['status'] == "Ditolak") $cls = "status-ditolak";
        ?>
            <tr>
                <td><?php echo htmlspecialchars($b['nama']); ?></td>
                <td><?php echo htmlspecialchars($b['nama_lapangan']); ?></td>
                <td><?php echo date("d M Y", strtotime($b['tanggal'])); ?></td>
                <td><?php echo htmlspecialchars($b['jam']); ?></td>
                <td><span class="status-pill <?php echo $cls; ?>"><?php echo htmlspecialchars($b['status']); ?></span></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>

    <a href="booking.php" class="btn btn-success mt-2">Lihat Semua Data Booking</a>
</div>

</div>

</body>
</html>
