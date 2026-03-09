<?php
session_start();
session_destroy();
// Usuń ciastko jeśli istnieje
if (isset($_COOKIE['admin_remember_token'])) {
    setcookie('admin_remember_token', '', time() - 3600, '/');
}
header('Location: login.php');
exit;