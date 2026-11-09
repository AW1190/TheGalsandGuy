<?php
/* home.php - landing page after login for users who may open both sections */
include 'includes/auth.php';          // login required

$office    = can_access_section('office');
$warehouse = can_access_section('warehouse');

/* only one section? go straight there */
if ($office && !$warehouse)  { header('Location: office/dashboard.php');    exit(); }
if ($warehouse && !$office)  { header('Location: warehouse/dashboard.php'); exit(); }

$me = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>UK Fruit Management System</title>
    <link rel="stylesheet" href="office/assets/style.css">
</head>
<body class="login-page">
<div class="login-card">
    <div align="center">
        <h1>UK Fruit Management System</h1>
        <p>Welcome, <?php echo htmlspecialchars($me['username']); ?>. Choose a section:</p>
    </div>
    <p align="center">
        <a href="office/dashboard.php">Office Management (StockTrack Pro)</a>
        &nbsp;|&nbsp;
        <a href="warehouse/dashboard.php">Warehouse Management (WarehouseProLite)</a>
    </p>
    <p align="center"><a href="logout.php">Sign out</a></p>
</div>
</body>
</html>
