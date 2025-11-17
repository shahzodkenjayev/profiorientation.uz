<?php
// PHP fayllarining oxiridagi bo'sh qatorlarni tozalash uchun helper

if (!function_exists('clean_output')) {
    function clean_output($buffer) {
        // PHP closing tag'dan keyin keladigan barcha matnlarni olib tashlash
        $buffer = preg_replace('/\?>\s*$/m', '', $buffer);
        // Oxiridagi bo'sh qatorlarni olib tashlash
        $buffer = rtrim($buffer);
        return $buffer;
    }
}

// Output buffering'ni yoqish (agar yoqilmagan bo'lsa)
if (!ob_get_level()) {
    ob_start('clean_output');
}

