<?php

require_once "auth.php";
require_once "../config/koneksi.php";

$id = $_SESSION['id'];

$data = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($data);

$successMsg = "";
$errorMsg   = "";

if (isset($_POST['simpan'])) {

    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $hp     = mysqli_real_escape_string($conn, $_POST['hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $fotoUpdateSql = "";

    // Upload foto profil jika ada file baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExt)) {

            $folderFoto = "../assets/profile_photos/";
            if (!is_dir($folderFoto)) {
                mkdir($folderFoto, 0755, true);
            }

            $namaFoto = "user_" . $id . "_" . time() . "." . $ext;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $folderFoto . $namaFoto)) {

                // Hapus foto lama jika ada
                if (!empty($user['foto']) && file_exists($folderFoto . $user['foto'])) {
                    @unlink($folderFoto . $user['foto']);
                }

                $fotoUpdateSql = ", foto='" . mysqli_real_escape_string($conn, $namaFoto) . "'";
            } else {
                $errorMsg = "Gagal mengunggah foto profil.";
            }
        } else {
            $errorMsg = "Format foto tidak didukung. Gunakan JPG, PNG, atau WEBP.";
        }
    }

    if (empty($errorMsg)) {

        mysqli_query($conn, "
            UPDATE users
            SET nama='$nama', no_hp='$hp', alamat='$alamat' $fotoUpdateSql
            WHERE id='$id'
        ");

        header("Location: profile.php?success=1");
        exit;
    }

    // Refresh data user (biar form tetap terisi walau ada error foto)
    $data = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
    $user = mysqli_fetch_assoc($data);
}

if (isset($_GET['success'])) {
    $successMsg = "Profil berhasil diperbarui!";
}

$fotoUrl = (!empty($user['foto']) && file_exists("../assets/profile_photos/" . $user['foto']))
    ? "../assets/profile_photos/" . $user['foto']
    : "https://ui-avatars.com/api/?name=" . urlencode($user['nama']) . "&background=1ea84a&color=fff&size=256";

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Profil</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">

</head>

<body class="bg-light">

<div class="container mt-4 mb-5">

<div class="page-header">
    <div>
        <h2>👤 Edit Profil</h2>
        <p>Perbarui informasi akun dan foto profil kamu.</p>
    </div>
    <a href="dashboard.php" class="btn-back">&larr; Kembali</a>
</div>

<?php if (!empty($successMsg)) { ?>
<div class="alert alert-success shadow-sm"><?php echo htmlspecialchars($successMsg); ?></div>
<?php } ?>

<?php if (!empty($errorMsg)) { ?>
<div class="alert alert-danger shadow-sm"><?php echo htmlspecialchars($errorMsg); ?></div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

<div class="profile-wrapper">

    <div class="profile-side">

        <div class="avatar-wrap">
            <img src="<?php echo $fotoUrl; ?>" class="avatar-img" id="previewFoto">

            <button type="button" class="avatar-edit-btn" onclick="document.getElementById('inputFoto').click()">
                📷
            </button>

            <input type="file" name="foto" id="inputFoto" accept="image/png, image/jpeg, image/webp" style="display:none;">
        </div>

        <h4><?php echo htmlspecialchars($user['nama']); ?></h4>
        <div><?php echo htmlspecialchars($user['email']); ?></div>
        <span class="role-badge">🎾 <?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
    </div>

    <div class="profile-form-card">

        <h5>Informasi Akun</h5>

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" disabled>
            <div class="form-text">Email tidak dapat diubah.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">No HP</label>
            <input type="text" name="hp" value="<?php echo htmlspecialchars($user['no_hp'] ?? ''); ?>" class="form-control" placeholder="Contoh: 081234567890">
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap"><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
        </div>

        <button name="simpan" class="btn btn-success px-4">💾 Simpan Perubahan</button>
        <a href="dashboard.php" class="btn btn-secondary px-4">Batal</a>

    </div>

</div>

</form>

</div>

<script>
document.getElementById('inputFoto').addEventListener('change', function(e){
    const file = e.target.files[0];
    if (file) {
        document.getElementById('previewFoto').src = URL.createObjectURL(file);
    }
});
</script>

<script src="../assets/script.js"></script>
</body>
</html>
