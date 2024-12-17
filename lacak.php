<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data pengguna yang sedang login
$user_id = $_SESSION['user_id'];
$query_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Proses pencarian barang
$search_query = '';
$barang_results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    
    $query = "SELECT * FROM barang_hilang WHERE nama_barang LIKE ? OR lokasi_hilang LIKE ?";
    $stmt = $conn->prepare($query);
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $barang_results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Barang Hilang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-gradient-primary text-white p-4 overflow-auto" id="sidebar" style="min-width: 250px; white-space: nowrap; overflow-x: auto;">
    <div class="text-center mb-4">
        <img src="Logouin.png" alt="Logo UIN Suska Riau" class="img-fluid rounded-circle" width="100">
        <h5 class="mt-3">Dashboard</h5>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item mb-3">
            <a href="index.php" class="nav-link text-white fw-bold <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-home me-2"></i> Menu Utama
            </a>
        </li>
        <li class="nav-item mb-3">
            <a href="tambah.php" class="nav-link text-white fw-bold <?= basename($_SERVER['PHP_SELF']) == 'tambah.php' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle me-2"></i> Tambah Barang
            </a>
        </li>
        <li class="nav-item mb-3">
            <a href="lacak.php" class="nav-link text-white fw-bold <?= basename($_SERVER['PHP_SELF']) == 'lacak.php' ? 'active' : '' ?>">
                <i class="fas fa-search me-2"></i> Lacak Barang
            </a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link text-white fw-bold">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>


    <!-- Content Area -->
    <div class="flex-grow-1 p-4" style="background-color: #f4f5f7;">
        <h1 class="text-center text-primary mb-5">Lacak Barang Hilang</h1>

        <!-- Search Form -->
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <form method="POST" action="" class="d-flex">
                    <input type="text" name="search_query" class="form-control me-2" placeholder="Cari Barang atau Lokasi" value="<?= htmlspecialchars($search_query) ?>" required>
                    <button type="submit" name="search" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                </form>
            </div>
        </div>

        <!-- Daftar Barang Hilang -->
        <?php if (!empty($barang_results)) { ?>
            <div class="row">
                <?php foreach ($barang_results as $row) { ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <img src="uploads/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama_barang']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?= htmlspecialchars($row['nama_barang']) ?></h5>
                                <p class="card-text"><strong>Tanggal Kehilangan:</strong> <?= date('d-m-Y', strtotime($row['tanggal_kehilangan'])) ?></p>
                                <p class="card-text"><strong>Lokasi Kehilangan:</strong> <?= htmlspecialchars($row['lokasi_hilang']) ?></p>
                                <p class="card-text text-muted">"<?= htmlspecialchars($row['deskripsi']) ?>"</p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p class="text-center text-muted">Tidak ada barang yang ditemukan dengan pencarian Anda.</p>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        overflow-x: hidden;
        background-color: #e9ecef;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    #sidebar {
        height: 100vh;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
    }
    #sidebar ul {
        padding: 0;
        list-style: none;
    }
    #sidebar a {
        display: block;
        padding: 10px;
        text-decoration: none;
    }
    #sidebar a:hover {
        background-color: #495057;
    }
    .card {
        border-radius: 8px;
    }
    .card img {
        border-radius: 8px 8px 0 0;
    }
</style>

</body>
</html>
