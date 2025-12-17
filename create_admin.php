<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Check if admin exists
$stmt = $conn->prepare("SELECT * FROM admin WHERE username = 'admin'");
$stmt->execute();
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    echo "Admin user already exists!<br>";
    echo "Username: " . htmlspecialchars($existing['username']) . "<br>";
    echo "Password hash exists: " . (!empty($existing['password']) ? 'YES' : 'NO') . "<br>";
    echo "Hash length: " . strlen($existing['password']) . "<br><br>";
    
    echo "Updating password to 'admin123'...<br>";
    $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE admin SET password = :pass WHERE username = 'admin'");
    $updateStmt->bindParam(':pass', $newPassword);
    
    if ($updateStmt->execute()) {
        echo "<strong style='color: green;'>✓ Password updated successfully!</strong><br>";
    } else {
        echo "<strong style='color: red;'>✗ Failed to update password</strong><br>";
    }
} else {
    echo "Creating new admin user...<br>";
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $insertStmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (:user, :pass)");
    $insertStmt->bindParam(':user', $username);
    $insertStmt->bindParam(':pass', $password);
    
    if ($insertStmt->execute()) {
        echo "<strong style='color: green;'>✓ Admin user created successfully!</strong><br>";
    } else {
        echo "<strong style='color: red;'>✗ Failed to create admin user</strong><br>";
    }
}

echo "<br><strong>Login Credentials:</strong><br>";
echo "Username: <code>admin</code><br>";
echo "Password: <code>admin123</code><br><br>";
echo "<a href='admin/login.php'>Go to Login Page →</a>";
?>
