
<?php
/* Shared header for UKFruitSystem */

if (session_id() == '') {
    session_start();
}

$section = basename(dirname($_SERVER['SCRIPT_FILENAME']));
$isWarehouse = ($section == 'warehouse');

$base = '/UKFruitSystem/';
$module = $isWarehouse ? 'warehouse/' : 'office/';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>UK Fruit Management System</title>
    <link rel="stylesheet" href="<?php echo $base . $module; ?>assets/style.css">
</head>
<body>
<header class="topbar">
    <h1 class="logo">UK Fruit Management System</h1>

    <nav>
        <?php if ($isWarehouse): ?>
            <a href="<?php echo $base; ?>warehouse/dashboard.php">Dashboard</a>
            <a href="<?php echo $base; ?>office/dashboard.php">Office</a>
            <a href="<?php echo $base; ?>warehouse/deliveries.php">Deliveries</a>
            <a href="<?php echo $base; ?>warehouse/stocktake_new.php">Stock-Take</a>
            <a href="<?php echo $base; ?>warehouse/stocktakes.php">Take History</a>
            <a href="<?php echo $base; ?>warehouse/adjustments.php">Adjustments</a>
            <a href="<?php echo $base; ?>warehouse/qa_samples.php">QA Samples</a>
            <a href="<?php echo $base; ?>warehouse/reports.php">Reports</a>
            <a href="<?php echo $base; ?>warehouse/logout.php" class="right">Logout</a>
        <?php else: ?>
            <a href="<?php echo $base; ?>office/dashboard.php">Dashboard</a>
            <a href="<?php echo $base; ?>warehouse/dashboard.php">Warehouse</a>
            <a href="<?php echo $base; ?>office/products.php">Products</a>
            <a href="<?php echo $base; ?>office/orders.php">Orders</a>
            <a href="<?php echo $base; ?>office/customers.php">Customers</a>
            <a href="<?php echo $base; ?>office/reports.php">Reports</a>
            <a href="<?php echo $base; ?>office/logout.php" class="right">Logout</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
