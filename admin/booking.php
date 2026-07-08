<?php

include "auth_admin.php";
include "../config/koneksi.php";

if (isset($_GET['konfirmasi'])) {
    $id = intval($_GET['konfirmasi']);
    mysqli_query($conn, "
        UPDATE booking
        SET status='Dikonfirmasi', notifikasi='Belum Dibaca'
        WHERE id='$id'
    ");
    header("Location: booking.php?msg=confirmed");
    exit;
}

if (isset($_GET['tolak'])) {
    $id = intval($_GET['tolak']);
    mysqli_query($conn, "
        UPDATE booking
        SET status='Ditolak', notifikasi='Belum Dibaca'
        WHERE id='$id'
    ");
    header("Location: booking.php?msg=rejected");
    exit;
}

$filter = isset($_GET['status']) ? $_GET['status'] : 'Semua';
$where  = "";

if (in_array($filter, ['Pending', 'Dikonfirmasi', 'Ditolak'])) {
    $filterSafe = mysqli_real_escape_string($conn, $filter);
    $where = "WHERE booking.status = '$filterSafe'";
}

$data = mysqli_query($conn, "
    SELECT booking.*, users.nama, lapangan.nama_lapangan
    FROM booking
    JOIN users ON booking.user_id = users.id
    JOIN lapangan ON booking.lapangan_id = lapangan.id
    $where
    ORDER BY booking.id DESC
");

$totalData = mysqli_num_rows($data);

?>

<!DOCTYPE html>
<html>
<head>

<title>Data Booking</title>

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
        <h2>📅 Data Booking</h2>
        <p>Kelola dan konfirmasi booking dari pengguna.</p>
    </div>
    <a href="dashboard.php" class="btn-back">&larr; Kembali</a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'confirmed') { ?>
<div class="alert alert-success shadow-sm">✅ Booking berhasil dikonfirmasi.</div>
<?php } elseif (isset($_GET['msg']) && $_GET['msg'] == 'rejected') { ?>
<div class="alert alert-danger shadow-sm">❌ Booking telah ditolak.</div>
<?php } ?>

<div class="section-card">

    <div class="filter-tabs">
        <a href="?status=Semua" class="<?php echo $filter=='Semua'?'active':''; ?>">Semua</a>
        <a href="?status=Pending" class="<?php echo $filter=='Pending'?'active':''; ?>">⏳ Pending</a>
        <a href="?status=Dikonfirmasi" class="<?php echo $filter=='Dikonfirmasi'?'active':''; ?>">✅ Dikonfirmasi</a>
        <a href="?status=Ditolak" class="<?php echo $filter=='Ditolak'?'active':''; ?>">❌ Ditolak</a>
    </div>

    <div class="table-responsive">
    <table class="table table-modern align-middle">
        <thead>
            <tr>
                <th>User</th>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Metode</th>
                <th>Bukti</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

        <?php if ($totalData == 0) { ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data booking.</td></tr>
        <?php } ?>

        <?php while ($d = mysqli_fetch_assoc($data)) {
            $cls = "status-pending";
            if ($d['status'] == "Dikonfirmasi") $cls = "status-dikonfirmasi";
            if ($d['status'] == "Ditolak") $cls = "status-ditolak";
        ?>

        <tr>
            <td><?php echo htmlspecialchars($d['nama']); ?></td>
            <td><?php echo htmlspecialchars($d['nama_lapangan']); ?></td>
            <td><?php echo date("d M Y", strtotime($d['tanggal'])); ?></td>
            <td><?php echo htmlspecialchars($d['jam']); ?></td>
            <td><?php echo htmlspecialchars($d['metode_pembayaran']); ?></td>
            <td>
                <?php if (!empty($d['bukti_pembayaran'])) { ?>
                <a href="../assets/bukti/<?php echo htmlspecialchars($d['bukti_pembayaran']); ?>" target="_blank" class="btn btn-info btn-sm">Lihat</a>
                <?php } else { echo "-"; } ?>
            </td>
            <td><span class="status-pill <?php echo $cls; ?>"><?php echo htmlspecialchars($d['status']); ?></span></td>
            <td>
                <?php if ($d['status'] == "Pending") { ?>
                <div class="d-flex gap-1">
                    <a href="?konfirmasi=<?php echo $d['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi booking ini?')">Konfirmasi</a>
                    <a href="?tolak=<?php echo $d['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak booking ini?')">Tolak</a>
                </div>
                <?php } else { ?>
                    <span class="text-muted">-</span>
                <?php } ?>
            </td>
        </tr>

        <?php } ?>

        </tbody>
    </table>
    </div>

</div>

</div>

</body>
</html>
