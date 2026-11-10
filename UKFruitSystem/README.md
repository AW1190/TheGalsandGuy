
<div align="center">

# 🍎 UKFruitSystem

### Local Setup & Database Connection Guide

![PHP 5.6](https://img.shields.io/badge/PHP-5.6-555555)
![MySQL / MariaDB](https://img.shields.io/badge/MySQL%20%2F%20MariaDB-555555)
![XAMPP](https://img.shields.io/badge/XAMPP-555555)

</div>

---


This guide explains how to run UKFruitSystem locally and connect the merged Office and Warehouse applications to the uk_fruits database.



## 1. XAMPP Setup

The application currently uses PHP 5.6 and legacy MySQL functions.

My working setup uses two XAMPP installations:

- **PHP 5.6 XAMPP:** Apache running on port `8080`.
- **Newer XAMPP:** MySQL/MariaDB running on port `3306`.

Start Apache in the PHP 5.6 XAMPP installation and MySQL in the newer installation.

## 2. Application Files

Download the `UKFruitSystem` folder from the GitHub repository.

Copy the folder into the PHP 5.6 XAMPP `htdocs` directory.

Example:

`xampp56/htdocs/UKFruitSystem/`

The application contains:

- `office/` – Products, customers, orders and reports.
- `warehouse/` – Deliveries, stocktaking, adjustments, QA samples and reports.
- `includes/` – Shared database connection, header and footer.
- `index.php` – Main application homepage.

## 3. Database Setup

The application now uses Amber's updated **`uk_fruits`** database.

Each developer needs to import the database into their local MySQL installation.

1. Obtain Amber's `uk_fruits.sql` file.
2. Open phpMyAdmin at `http://localhost/phpmyadmin`.
3. Click **New** and create a database called `uk_fruits`.
4. Select `uk_fruits` from the left sidebar.
5. Click the **Import** tab.
6. Choose `uk_fruits.sql` and click **Go**.
7. Check that the tables and records have been imported successfully.

**Important:** Downloading the PHP application from GitHub does not automatically install the database. The SQL file must be imported separately.

 ## 4. Database Connection

The central database connection is located in:

`UKFruitSystem/includes/db.php`

My current configuration uses:

| Setting | Value |
|---|---|
| Host | `127.0.0.1` |
| Port | `3306` |
| Database | `uk_fruits` |
| Username | `root` |
| Password | Empty (local development only) |

Check that the connection settings match your local MySQL configuration.

Both Office and Warehouse use this shared database connection.

## 5. Running the Application

Once Apache and MySQL are running, open:

http://localhost:8080/UKFruitSystem/

From the homepage, you can access:

- **Office Management:** Products, customers, orders and reports.
- **Warehouse Management:** Deliveries, stocktaking, adjustments, QA samples and reports.
