<?php
// Admin papkasiga kirganda login.php ga redirect qilish
require_once '../config/config.php';

if (isAdmin()) {
    redirect(BASE_URL . 'admin/dashboard.php');
} else {
    redirect(BASE_URL . 'admin/login.php');
}

