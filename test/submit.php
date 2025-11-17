<?php
require_once '../config/config.php';
requireLogin();

$db = getDB();

// Foydalanuvchi ma'lumotlarini olish
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['test_completed']) {
    redirect(BASE_URL . 'results/view.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Eski javoblarni o'chirish
        $stmt = $db->prepare("DELETE FROM user_answers WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Yangi javoblarni saqlash
        $scores = [];
        $profession_tags = [];
        
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'question_') === 0) {
                $question_id = str_replace('question_', '', $key);
                $answer_option_id = intval($value);
                
                // Javobni saqlash
                $stmt = $db->prepare("INSERT INTO user_answers (user_id, question_id, answer_option_id) 
                                     VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $question_id, $answer_option_id]);
                
                // Score va profession taglarni olish
                $stmt = $db->prepare("SELECT score, profession_tags FROM answer_options WHERE id = ?");
                $stmt->execute([$answer_option_id]);
                $option = $stmt->fetch();
                
                if ($option) {
                    $scores[] = $option['score'];
                    if ($option['profession_tags']) {
                        $tags = explode(',', $option['profession_tags']);
                        foreach ($tags as $tag) {
                            $tag = trim($tag);
                            if (!isset($profession_tags[$tag])) {
                                $profession_tags[$tag] = 0;
                            }
                            $profession_tags[$tag] += $option['score'];
                        }
                    }
                }
            }
        }
        
        // Natijalarni hisoblash
        $total_score = array_sum($scores);
        $avg_score = count($scores) > 0 ? $total_score / count($scores) : 0;
        
        // Eng yuqori profession tag
        arsort($profession_tags);
        $top_profession = key($profession_tags);
        
        // Personality type aniqlash
        $personality_type = 'Balanced';
        if ($avg_score >= 80) {
            $personality_type = 'Leader';
        } elseif ($avg_score >= 60) {
            $personality_type = 'Creative';
        } elseif ($avg_score >= 40) {
            $personality_type = 'Analytical';
        } else {
            $personality_type = 'Supportive';
        }
        
        // Profession tavsiyalari
        $profession_recommendations = [
            'IT' => 'Axborot texnologiyalari sohasida kuchli qobiliyatlar. Dasturlash, dizayn yoki tizimlar muhandisligi sizga mos.',
            'Medical' => 'Tibbiyot sohasida yaxshi natijalar. Shifokor, hamshira yoki farmatsevt bo\'lish mumkin.',
            'Engineering' => 'Muhandislik sohasida kuchli. Qurilish, mexanika yoki elektrotexnika sizga mos.',
            'Education' => 'Ta\'lim sohasida yaxshi natijalar. O\'qituvchi, pedagog yoki ta\'lim tizimlari mutaxassisi.',
            'Business' => 'Biznes va menejment sohasida kuchli. Menejer, tadbirkor yoki moliyaviy maslahatchi.',
            'Arts' => 'Ijodiy sohada yaxshi qobiliyatlar. Rassom, dizayner yoki san\'at mutaxassisi.',
        ];
        
        $recommendation = $profession_recommendations[$top_profession] ?? 'Sizning qobiliyatlaringiz turli sohalarda rivojlantirilishi mumkin.';
        
        // Strengths
        $strengths = [];
        if ($avg_score >= 70) {
            $strengths[] = 'Yuqori intellektual qobiliyatlar';
        }
        if (count($profession_tags) >= 3) {
            $strengths[] = 'Ko\'p qirrali qiziqishlar';
        }
        if ($personality_type === 'Leader') {
            $strengths[] = 'Liderlik qobiliyatlari';
        }
        if ($personality_type === 'Creative') {
            $strengths[] = 'Ijodiy qobiliyatlar';
        }
        
        // Natijani saqlash
        $stmt = $db->prepare("INSERT INTO test_results 
                             (user_id, profession_name, profession_description, match_percentage, 
                              personality_type, strengths, recommendations) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)
                             ON DUPLICATE KEY UPDATE
                             profession_name = VALUES(profession_name),
                             profession_description = VALUES(profession_description),
                             match_percentage = VALUES(match_percentage),
                             personality_type = VALUES(personality_type),
                             strengths = VALUES(strengths),
                             recommendations = VALUES(recommendations)");
        
        $match_percentage = min(100, ($avg_score / 100) * 100);
        $stmt->execute([
            $_SESSION['user_id'],
            $top_profession,
            $recommendation,
            $match_percentage,
            $personality_type,
            implode(', ', $strengths),
            $recommendation
        ]);
        
        // Testni yakunlangan deb belgilash
        $stmt = $db->prepare("UPDATE users SET test_completed = 1 WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        $db->commit();
        
        redirect(BASE_URL . 'results/view.php');
        
    } catch (PDOException $e) {
        $db->rollBack();
        die("Xatolik: " . $e->getMessage());
    }
} else {
    redirect(BASE_URL . 'test/start.php');
}
?>

