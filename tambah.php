<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data pengguna
$user_id = $_SESSION['user_id'];

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = htmlspecialchars($_POST['nama_barang']);
    $tanggal_kehilangan = htmlspecialchars($_POST['tanggal_kehilangan']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    $lokasi_hilang = htmlspecialchars($_POST['lokasi_hilang']);
    $gambar = null;

    // Upload file gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES['gambar']['name']);
        $target_file = $target_dir . uniqid() . "_" . $file_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi jenis file
        if (in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = basename($target_file);
            } else {
                echo "<script>alert('Gagal mengunggah gambar.');</script>";
            }
        } else {
            echo "<script>alert('Format file tidak didukung. Hanya JPG, JPEG, PNG, dan GIF diperbolehkan.');</script>";
        }
    }

    // Simpan data ke database
    $query = "INSERT INTO barang_hilang (id_user, nama_barang, tanggal_kehilangan, deskripsi, lokasi_hilang, gambar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $user_id, $nama_barang, $tanggal_kehilangan, $deskripsi, $lokasi_hilang, $gambar);
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Hilang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Tambah Barang Hilang</h1>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" id="nama_barang" placeholder="Nama Barang" required>
        </div>
        <div class="form-group">
            <label for="tanggal_kehilangan">Tanggal Kehilangan</label>
            <input type="date" name="tanggal_kehilangan" class="form-control" id="tanggal_kehilangan" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi Barang</label>
            <textarea name="deskripsi" class="form-control" id="deskripsi" placeholder="Deskripsi Barang" required></textarea>
        </div>
        <div class="form-group">
            <label for="lokasi_hilang">Lokasi Kehilangan</label>
            <input type="text" name="lokasi_hilang" class="form-control" id="lokasi_hilang" placeholder="Lokasi Kehilangan" required>
        </div>
        <div class="form-group">
            <label for="gambar">Upload Gambar</label>
            <input type="file" name="gambar" class="form-control-file" id="gambar" accept="image/*">
        </div>
        <button type="submit" class="btn btn-success btn-block">Simpan</button>
    </form>
    
    <!-- Tombol Kembali ke Menu Utama berada di bawah -->
    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali ke Menu Utama
        </a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
