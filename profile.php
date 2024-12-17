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

// Proses upload foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {
    $foto = $_FILES['foto_profil'];
    
    // Cek apakah file diunggah
    if ($foto['error'] === UPLOAD_ERR_OK) {
        $folder = 'uploads/';
        $filename = time() . '_' . basename($foto['name']);
        $target_file = $folder . $filename;

        // Validasi file (ukuran, tipe)
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            echo "File tidak didukung. Silakan pilih foto dengan format JPG, JPEG, PNG, atau GIF.";
            exit;
        }

        if ($foto['size'] > 2000000) { // Maksimal 2MB
            echo "Ukuran file terlalu besar. Maksimal 2MB.";
            exit;
        }

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($foto['tmp_name'], $target_file)) {
            // Simpan nama file foto ke database
            $query = "UPDATE users SET foto = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $filename, $user_id);
            if ($stmt->execute()) {
                echo "Foto profil berhasil diperbarui.";
                header("Location: profil.php"); // Redirect ke halaman profil setelah update
                exit;
            } else {
                echo "Gagal memperbarui foto profil.";
            }
        } else {
            echo "Terjadi kesalahan saat mengunggah foto.";
        }
    } else {
        echo "Terjadi kesalahan saat mengunggah foto.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Foto Profil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Update Foto Profil</h1>
        
        <form action="profil.php" method="post" enctype="multipart/form-data">
            <div class="profile text-center py-3">
                <img src="uploads/<?= $user['foto'] ?>" alt="Foto Profil" class="img-fluid rounded-circle" width="80">
                <h5 class="mt-2"><?= htmlspecialchars($user['nama']) ?></h5>
            </div>

            <div class="form-group">
                <label for="foto_profil">Pilih Foto Profil:</label>
                <input type="file" name="foto_profil" id="foto_profil" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Perbarui Foto</button>
        </form>
    </div>

</body>
</html>
