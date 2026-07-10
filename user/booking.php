<?php
/**
 * booking.php
 * -----------------------------------------------------------------------
 * Modul  : Booking Lapangan (Sisi User)
 * Dikerjakan oleh : Anggota 2 (Booking & Interaktivitas JavaScript)
 *
 * Fitur:
 * - Form booking lapangan: pilih tanggal, pilih 1 atau beberapa jam
 *   sekaligus (multi-select), metode pembayaran, dan upload bukti bayar
 * - Grid jam dibangun & dikendalikan lewat DOM/JavaScript (jam-slot.js
 *   inline di bawah), termasuk menonaktifkan jam yang sudah lewat / penuh
 * - Validasi ulang di server sebelum INSERT (Create), mencegah bentrok
 *   jadwal / double booking
 *
 * Catatan perbaikan saat refactor:
 * Pada kode awal, jam yang dipilih (bisa lebih dari satu / array JSON)
 * tidak pernah benar-benar disimpan satu per satu ke tabel booking
 * (variabel jam tunggal tidak sinkron dengan array jam yang dipilih user).
 * Pada versi ini setiap jam yang dipilih disimpan sebagai satu baris
 * booking tersendiri, dan pengecekan bentrok jadwal dilakukan untuk
 * seluruh jam yang dipilih.
 * -----------------------------------------------------------------------
 */

include "auth.php";
include "config/koneksi.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$lapangan = mysqli_query($conn, "SELECT * FROM lapangan WHERE id = '$id'");
$l = mysqli_fetch_assoc($lapangan);

if (!$l) {
    header("Location: dashboard.php");
    exit;
}

// Jam operasional lapangan: 08.00 - 00.00 (16 slot per jam)
$jamBuka  = 8;
$jamTutup = 24; // 24 = 00:00 keesokan hari
$slotJam  = [];

for ($j = $jamBuka; $j < $jamTutup; $j++) {
    $mulai   = str_pad($j % 24, 2, "0", STR_PAD_LEFT) . ":00";
    $selesai = str_pad(($j + 1) % 24, 2, "0", STR_PAD_LEFT) . ":00";
    $slotJam[] = [
        "value" => $mulai,
        "label" => $mulai . " - " . $selesai
    ];
}

$errorMsg = "";

