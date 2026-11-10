<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$conn = mysql_connect("127.0.0.1:3306", "root", "");
mysql_select_db("uk_fruits", $conn);
mysql_set_charset('utf8', $conn);
?>