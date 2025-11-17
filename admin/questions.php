<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();

$success = '';
$error = '';

// Savol qo'shish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $category = sanitize($_POST['category'] ?? '');
    $question_text = sanitize($_POST['question_text'] ?? '');
    $question_type = sanitize($_POST['question_type'] ?? 'multiple_choice');
    $order_number = intval($_POST['order_number'] ?? 0);
    
    if (empty($category) || empty($question_text)) {
        $error = 'Barcha maydonlarni to\'ldiring!';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO questions (category, question_text, question_type, order_number) 
                                 VALUES (?, ?, ?, ?)");
            $stmt->execute([$category, $question_text, $question_type, $order_number]);
            $question_id = $db->lastInsertId();
            
            // Javob variantlarini qo'shish
            if (isset($_POST['options']) && is_array($_POST['options'])) {
                foreach ($_POST['options'] as $index => $option_data) {
                    if (!empty($option_data['text'])) {
                        $stmt = $db->prepare("INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) 
                                             VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $question_id,
                            sanitize($option_data['text']),
                            intval($option_data['score'] ?? 0),
                            sanitize($option_data['tags'] ?? ''),
                            $index + 1
                        ]);
                    }
                }
            }
            
            $success = 'Savol muvaffaqiyatli qo\'shildi!';
        } catch (PDOException $e) {
            $error = 'Xatolik: ' . $e->getMessage();
        }
    }
}

// Savollarni olish
$stmt = $db->query("SELECT q.*, 
                    (SELECT COUNT(*) FROM answer_options WHERE question_id = q.id) as option_count
                    FROM questions q 
                    ORDER BY q.category, q.order_number");
$questions = $stmt->fetchAll();

// Kategoriyalar
$categories = ['Temperament', 'Qiziqishlar', 'Qobiliyatlar', 'Stressga chidamlilik', 'Muloqot', 'Analitik fikrlash', 'Ijodiy qobiliyatlar'];
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savollar - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Test Savollari</h1>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="admin-card">
                <h2>Yangi savol qo'shish</h2>
                <form method="POST" id="questionForm">
                    <input type="hidden" name="add_question" value="1">
                    
                    <div class="form-group">
                        <label>Kategoriya</label>
                        <select name="category" required>
                            <option value="">Tanlang</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Savol matni</label>
                        <textarea name="question_text" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Savol turi</label>
                        <select name="question_type">
                            <option value="multiple_choice">Ko'p tanlovli</option>
                            <option value="scale">Shkala</option>
                            <option value="yes_no">Ha/Yo'q</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tartib raqami</label>
                        <input type="number" name="order_number" value="0" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label>Javob variantlari</label>
                        <div id="options-container">
                            <div class="option-row">
                                <input type="text" name="options[0][text]" placeholder="Variant matni" required>
                                <input type="number" name="options[0][score]" placeholder="Ball" value="0" min="0" max="100" required>
                                <input type="text" name="options[0][tags]" placeholder="Kasb teglari (IT, Medical, ...)">
                            </div>
                        </div>
                        <button type="button" id="add-option" class="btn-secondary">+ Variant qo'shish</button>
                    </div>
                    
                    <button type="submit" class="btn-primary">Qo'shish</button>
                </form>
            </div>
            
            <div class="admin-card">
                <h2>Mavjud savollar (<?= count($questions) ?>)</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategoriya</th>
                            <th>Savol</th>
                            <th>Turi</th>
                            <th>Variantlar</th>
                            <th>Tartib</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $q): ?>
                            <tr>
                                <td><?= $q['id'] ?></td>
                                <td><?= htmlspecialchars($q['category']) ?></td>
                                <td><?= htmlspecialchars(mb_substr($q['question_text'], 0, 50)) ?>...</td>
                                <td><?= $q['question_type'] ?></td>
                                <td><?= $q['option_count'] ?></td>
                                <td><?= $q['order_number'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        let optionIndex = 1;
        document.getElementById('add-option').addEventListener('click', function() {
            const container = document.getElementById('options-container');
            const newRow = document.createElement('div');
            newRow.className = 'option-row';
            newRow.innerHTML = `
                <input type="text" name="options[${optionIndex}][text]" placeholder="Variant matni" required>
                <input type="number" name="options[${optionIndex}][score]" placeholder="Ball" value="0" min="0" max="100" required>
                <input type="text" name="options[${optionIndex}][tags]" placeholder="Kasb teglari">
                <button type="button" class="btn-danger btn-small remove-option">O'chirish</button>
            `;
            container.appendChild(newRow);
            optionIndex++;
            
            // Remove option handler
            newRow.querySelector('.remove-option').addEventListener('click', function() {
                newRow.remove();
            });
        });
        
        // Remove option handlers for existing rows
        document.querySelectorAll('.remove-option').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.option-row').remove();
            });
        });
    </script>
    
    <style>
        .option-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .option-row input {
            flex: 1;
        }
    </style>
</body>
</html>

