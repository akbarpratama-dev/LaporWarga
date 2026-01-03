<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin/login.php?error=unauthorized");
    exit();
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    
    $kategori = $_POST['kategori'];
    $judul = $_POST['judul'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : null;
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO info_warga (kategori, judul, lokasi, deskripsi, tanggal_mulai, tanggal_selesai) 
            VALUES (:kategori, :judul, :lokasi, :deskripsi, :tanggal_mulai, :tanggal_selesai)
        ");
        
        $stmt->bindParam(':kategori', $kategori);
        $stmt->bindParam(':judul', $judul);
        $stmt->bindParam(':lokasi', $lokasi);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':tanggal_mulai', $tanggal_mulai);
        $stmt->bindParam(':tanggal_selesai', $tanggal_selesai);
        
        $stmt->execute();
        
        header("Location: /admin/info_warga.php?success=1");
        exit();
    } catch (PDOException $e) {
        error_log("Error upload info: " . $e->getMessage());
        header("Location: /admin/info_warga.php?error=database");
        exit();
    }
} else {
    header("Location: /admin/info_warga.php");
    exit();
}