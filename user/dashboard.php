<?php

require_once "auth.php";
require_once "../config/koneksi.php";

$data = mysqli_query($conn,"SELECT * FROM lapangan");

$id_user = $_SESSION['id'];

$notif = mysqli_query($conn,"
SELECT *
FROM booking
WHERE
user_id='$id_user'
AND status IN ('Dikonfirmasi','Ditolak')
AND notifikasi='Belum Dibaca'
");

$totalNotif = mysqli_num_rows($notif);

?>

<!DOCTYPE html>
<html>

<head>

<title>Dashboard User</title>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container">

<a class="navbar-brand">

Booking Lapangan Padel

</a>

<div>

<span class="text-white me-3">

Halo,
<b><?php echo $_SESSION['nama']; ?></b>

</span>

<a
href="notifikasi.php"
class="btn btn-warning btn-sm">

Notifikasi

<?php

if($totalNotif>0){

?>

<span class="badge bg-danger">

<?php echo $totalNotif; ?>

</span>

<?php } ?>

</a>

<a
href="riwayat.php"
class="btn btn-primary btn-sm">

Riwayat

</a>

<a
href="profile.php"
class="btn btn-info btn-sm">

Edit Profil

</a>

<a
href="../logout.php"
class="btn btn-danger btn-sm">

Logout

</a>

</div>

</div>

</nav>
<div class="container mt-4">

<div class="hero-padel">

<div class="hero-emoji">
    🎾
</div>

<div class="hero-content">
    <span class="hero-badge">Premium Padel Court</span>

    <h1>
        Booking Lapangan
        <span>Padel</span>
    </h1>

    <p>
        Nikmati pengalaman bermain padel yang modern,
        nyaman, dan cepat.
        Booking lapangan favoritmu hanya dalam beberapa klik.
    </p>

    <a href="#lapangan" class="btn btn-hero">
        Booking Sekarang
    </a>

</div>

</div>

    <h2 id="lapangan" class="mb-4">
        🎾 Daftar Lapangan Premium
    </h2>

    <div class="row">

    <?php while($d=mysqli_fetch_assoc($data)){ ?>

        <div class="col-md-4 mb-4">

            <div class="card shadow">

                <?php

            $gambar = [
                1 => "indoor mezzanme viewing deck.png",
                2 => "indoor modern santai.png",
                3 => "outdoor konsep resort.png",
                4 => "outdoor modern.png"
                
            ];

            ?>

                <div class="card-img-top lapangan-emoji">
                    🎾
                </div>

                <div class="card-body">

                    <h4>
                        <?php echo $d['nama_lapangan']; ?>
                    </h4>

                    <h5 class="text-success">
                        Rp. <?php echo number_format($d['harga']); ?>/Jam
                    </h5>

                    <?php

                    $cek=mysqli_query($conn,"
                    SELECT *
                    FROM booking
                    WHERE lapangan_id='".$d['id']."'
                    AND tanggal=CURDATE()
                    AND status='Dikonfirmasi'
                    ");

                    if(mysqli_num_rows($cek)>0){
                        echo "<span class='badge bg-danger'>Sedang Digunakan</span>";
                    }else{
                        echo "<span class='badge bg-success'>Tersedia</span>";
                    }

                    ?>

                    <br><br>

                    <a
                    href="booking.php?id=<?php echo $d['id'];?>"
                    class="btn btn-success w-100">

                    Booking Sekarang

                    </a>

                </div>

            </div>

        </div>

    <?php } ?>

    </div>

</div>

<footer class="site-footer mt-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5>🎾 Padel Booking</h5>
                <p>
                    Lapangan padel premium dengan fasilitas modern,
                    nyaman, dan berkualitas. Buka setiap hari
                    pukul 08.00 - 00.00 WIB.
                </p>
                <div class="footer-social">
                    <a href="https://instagram.com" target="_blank" title="Instagram">📷</a>
                    <a href="https://wa.me/6281234567890" target="_blank" title="WhatsApp">💬</a>
                    <a href="https://facebook.com" target="_blank" title="Facebook">📘</a>
                </div>
            </div>

            <div class="col-md-4">
                <h5>Tautan Cepat</h5>
                <ul class="footer-links">
                    <li><a href="dashboard.php">Beranda</a></li>
                    <li><a href="riwayat.php">Riwayat Booking</a></li>
                    <li><a href="notifikasi.php">Notifikasi</a></li>
                    <li><a href="profile.php">Edit Profil</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h5>Hubungi Kami</h5>
                <ul class="footer-contact">
                    <li>📍 Jl. Malioboro No. 123, Yogyakarta</li>
                    <li>📞 (0274) 123-4567 / 0812-3456-7890</li>
                    <li>✉️ info@padelbooking.com</li>
                    <li>🕒 Setiap Hari, 08.00 - 00.00 WIB</li>
                </ul>
            </div>
        </div>

        <hr>

        <div class="footer-bottom">
            &copy; <?php echo date("Y"); ?> Padel Booking. All rights reserved.
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const cards = document.querySelectorAll('.card');

    cards.forEach(function(card, index) {

        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.5s ease';

        setTimeout(function() {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);

    });

});
</script>
<script src="../assets/script.js"></script>
</body>
</html>