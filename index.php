<?php
session_start();

// Redirect ke login jika belum login, ke dashboard jika sudah
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
