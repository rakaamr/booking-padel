<?php

include "auth_admin.php";
include "../config/koneksi.php";

if (isset($_POST['simpan'])) {

    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);

    mysqli_query($conn, "
        INSERT INTO lapangan (nama_lapangan, harga)
        VALUES ('$nama', '$harga')
    ");

    header("Location: lapangan.php?added=1");
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id ASC");
$totalLapangan = mysqli_num_rows($data);

$gambarDefault = [
    1 => "indoor mezzanme viewing deck.png",
    2 => "indoor modern santai.png",
    3 => "outdoor konsep resort.png",
    4 => "outdoor modern.png"
];

?>

<!DOCTYPE html>
<html>
<head>

<title>Kelola Lapangan</title>

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
        <h2>🏟️ Kelola Lapangan</h2>
        <p>Tambah, ubah, atau hapus data lapangan padel.</p>
    </div>
    <a href="dashboard.php" class="btn-back">&larr; Kembali</a>
</div>

<?php if (isset($_GET['added'])) { ?>
<div class="alert alert-success shadow-sm">✅ Lapangan berhasil ditambahkan.</div>
<?php } ?>

<div class="section-card">
    <h5 class="mb-3 fw-bold text-success">➕ Tambah Lapangan Baru</h5>

    <form method="POST" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Nama Lapangan</label>
            <input type="text" name="nama" class="form-control" placeholder="Contoh: Lapangan A - Indoor" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Harga per Jam (Rp)</label>
            <input type="number" name="harga" class="form-control" placeholder="150000" required>
        </div>
        <div class="col-md-2">
            <button name="simpan" class="btn btn-success w-100">Tambah</button>
        </div>
    </form>
</div>

<h2 class="mb-3">Daftar Lapangan (<?php echo $totalLapangan; ?>)</h2>

<div class="row g-4">

<?php if ($totalLapangan == 0) { ?>
    <div class="col-12">
        <div class="empty-state">
            <span class="icon">🏟️</span>
            <h4>Belum Ada Lapangan</h4>
            <p>Tambahkan lapangan pertama menggunakan form di atas.</p>
        </div>
    </div>
<?php } ?>

<?php while ($d = mysqli_fetch_assoc($data)) {
    $gambar = isset($gambarDefault[$d['id']]) ? $gambarDefault[$d['id']] : null;
?>

<div class="col-md-4">
    <div class="lapangan-manage-card">

        <?php if ($gambar && file_exists("../assets/img/" . $gambar)) { ?>
            <img src="../assets/img/<?php echo rawurlencode($gambar); ?>" class="w-100" style="height:180px;object-fit:cover;">
        <?php } else { ?>
            <div style="height:180px;background:linear-gradient(135deg,#0d5f2b,#1ea84a);display:flex;align-items:center;justify-content:center;font-size:48px;">🎾</div>
        <?php } ?>

        <div class="lm-body">
            <h5><?php echo htmlspecialchars($d['nama_lapangan']); ?></h5>
            <p class="text-success fw-bold fs-5 mb-3">Rp <?php echo number_format($d['harga']); ?>/Jam</p>

            <div class="d-flex gap-2">
                <a href="edit_lapangan.php?id=<?php echo $d['id']; ?>" class="btn btn-warning btn-sm flex-fill">✏️ Edit</a>
                <a href="hapus_lapangan.php?id=<?php echo $d['id']; ?>" class="btn btn-danger btn-sm flex-fill" onclick="return confirm('Yakin ingin menghapus lapangan ini?')">🗑️ Hapus</a>
            </div>
        </div>

    </div>
</div>

<?php } ?>

</div>

</div>

</body>
</html>
