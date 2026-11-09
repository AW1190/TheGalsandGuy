<?php
/* login.php - checks the form from index.php, then sends the user to their section */
define('AUTH_PUBLIC', true);
include 'includes/auth.php';
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit(); }

$MAX_FAILS = 5;       // wrong attempts allowed ...
$LOCK_SECS = 300;     // ... before this session is locked for 5 minutes

function go_back($error, $next) {
    $url = 'index.php?error=' . $error;
    if (safe_next($next) !== '') { $url .= '&next=' . urlencode($next); }
    header('Location: ' . $url);
    exit();
}

$next     = isset($_POST['next']) ? $_POST['next'] : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (isset($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until']) {
    go_back('locked', $next);
}
if ($username === '' || $password === '') { go_back('1', $next); }

/* look the user up by name only; the password is checked in PHP, not in SQL */
$u   = mysql_real_escape_string($username);
$res = mysql_query("SELECT id, username, password, role FROM users WHERE username='$u' LIMIT 1");
if (!$res) { go_back('db', $next); }
$row = mysql_fetch_assoc($res);

$ok = false;
$upgrade = false;
if ($row) {
    $stored = $row['password'];
    if (preg_match('/^[a-f0-9]{32}$/i', $stored)) {
        /* legacy MD5 hash from the old sites: accept it, then upgrade it below */
        $ok = hash_equals(strtolower($stored), md5($password));
        $upgrade = $ok;
    } else {
        $ok = password_verify($password, $stored);
        if ($ok && password_needs_rehash($stored, PASSWORD_DEFAULT)) { $upgrade = true; }
    }
} else {
    /* unknown user: do the same amount of work so timing doesn't reveal it */
    password_verify($password, '$2y$10$RNwJT99cqTbEPqqVuFGg7O0.pvss5Nevrr1.yE1BiWp62td.4oJn6');
}

if (!$ok) {
    $_SESSION['login_fails'] = (isset($_SESSION['login_fails']) ? $_SESSION['login_fails'] : 0) + 1;
    if ($_SESSION['login_fails'] >= $MAX_FAILS) {
        $_SESSION['login_locked_until'] = time() + $LOCK_SECS;
        $_SESSION['login_fails'] = 0;
        go_back('locked', $next);
    }
    go_back('1', $next);
}

/* correct password, but the role has no section to go to */
$sections = role_sections();
if (!isset($sections[$row['role']])) { go_back('norole', $next); }

if ($upgrade) {   /* store a bcrypt hash in place of the MD5 (column is varchar(255)) */
    $new = password_hash($password, PASSWORD_DEFAULT);
    mysql_query("UPDATE users SET password='" . mysql_real_escape_string($new)
              . "' WHERE id=" . (int)$row['id']);
}

login_user($row);
unset($_SESSION['login_fails'], $_SESSION['login_locked_until']);

/* back to the page they asked for if their role may open it, else their own section */
$target = home_for_user();
$n = safe_next($next);
if ($n !== '') {
    $parts = explode('/', $n);
    if (can_access_section($parts[0])) { $target = $n; }
}
header('Location: ' . $target);
exit();
