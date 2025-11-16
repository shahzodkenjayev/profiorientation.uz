<?php
require_once '../config/config.php';

session_destroy();
redirect(BASE_URL . 'admin/login.php');
?>

