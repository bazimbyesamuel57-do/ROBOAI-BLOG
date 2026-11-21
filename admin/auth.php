<?php
//start a session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//controll cache
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// the admin id
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
