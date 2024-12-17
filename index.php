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
$query_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Query untuk menampilkan daftar barang hilang
$query = "SELECT * FROM barang_hilang WHERE id_user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Hilang - UIN Suska Riau</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-gradient-primary text-white p-4 overflow-auto" id="sidebar" style="min-width: 250px; white-space: nowrap; overflow-x: auto;">
        <div class="text-center mb-4">
            <img src="Logouin.png" alt="Logo UIN Suska Riau" class="img-fluid rounded-circle" width="100">
            <h5 class="mt-3">Dashboard</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a href="tambah.php" class="nav-link text-white fw-bold"><i class="fas fa-plus-circle me-2"></i> Tambah Barang</a>
            </li>
            <li class="nav-item mb-3">
                <a href="lacak.php" class="nav-link text-white fw-bold"><i class="fas fa-search me-2"></i> Lacak Barang</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link text-white fw-bold"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4" id="content" style="background-color: #f4f5f7;">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 rounded">
    <div class="container-fluid d-flex align-items-center">
        <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>
        <a class="navbar-brand me-3 fw-bold text-primary" href="#">Sistem Pelaporan Barang Hilang Mahasiswa Fakultas Sains dan Teknologi UIN SUSKA RIAU</a>
    </div>
</nav>


         <!-- Ucapan Selamat Datang -->
         <div class="alert alert-info mb-4" role="alert">
            <h4 class="alert-heading">Selamat Datang</h4>
            <p>Anda dapat mengelola barang hilang yang Anda laporkan di sini. Semoga sistem ini memudahkan Anda untuk menemukan barang Anda yang hilang.</p>
        </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle shadow-sm rounded">
                    <thead class="table-primary">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Tanggal Kehilangan</th>
                            <th>Deskripsi Barang</th>
                            <th>Lokasi Kehilangan</th>
                            <th>Foto Barang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_kehilangan']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td><?= htmlspecialchars($row['lokasi_hilang']) ?></td>
                            <td>
                                <?php if ($row['gambar']): ?>
                                    <img src="uploads/<?= $row['gambar'] ?>" alt="Gambar Barang" class="img-thumbnail" style="max-width: 80px;">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-1"><i class="fas fa-edit"></i></a>
                                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
        document.getElementById("sidebar").classList.toggle("d-none");
        document.getElementById("content").classList.toggle("w-100");
    });
</script>

<style>
    body {
        overflow-x: hidden;
        background-color: #e9ecef;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    #wrapper {
        display: flex;
    }
    #sidebar {
        height: 100vh;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        overflow-x: auto;
        white-space: nowrap;
    }
    #sidebar a:hover {
        background-color: #495057;
    }
    #sidebar ul {
        padding: 0;
    }
    #content {
        transition: margin-left 0.3s ease;
    }
    #content.w-100 {
        margin-left: 0;
    }
    .navbar {
        border-radius: 8px;
    }
    .table {
        background-color: #fff;
    }
    .table th, .table td {
        text-align: center;
    }
    table img {
        max-height: 60px;
    }
</style>

</body>
</html>
