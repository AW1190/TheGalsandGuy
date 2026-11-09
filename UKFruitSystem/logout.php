<?php
/* logout.php - end the session and return to the login page */
define('AUTH_PUBLIC', true);
include 'includes/auth.php';
logout_user();
header('Location: index.php?error=out');
exit();
