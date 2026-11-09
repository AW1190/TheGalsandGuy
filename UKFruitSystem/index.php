<?php
/* index.php - the one login page for office and warehouse */
define('AUTH_PUBLIC', true);
include 'includes/auth.php';

if (is_logged_in()) { header('Location: ' . home_for_user()); exit(); }

$messages = array(
    '1'       => 'Invalid username or password.',
    'locked'  => 'Too many failed attempts. Please wait a few minutes and try again.',
    'db'      => 'The database is unavailable. Please try again later.',
    'norole'  => 'Your account is not assigned to a section. Please ask an administrator.',
    'login'   => 'Please sign in to continue.',
    'timeout' => 'You were signed out after 30 minutes of inactivity.',
    'out'     => 'You have been signed out.'
);
$code = isset($_GET['error']) ? $_GET['error'] : '';
$msg  = isset($messages[$code]) ? $messages[$code] : '';
$next = safe_next(isset($_GET['next']) ? $_GET['next'] : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sign in - UK Fruit Management System</title>
    <link rel="stylesheet" href="office/assets/style.css">
</head>
<body class="login-page">

<div class="login-card">
    <div align="center">
        <h1>UK Fruit Management System</h1>
        <p>Sign in to continue to the office or warehouse system.</p>
    </div>
    <p><img src="office/assets/UKFruit2010.png" width="400" alt="Customer Logo" class="logo"></p>

    <?php if ($msg !== ''): ?>
        <p class="notice"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form action="login.php" method="post" autocomplete="off">
        <input type="hidden" name="next" value="<?php echo htmlspecialchars($next); ?>">
        <label>Username
            <input type="text" name="username" required autofocus>
        </label>

        <label>Password
            <input type="password" name="password" required>
        </label>

        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>
