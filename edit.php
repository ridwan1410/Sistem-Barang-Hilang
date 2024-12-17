<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Periksa apakah ID barang hilang diberikan melalui URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID barang hilang tidak valid.";
    exit;
}

$id_barang = $_GET['id'];

// Ambil data barang hilang berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM barang_hilang WHERE id = ? AND id_user = ?");
$stmt->bind_param("ii", $id_barang, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data barang hilang tidak ditemukan.";
    exit;
}

// Tangani form submit untuk update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_barang = $_POST['nama_barang'];
    $tanggal_kehilangan = $_POST['tanggal_kehilangan'];
    $deskripsi = $_POST['deskripsi'];
    $lokasi_hilang = $_POST['lokasi_hilang'];

    // Variabel untuk menyimpan nama file gambar
    $gambar = $data['gambar'];  // Jika gambar tidak diubah, tetap gunakan gambar yang lama

    // Proses upload file jika ada
    if (!empty($_FILES['gambar']['name'])) {
        // Buat folder uploads jika belum ada
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Ambil nama file asli dan buat nama unik
        $gambar = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = "uploads/$gambar";

        // Validasi ukuran dan jenis file
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($_FILES['gambar']['size'] > 2000000) { // Maksimal 2 MB
            echo "Ukuran file terlalu besar (maksimal 2MB).";
            exit;
        }
        if (!in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo "Jenis file tidak didukung (hanya JPG, JPEG, PNG, atau GIF).";
            exit;
        }

        // Pindahkan file yang diupload
        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            echo "Terjadi kesalahan saat mengupload file.";
            exit;
        }
    }

    // Update data ke database
    $stmt = $conn->prepare("UPDATE barang_hilang SET nama_barang = ?, tanggal_kehilangan = ?, deskripsi = ?, lokasi_hilang = ?, gambar = ? WHERE id = ? AND id_user = ?");
    $stmt->bind_param("sssssii", $nama_barang, $tanggal_kehilangan, $deskripsi, $lokasi_hilang, $gambar, $id_barang, $_SESSION['user_id']);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error saat memperbarui data: " . $stmt->error;
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang Hilang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Edit Barang Hilang</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" id="nama_barang" value="<?= htmlspecialchars($data['nama_barang']) ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal_kehilangan">Tanggal Kehilangan</label>
                <input type="date" name="tanggal_kehilangan" class="form-control" id="tanggal_kehilangan" value="<?= $data['tanggal_kehilangan'] ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi Barang</label>
                <textarea name="deskripsi" class="form-control" id="deskripsi" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="lokasi_hilang">Lokasi Kehilangan</label>
                <input type="text" name="lokasi_hilang" class="form-control" id="lokasi_hilang" value="<?= htmlspecialchars($data['lokasi_hilang']) ?>" required>
            </div>
            <div class="form-group">
                <label for="gambar">Upload Gambar</label>
                <input type="file" name="gambar" class="form-control-file" id="gambar" accept="image/*">
            </div>
            <button type="submit" class="btn btn-warning btn-block">Update</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
