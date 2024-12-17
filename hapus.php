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
$stmt = $conn->prepare("SELECT gambar FROM barang_hilang WHERE id = ? AND id_user = ?");
$stmt->bind_param("ii", $id_barang, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data barang hilang tidak ditemukan.";
    exit;
}

// Hapus gambar dari folder uploads (jika ada)
if ($data['gambar'] != '') {
    $file_path = "uploads/" . $data['gambar'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Hapus data dari database
$stmt = $conn->prepare("DELETE FROM barang_hilang WHERE id = ? AND id_user = ?");
$stmt->bind_param("ii", $id_barang, $_SESSION['user_id']);

if ($stmt->execute()) {
    header("Location: index.php");
    exit;
} else {
    echo "Error saat menghapus data: " . $stmt->error;
    exit;
}
?>