if (isset($_POST['booking'])) {

    $user     = $_SESSION['id'];
    $tanggal  = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jamArray = json_decode($_POST['jam'], true);
    $metode   = mysqli_real_escape_string($conn, $_POST['metode']);

    $today = date("Y-m-d");

    if (empty($_POST['tanggal']) || empty($_POST['jam']) || empty($_POST['metode'])) {
        $errorMsg = "Semua data wajib diisi.";
    } elseif ($tanggal < $today) {
        $errorMsg = "Tanggal booking tidak boleh sebelum hari ini.";
    } elseif (empty($jamArray)) {
        $errorMsg = "Pilih minimal 1 jam.";
    } elseif (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = "Bukti pembayaran wajib diunggah.";
    } else {

        // Cek ulang ketersediaan SEMUA jam yang dipilih (mencegah bentrok / double booking)
        $jamBentrok = [];

        foreach ($jamArray as $jamPilih) {
            $jamPilihSafe = mysqli_real_escape_string($conn, $jamPilih);

            $cek = mysqli_query($conn, "
                SELECT * FROM booking
                WHERE lapangan_id = '$id'
                AND tanggal = '$tanggal'
                AND jam = '$jamPilihSafe'
                AND status != 'Ditolak'
            ");

            if (mysqli_num_rows($cek) > 0) {
                $jamBentrok[] = $jamPilih;
            }
        }

        if (!empty($jamBentrok)) {
            $errorMsg = "Mohon maaf, jam " . implode(", ", $jamBentrok) .
                        " baru saja dibooking orang lain. Silakan pilih jam lain.";
        } else {

            $ext         = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
            $namaFile    = "bukti_" . $id . "_" . time() . "_" . uniqid() . "." . $ext;
            $folderBukti = "assets/bukti/";

            if (!is_dir($folderBukti)) {
                mkdir($folderBukti, 0755, true);
            }

            $uploadOk = move_uploaded_file($_FILES['bukti']['tmp_name'], $folderBukti . $namaFile);

            if (!$uploadOk) {
                $errorMsg = "Gagal mengunggah bukti pembayaran. Silakan coba lagi.";
            } else {

                // Simpan satu baris booking untuk setiap jam yang dipilih
                $semuaBerhasil = true;

                foreach ($jamArray as $jamPilih) {
                    $jamPilihSafe = mysqli_real_escape_string($conn, $jamPilih);

                    $simpan = mysqli_query($conn, "
                        INSERT INTO booking
                            (user_id, lapangan_id, tanggal, jam, metode_pembayaran, bukti_pembayaran, status, notifikasi)
                        VALUES
                            ('$user', '$id', '$tanggal', '$jamPilihSafe', '$metode', '$namaFile', 'Pending', 'Belum Dibaca')
                    ");

                    if (!$simpan) {
                        $semuaBerhasil = false;
                    }
                }

                if ($semuaBerhasil) {
                    // Pola PRG (Post/Redirect/Get) supaya tidak ada layar putih
                    // dan pesan sukses selalu tampil dengan baik.
                    header("Location: riwayat.php?success=1");
                    exit;
                } else {
                    $errorMsg = "Terjadi kesalahan sistem saat menyimpan booking. Silakan coba lagi.";
                }
            }
        }
    }
}

// Ambil daftar jam yang sudah terisi untuk hari ini (default tampilan awal)
$tanggalDefault = date("Y-m-d");
$jamTerisi = [];
$cekJam = mysqli_query($conn, "
    SELECT jam FROM booking
    WHERE lapangan_id = '$id'
    AND tanggal = '$tanggalDefault'
    AND status != 'Ditolak'
");
while ($row = mysqli_fetch_assoc($cekJam)) {
    $jamTerisi[] = $row['jam'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking - <?php echo htmlspecialchars($l['nama_lapangan']); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="hero-padel">
        <h1>🎾 Booking Lapangan Padel</h1>
        <p>
            Pilih jadwal bermain favoritmu dan nikmati pengalaman bermain
            padel premium. Buka setiap hari 08.00 - 00.00 WIB.
        </p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8 col-md-9">
<div class="card booking-card mt-2 mb-5">

<div class="card-header booking-header">
    <h2>🎾 Booking Lapangan</h2>
</div>

<div class="card-body">

    <h3 class="mb-3 text-success fw-bold"><?php echo htmlspecialchars($l['nama_lapangan']); ?></h3>

    <div class="booking-info">
        <h4>🎾 Premium Padel Court</h4>
        <p>
            Pilih tanggal bermain, jam tersedia, dan metode pembayaran.
            Jam yang sudah dibooking otomatis tidak dapat dipilih.
        </p>
    </div>

    <p class="price-tag">Rp <?php echo number_format($l['harga']); ?>/Jam</p>

    <?php if (!empty($errorMsg)) { ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data" id="formBooking">

        <div class="mb-3">
            <label class="form-label">Tanggal Booking</label>
            <input type="date" name="tanggal" id="tgl" class="form-control"
                   min="<?php echo date('Y-m-d'); ?>"
                   value="<?php echo isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : date('Y-m-d'); ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">
                Pilih Jam Bermain <span class="text-muted fw-normal">(08.00 - 00.00)</span>
            </label>

            <div id="jamGrid" class="jam-grid"></div>
            <input type="hidden" name="jam" id="jam" required>

            <div id="jamHelp" class="form-text mt-2">
                Silakan pilih tanggal untuk melihat jam tersedia.
            </div>

            <div id="infoBooking" class="alert alert-success mt-2" style="display:none;">
                <strong>Durasi:</strong> <span id="jumlahJam">0</span> Jam
                <br>
                <strong>Total:</strong> Rp <span id="totalHarga">0</span>
            </div>

            <div class="mb-3">
                <label class="form-label">Metode Pembayaran</label>
                <select name="metode" id="metode" class="form-control" required>
                    <option value="">-- Pilih Metode --</option>
                    <option value="DANA">DANA</option>
                    <option value="OVO">OVO</option>
                    <option value="GoPay">GoPay</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                </select>

                <div id="infoPembayaran" class="booking-info mt-3" style="display:none;"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload Bukti Pembayaran</label>
                <input type="file" name="bukti" class="form-control" accept="image/*,.pdf" required>
            </div>

            <button type="submit" class="booking-btn" name="booking">🎾 Booking Sekarang</button>

        </div>
    </form>

</div>
</div>
</div>
</div>
</div>

<script>
const LAPANGAN_ID = <?php echo (int) $id; ?>;

const SLOT_JAM = <?php echo json_encode($slotJam); ?>;
let jamTerisi  = <?php echo json_encode($jamTerisi); ?>;

const jamGrid  = document.getElementById("jamGrid");
const jamInput = document.getElementById("jam");
const jamHelp  = document.getElementById("jamHelp");
const tglInput = document.getElementById("tgl");

let selectedJam = [];

function renderJamGrid() {
    jamGrid.innerHTML = "";
    const now       = new Date();
    const todayStr  = now.toISOString().slice(0, 10);
    const isToday   = tglInput.value === todayStr;
    const currentHour = now.getHours();

    SLOT_JAM.forEach(function (slot) {
        const div = document.createElement("div");
        div.className = "jam-slot";
        div.textContent = slot.label;

        const jamSudahLewat = isToday && parseInt(slot.value.split(":")[0]) <= currentHour;
        const isFull = jamTerisi.includes(slot.value);

        if (isFull || jamSudahLewat) {
            div.classList.add("disabled");
            const info = document.createElement("small");
            info.textContent = isFull ? "Terisi" : "Terlewat";
            div.appendChild(info);
        } else {

            div.addEventListener("click", function () {

                if (div.classList.contains("selected")) {
                    div.classList.remove("selected");
                    selectedJam = selectedJam.filter(item => item !== slot.value);
                } else {
                    div.classList.add("selected");
                    selectedJam.push(slot.value);
                }

                jamInput.value = JSON.stringify(selectedJam);

                const jumlahJam   = selectedJam.length;
                const hargaPerJam = <?php echo (int) $l['harga']; ?>;
                const total       = jumlahJam * hargaPerJam;

                document.getElementById("jumlahJam").textContent = jumlahJam;
                document.getElementById("totalHarga").textContent = total.toLocaleString('id-ID');
                document.getElementById("infoBooking").style.display = jumlahJam > 0 ? "block" : "none";

                jamHelp.textContent = "Dipilih " + jumlahJam + " jam";
            });
        }

        jamGrid.appendChild(div);
    });
}

function muatJamTerisi() {
    jamHelp.textContent = "Memuat jam tersedia...";
    jamInput.value = "";

    fetch("get_jam.php?lapangan_id=" + LAPANGAN_ID + "&tanggal=" + tglInput.value)
        .then(function (res) { return res.json(); })
        .then(function (data) {
            jamTerisi = data.terisi || [];
            renderJamGrid();
            jamHelp.textContent = "Klik salah satu jam yang tersedia di atas.";
        })
        .catch(function () {
            renderJamGrid();
            jamHelp.textContent = "Klik salah satu jam yang tersedia di atas.";
        });
}

tglInput.addEventListener("change", muatJamTerisi);
muatJamTerisi();

document.getElementById("formBooking").addEventListener("submit", function (e) {
    if (!jamInput.value) {
        e.preventDefault();
        alert("Silakan pilih jam bermain terlebih dahulu.");
    }
});

const metode = document.getElementById("metode");
const info   = document.getElementById("infoPembayaran");

metode.addEventListener("change", function () {
    const data = {
        "DANA": ["DANA", "081234567890"],
        "OVO": ["OVO", "081234567890"],
        "GoPay": ["GoPay", "081234567890"],
        "Transfer Bank": ["Transfer Bank", "BCA 1234567890"]
    };

    if (data[this.value]) {
        info.style.display = "block";
        info.innerHTML = "<h5>" + data[this.value][0] + "</h5><p>Nomor : " + data[this.value][1] + "<br>A/N : Padel Premium</p>";
    } else {
        info.style.display = "none";
    }
});
</script>

<script src="assets/script.js"></script>
</body>
</html>
