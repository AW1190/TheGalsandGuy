<?php
include 'includes/auth.php';   // login required
include 'includes/db.php';
$name = $_POST['name'];
$price = $_POST['price'];
mysql_query("INSERT INTO products (name, price) VALUES ('$name', '$price')");
header("Location: products.php");
?>
