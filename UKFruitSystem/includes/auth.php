<?php
/* includes/auth.php - login sessions and section access for UKFruitSystem

   Include it at the top of a page (header.php does this already) and the page is
   protected:  you must be logged in, and your role must be allowed into the
   folder the page lives in (office/ or warehouse/).

   Pages that must work while logged out (index.php, login.php, logout.php)
   define('AUTH_PUBLIC', true); BEFORE including this file.                     */

if (session_id() === '') {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    session_start();
}

if (!function_exists('is_logged_in')) {

    /* ---- WHO GOES WHERE: sections each role may open (edit here) ----
       a role with one section lands there after login (StockTrack = office,
       WarehouseProLite = warehouse); a role with both gets a chooser page.  */
    function role_sections() {
        return array(
            'admin'             => array('office', 'warehouse'),
            'office_clerk'      => array('office'),
            'warehouse_manager' => array('warehouse'),
            'warehouse_clerk'   => array('warehouse'),
            'qa'                => array('warehouse')
        );
    }

    /* section (folder) the running script is in: 'office', 'warehouse' or '' */
    function current_section() {
        $leaf = basename(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])));
        return ($leaf === 'office' || $leaf === 'warehouse') ? $leaf : '';
    }

    /* URL path of the UKFruitSystem folder, e.g. /UKFruitSystem/ (any folder name) */
    function site_root() {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if (current_section() !== '') { $dir = dirname($dir); }
        return rtrim(str_replace('\\', '/', $dir), '/') . '/';
    }

    function is_logged_in() { return isset($_SESSION['user_id']); }

    function current_user() {
        if (!is_logged_in()) { return null; }
        return array('id' => (int)$_SESSION['user_id'],
                     'username' => $_SESSION['username'],
                     'role' => $_SESSION['role']);
    }

    function can_access_section($section) {
        if (!is_logged_in()) { return false; }
        $map = role_sections();
        $role = $_SESSION['role'];
        return isset($map[$role]) && in_array($section, $map[$role]);
    }

    /* where this user starts after login, relative to the site root */
    function home_for_user() {
        $map  = role_sections();
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        if (!isset($map[$role])) { return 'index.php'; }
        return count($map[$role]) > 1 ? 'home.php' : $map[$role][0] . '/dashboard.php';
    }

    /* only a plain page inside office/ or warehouse/ is accepted as a return address */
    function safe_next($next) {
        if (is_string($next) &&
            preg_match('~^(office|warehouse)/[A-Za-z0-9_]+\.php(\?[A-Za-z0-9_=&%.-]*)?$~', $next)) {
            return $next;
        }
        return '';
    }

    function login_user($row) {
        session_regenerate_id(true);                 // stops session fixation
        $_SESSION = array();
        $_SESSION['user_id']       = (int)$row['id'];
        $_SESSION['username']      = $row['username'];
        $_SESSION['role']          = $row['role'];
        $_SESSION['last_activity'] = time();
        /* keys the existing pages already read (clerk_id / approved_by / tech_id) */
        $_SESSION['user']       = $row['username'];
        $_SESSION['wh_user_id'] = (int)$row['id'];
        $_SESSION['wh_user']    = $row['username'];
        $_SESSION['wh_role']    = $row['role'];
    }

    function logout_user() {
        $_SESSION = array();
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'],
                      $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    function require_login() {
        if (is_logged_in()) {
            $idle_limit = 1800;                      // sign out after 30 minutes idle
            if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $idle_limit) {
                logout_user();
                header('Location: ' . site_root() . 'index.php?error=timeout');
                exit();
            }
            $_SESSION['last_activity'] = time();
            return;
        }
        $here = '';
        if (current_section() !== '') {
            $here = current_section() . '/' . basename($_SERVER['SCRIPT_NAME']);
            if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
                $here .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        $target = site_root() . 'index.php?error=login';
        if (safe_next($here) !== '') { $target .= '&next=' . urlencode($here); }
        header('Location: ' . $target);
        exit();
    }

    function require_section($section) {
        require_login();
        if (can_access_section($section)) { return; }
        if (!headers_sent()) { header('HTTP/1.1 403 Forbidden'); }
        $root = site_root();
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Access denied</title>'
           . '<link rel="stylesheet" href="' . $root . 'office/assets/style.css"></head>'
           . '<body><main class="container"><h2>Access denied</h2>'
           . '<p class="notice">Your role (<strong>' . htmlspecialchars($_SESSION['role']) . '</strong>) '
           . 'is not allowed into the ' . htmlspecialchars($section) . ' section.</p>'
           . '<p><a href="' . $root . htmlspecialchars(home_for_user()) . '">Go to my section</a>'
           . ' | <a href="' . $root . 'logout.php">Sign out</a></p>'
           . '</main></body></html>';
        exit();
    }
}

/* Runs every time this file is included: protect the page unless it is public */
if (!defined('AUTH_PUBLIC')) {
    require_login();
    $__section = current_section();
    if ($__section !== '') { require_section($__section); }
}
