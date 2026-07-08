<?php

include "auth_admin.php";
include "../config/koneksi.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$data = mysqli_query($conn, "SELECT * FROM lapangan WHERE id='$id'");
$d = mysqli_fetch_assoc($data);

if (!$d) {
    header("Location: lapangan.php");
    exit;
}

if (isset($_POST['update'])) {

    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);

    mysqli_query($conn, "
        UPDATE lapangan
        SET nama_lapangan='$nama', harga='$harga'
        WHERE id='$id'
    ");

    header("Location: lapangan.php?updated=1");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Lapangan</title>

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
        <h2>✏️ Edit Lapangan</h2>
        <p>Perbarui nama dan harga lapangan.</p>
    </div>
    <a href="lapangan.php" class="btn-back">&larr; Kembali</a>
</div>

<div class="section-card" style="max-width:560px;">

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Nama Lapangan</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($d['nama_lapangan']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga per Jam (Rp)</label>
            <input type="number" name="harga" value="<?php echo htmlspecialchars($d['harga']); ?>" class="form-control" required>
        </div>

        <button name="update" class="btn btn-success px-4">💾 Update</button>
        <a href="lapangan.php" class="btn btn-secondary px-4">Batal</a>

    </form>

</div>

</div>

</body>
</html>
