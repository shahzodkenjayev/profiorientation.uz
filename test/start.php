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
// Agar foydalanuvchi tilida savollar bo'lmasa, o'zbek tilida olish
$stmt = $db->prepare("SELECT q.*, 
                    (SELECT COUNT(*) FROM answer_options WHERE question_id = q.id AND language = q.language) as option_count
                    FROM questions q 
                    WHERE q.language = ? OR (q.language = 'uz' AND NOT EXISTS (SELECT 1 FROM questions WHERE language = ?))
                    ORDER BY q.category, q.order_number
                    LIMIT 50");
$stmt->execute([$user_language, $user_language]);
$questions = $stmt->fetchAll();

// Agar savollar topilmasa, xatolik ko'rsatish
if (empty($questions)) {
    die("Xatolik: Test savollari topilmadi! Iltimos, admin panel orqali savollar qo'shing.");
}

// Kategoriyalar bo'yicha guruhlash
$questions_by_category = [];
foreach ($questions as $q) {
    $questions_by_category[$q['category']][] = $q;
}

// Javob variantlarini olish (foydalanuvchi tiliga mos yoki o'zbek tilida)
$question_ids = array_column($questions, 'id');
if (!empty($question_ids)) {
    $placeholders = str_repeat('?,', count($question_ids) - 1) . '?';
    $stmt = $db->prepare("SELECT * FROM answer_options 
                         WHERE question_id IN ($placeholders) 
                         AND (language = ? OR (language = 'uz' AND NOT EXISTS (
                             SELECT 1 FROM answer_options ao2 
                             WHERE ao2.question_id = answer_options.question_id 
                             AND ao2.language = ?
                         )))
                         ORDER BY question_id, order_number");
    $params = array_merge($question_ids, [$user_language, $user_language]);
    $stmt->execute($params);
    $all_options = $stmt->fetchAll();
} else {
    $all_options = [];
}

$options_by_question = [];
foreach ($all_options as $opt) {
    $options_by_question[$opt['question_id']][] = $opt;
}

// Debug: Savollar va javob variantlarini tekshirish
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_log("Test start: Found " . count($questions) . " questions");
    error_log("Test start: Found " . count($all_options) . " answer options");
    foreach ($options_by_question as $q_id => $opts) {
        error_log("Question ID $q_id has " . count($opts) . " options");
    }
    if (empty($all_options)) {
        error_log("WARNING: No answer options found! Check database.");
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $user_language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('Test') ?> - <?= __('Prof Orientatsiya') ?></title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/test.css">
</head>
<body>
    <div class="container">
        <div class="test-header">
            <h1><?= __('test.title') ?></h1>
            <p><?= __('Salom') ?>, <?= htmlspecialchars($user['full_name']) ?>!</p>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
            <p class="progress-text"><?= __('test.question') ?> <span id="currentQuestion">1</span> / <span id="totalQuestions"><?= count($questions) ?></span></p>
        </div>
        
        <form id="testForm" method="POST" action="submit.php">
            <div class="test-container">
                <?php 
                $question_num = 1;
                $total_questions = count($questions);
                foreach ($questions_by_category as $category => $category_questions): 
                ?>
                    <div class="category-section" data-category="<?= htmlspecialchars($category) ?>">
                        <?php foreach ($category_questions as $question): ?>
                            <div class="question-block" data-question-id="<?= $question['id'] ?>" data-question-num="<?= $question_num ?>" style="display: <?= $question_num === 1 ? 'block' : 'none' ?>;">
                                <div class="question-number"><?= __('test.question') ?> <?= $question_num ?> / <?= $total_questions ?></div>
                                <div class="question-text"><?= htmlspecialchars($question['question_text']) ?></div>
                                
                                <div class="answer-options">
                                    <?php if (isset($options_by_question[$question['id']]) && !empty($options_by_question[$question['id']])): ?>
                                        <?php foreach ($options_by_question[$question['id']] as $option): ?>
                                            <label class="option-label">
                                                <input type="radio" 
                                                       name="question_<?= $question['id'] ?>" 
                                                       value="<?= $option['id'] ?>"
                                                       required>
                                                <span class="option-text"><?= htmlspecialchars($option['option_text']) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="no-options" style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; color: #856404; margin: 10px 0;">
                                            <strong>⚠️ Eslatma:</strong> Bu savol uchun javob variantlari topilmadi. Iltimos, admin panel orqali javob variantlarini qo'shing.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php $question_num++; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="test-navigation">
                <button type="button" id="prevBtn" class="btn-secondary" style="display:none;">← <?= __('test.prev') ?></button>
                <button type="button" id="nextBtn" class="btn-primary"><?= __('test.next') ?> →</button>
                <button type="submit" id="submitBtn" class="btn-success" style="display:none;"><?= __('test.submit') ?></button>
            </div>
        </form>
    </div>
    
    <script src="<?= ASSETS_PATH ?>js/test.js"></script>
</body>
</html>

