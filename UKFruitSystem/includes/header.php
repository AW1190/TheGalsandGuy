<?php
/* Shared header for UKFruitSystem.
   Including auth.php here is what protects every page that uses this header. */
require_once dirname(__FILE__) . '/auth.php';

$section     = current_section();
$isWarehouse = ($section == 'warehouse');

$base        = site_root();                 // e.g. /UKFruitSystem/  (works with any folder name)
$module      = $isWarehouse ? 'warehouse/' : 'office/';
$currentPage = basename($_SERVER['PHP_SELF']);
$me          = current_user();
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
            <?php if (can_access_section('office')): ?>
                <a href="<?php echo $base; ?>office/dashboard.php">Office</a>
            <?php endif; ?>
            <a href="<?php echo $base; ?>warehouse/deliveries.php">Deliveries</a>
            <a href="<?php echo $base; ?>warehouse/stocktake_new.php">Stock-Take</a>
            <a href="<?php echo $base; ?>warehouse/stocktakes.php">Take History</a>
            <a href="<?php echo $base; ?>warehouse/adjustments.php">Adjustments</a>
            <a href="<?php echo $base; ?>warehouse/qa_samples.php">QA Samples</a>
            <a href="<?php echo $base; ?>warehouse/reports.php">Reports</a>
        <?php else: ?>
            <a href="<?php echo $base; ?>office/dashboard.php">Dashboard</a>
            <?php if (can_access_section('warehouse')): ?>
                <a href="<?php echo $base; ?>warehouse/dashboard.php">Warehouse</a>
            <?php endif; ?>
            <a href="<?php echo $base; ?>office/products.php">Products</a>
            <a href="<?php echo $base; ?>office/orders.php">Orders</a>
            <a href="<?php echo $base; ?>office/customers.php">Customers</a>
            <a href="<?php echo $base; ?>office/reports.php">Reports</a>
        <?php endif; ?>
        <a href="<?php echo $base; ?>logout.php" class="right">Logout (<?php echo htmlspecialchars($me['username']); ?>)</a>
    </nav>
</header>

<main class="container">
