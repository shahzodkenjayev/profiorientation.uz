<?php
// Admin parolini qayta tiklash uchun script
// Bu faylni ishlatgandan keyin o'chirish kerak!

require_once '../config/config.php';

// Faqat localhost dan kirishga ruxsat
if ($_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== '127.0.0.1') {
    die('Bu script faqat localhost da ishlaydi!');
}

$db = getDB();

// Yangi parol hash yaratish
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Admin parolini yangilash
    $stmt = $db->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_password]);
    
    echo "✅ Admin paroli muvaffaqiyatli yangilandi!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br><strong>⚠️ Eslatma: Bu faylni o'chirish kerak!</strong>";
    
} catch (PDOException $e) {
    echo "❌ Xatolik: " . $e->getMessage();
}
?>

