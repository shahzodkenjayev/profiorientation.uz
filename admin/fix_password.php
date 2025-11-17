<?php
// Admin parolini to'g'rilash uchun script
// Bu faylni ishlatgandan keyin o'chirish kerak!

require_once '../config/config.php';

// Faqat localhost dan kirishga ruxsat
$allowed_hosts = ['localhost', '127.0.0.1'];
$current_host = $_SERVER['HTTP_HOST'] ?? '';
if (!in_array($current_host, $allowed_hosts) && strpos($current_host, 'localhost') === false) {
    die('Bu script faqat localhost da ishlaydi!');
}

$db = getDB();

// Yangi parol hash yaratish
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

echo "<h2>Admin parolini yangilash</h2>";

try {
    // Admin parolini yangilash
    $stmt = $db->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_password]);
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Admin paroli muvaffaqiyatli yangilandi!</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p><strong>Hash:</strong> " . $hashed_password . "</p>";
        echo "<br><p style='color: red;'><strong>⚠️ Eslatma: Bu faylni o'chirish kerak!</strong></p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Admin topilmadi. Yangi admin yaratilmoqda...</p>";
        
        // Yangi admin yaratish
        $stmt = $db->prepare("INSERT INTO admins (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $hashed_password, 'Bosh Admin', 'super_admin']);
        
        echo "<p style='color: green;'>✅ Yangi admin yaratildi!</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Xatolik: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

