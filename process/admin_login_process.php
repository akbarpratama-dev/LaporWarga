<?php
session_start();
require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/login.php');
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if($username === '' || $password === '') {
    header('Location: ../admin/login.php?error=invalid');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare('SELECT id, username, password FROM admin WHERE username = :u LIMIT 1');
    $stmt->bindParam(':u', $username);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../admin/login.php?error=invalid');
    }
} catch(PDOException $e) {
    error_log('Admin login error: ' . $e->getMessage());
    header('Location: ../admin/login.php?error=invalid');
}
exit();
?>