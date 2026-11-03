<?php
/**
 * Admin Logout Page - Survey IPT System
 */
require_once __DIR__ . '/config.php';

unset($_SESSION['admin_user']);
session_destroy();

header('Location: login.php');
exit;
?>
