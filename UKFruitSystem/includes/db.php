<?php
// includes/db.php - shared by office/ and warehouse/ (database from uk_fruits.sql)
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$DB_HOST = '127.0.0.1';
$DB_PORT = 3306;
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'uk_fruits';

// PHP 7+ removed the old mysql_* functions. If they are missing, define them
// on top of mysqli so the existing pages run unchanged (PHP 5.6 skips this).
if (!function_exists('mysql_connect')) {
    if (!function_exists('mysqli_connect')) {
        die('The mysqli extension is not enabled. In php.ini remove the ; before '
          . 'extension=mysqli, then restart Apache.');
    }
    mysqli_report(MYSQLI_REPORT_OFF);          // PHP 8.1+ throws by default
    $GLOBALS['__mysql_link'] = null;

    function __mysql_l($l) { return $l ? $l : $GLOBALS['__mysql_link']; }

    function mysql_connect($server, $user, $pass) {
        $host = $server; $port = 3306;
        if (strpos($server, ':') !== false) {
            list($host, $p) = explode(':', $server, 2);
            $port = (int)$p;
        }
        $l = @mysqli_connect($host, $user, $pass, '', $port);
        if ($l) { $GLOBALS['__mysql_link'] = $l; }
        return $l;
    }
    function mysql_error($l = null) {
        $l = __mysql_l($l);
        return $l ? mysqli_error($l) : mysqli_connect_error();
    }
    function mysql_select_db($db, $l = null) { return mysqli_select_db(__mysql_l($l), $db); }
    function mysql_set_charset($cs, $l = null) { return mysqli_set_charset(__mysql_l($l), $cs); }
    function mysql_query($sql, $l = null) { return mysqli_query(__mysql_l($l), $sql); }
    function mysql_real_escape_string($s, $l = null) { return mysqli_real_escape_string(__mysql_l($l), $s); }
    function mysql_insert_id($l = null) { return mysqli_insert_id(__mysql_l($l)); }
    function mysql_fetch_assoc($r) {
        if (!($r instanceof mysqli_result)) { return false; }
        $row = mysqli_fetch_assoc($r);
        return $row === null ? false : $row;
    }
    function mysql_num_rows($r) {
        return ($r instanceof mysqli_result) ? mysqli_num_rows($r) : false;
    }
    function mysql_result($r, $row, $field = 0) {
        if (!($r instanceof mysqli_result)) { return false; }
        mysqli_data_seek($r, $row);
        $data = mysqli_fetch_array($r);
        return $data === null ? false : $data[$field];
    }
}

$conn = mysql_connect($DB_HOST . ':' . $DB_PORT, $DB_USER, $DB_PASS);
if (!$conn) {
    die('Database connection failed: ' . mysql_error()
      . ' - is MySQL started in the XAMPP Control Panel?');
}
if (!mysql_select_db($DB_NAME, $conn)) {
    die("Database '$DB_NAME' not found - create it in phpMyAdmin and import uk_fruits.sql.");
}
if (!mysql_set_charset('utf8mb4', $conn)) { mysql_set_charset('utf8', $conn); }
?>
