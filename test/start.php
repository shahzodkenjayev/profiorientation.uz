<?php
require_once '../config/config.php';
requireLogin();

require_once '../includes/payment.php';

$db = getDB();
$paymentHelper = new PaymentHelper();

// Foydalanuvchi ma'lumotlarini olish
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['test_completed']) {
    redirect(BASE_URL . 'results/view.php');
}

// To'lov tekshiruvi
$user_payments = $paymentHelper->getUserPayments($_SESSION['user_id']);
$has_paid = false;
foreach ($user_payments as $payment) {
    if ($payment['payment_status'] === 'completed') {
        $has_paid = true;
        break;
    }
}

if (!$has_paid) {
    redirect(BASE_URL . 'payment/index.php');
}

// Foydalanuvchi tilini aniqlash
$user_language = Language::current();

// Test savollarini olish (foydalanuvchi tiliga mos)
$stmt = $db->prepare("SELECT q.*, 
                    (SELECT COUNT(*) FROM answer_options WHERE question_id = q.id AND language = ?) as option_count
                    FROM questions q 
                    WHERE q.language = ?
                    ORDER BY q.category, q.order_number");
$stmt->execute([$user_language, $user_language]);
$questions = $stmt->fetchAll();

// Kategoriyalar bo'yicha guruhlash
$questions_by_category = [];
foreach ($questions as $q) {
    $questions_by_category[$q['category']][] = $q;
}

// Javob variantlarini olish (foydalanuvchi tiliga mos)
$stmt = $db->prepare("SELECT * FROM answer_options WHERE language = ? ORDER BY question_id, order_number");
$stmt->execute([$user_language]);
$all_options = $stmt->fetchAll();

$options_by_question = [];
foreach ($all_options as $opt) {
    $options_by_question[$opt['question_id']][] = $opt;
}
?>
<!DOCTYPE html>
<html lang="<?= $user_language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Kasb Tanlash</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/test.css">
</head>
<body>
    <div class="container">
        <div class="test-header">
            <h1>Kasb Tanlash Testi</h1>
            <p>Salom, <?= htmlspecialchars($user['full_name']) ?>!</p>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
            <p class="progress-text">Savol <span id="currentQuestion">1</span> / <span id="totalQuestions"><?= count($questions) ?></span></p>
        </div>
        
        <form id="testForm" method="POST" action="submit.php">
            <div class="test-container">
                <?php 
                $question_num = 1;
                foreach ($questions_by_category as $category => $category_questions): 
                ?>
                    <div class="category-section" data-category="<?= $category ?>">
                        <h2 class="category-title"><?= htmlspecialchars($category) ?></h2>
                        
                        <?php foreach ($category_questions as $question): ?>
                            <div class="question-block" data-question-id="<?= $question['id'] ?>" style="display: none;">
                                <div class="question-number">Savol <?= $question_num ?></div>
                                <div class="question-text"><?= htmlspecialchars($question['question_text']) ?></div>
                                
                                <div class="answer-options">
                                    <?php if (isset($options_by_question[$question['id']])): ?>
                                        <?php foreach ($options_by_question[$question['id']] as $option): ?>
                                            <label class="option-label">
                                                <input type="radio" 
                                                       name="question_<?= $question['id'] ?>" 
                                                       value="<?= $option['id'] ?>"
                                                       required>
                                                <span class="option-text"><?= htmlspecialchars($option['option_text']) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php $question_num++; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="test-navigation">
                <button type="button" id="prevBtn" class="btn-secondary" style="display:none;">← Oldingi</button>
                <button type="button" id="nextBtn" class="btn-primary">Keyingi →</button>
                <button type="submit" id="submitBtn" class="btn-success" style="display:none;">Testni yakunlash</button>
            </div>
        </form>
    </div>
    
    <script src="<?= ASSETS_PATH ?>js/test.js"></script>
</body>
</html>

