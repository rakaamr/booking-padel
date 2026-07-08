<?php

include "auth_admin.php";
include "../config/koneksi.php";

$data = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
$totalUser = mysqli_num_rows($data);

?>

<!DOCTYPE html>
<html>
<head>

<title>Kelola User</title>

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
        <h2>👤 Kelola User</h2>
        <p>Daftar pengguna terdaftar pada sistem booking padel.</p>
    </div>
    <a href="dashboard.php" class="btn-back">&larr; Kembali</a>
</div>

<div class="section-card">

    <div class="table-responsive">
    <table class="table table-modern align-middle">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

        <?php if ($totalUser == 0) { ?>
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada user terdaftar.</td></tr>
        <?php } ?>

        <?php while ($d = mysqli_fetch_assoc($data)) {
            $initial = strtoupper(substr($d['nama'], 0, 1));
        ?>

        <tr>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar-sm"><?php echo $initial; ?></div>
                    <div>
                        <div class="fw-bold"><?php echo htmlspecialchars($d['nama']); ?></div>
                        <div class="text-muted" style="font-size:12px;">ID #<?php echo $d['id']; ?></div>
                    </div>
                </div>
            </td>
            <td><?php echo htmlspecialchars($d['email']); ?></td>
            <td><?php echo !empty($d['no_hp']) ? htmlspecialchars($d['no_hp']) : '-'; ?></td>
            <td>
                <span class="role-tag <?php echo $d['role'] == 'admin' ? 'admin' : 'user'; ?>">
                    <?php echo ucfirst(htmlspecialchars($d['role'])); ?>
                </span>
            </td>
            <td>
                <?php if ($d['role'] != 'admin') { ?>
                <a href="hapus_user.php?id=<?php echo $d['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')">🗑️ Hapus</a>
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
