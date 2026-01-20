<?php
// assets/php/auth_check.php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../admin/login.html");
    exit;
}
