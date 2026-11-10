<?php

declare(strict_types=1);

$allowedOrigins = [
    'http://localhost:5173',
    'http://127.0.0.1:5173',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Vary: Origin');
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET, OPTIONS');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$configPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'php-api-config.local.php';
if (!is_file($configPath)) {
    http_response_code(503);
    echo json_encode(['error' => 'Create php-api-config.local.php from php-api-config.example.php first.']);
    exit;
}

$config = require $configPath;
if (
    !is_array($config)
    || !isset($config['host'], $config['database'], $config['username'], $config['password'])
    || !is_string($config['host'])
    || !is_string($config['database'])
    || !is_string($config['username'])
    || !is_string($config['password'])
) {
    http_response_code(500);
    echo json_encode(['error' => 'PHP API database configuration is invalid.']);
    exit;
}
if ($config['username'] === 'your_mysql_username') {
    http_response_code(503);
    echo json_encode(['error' => 'Set your MySQL username in php-api-config.local.php.']);
    exit;
}
$threshold = filter_var($config['low_stock_threshold'] ?? null, FILTER_VALIDATE_INT);
if ($threshold === false || $threshold < 0) {
    http_response_code(500);
    echo json_encode(['error' => 'PHP API database configuration is invalid.']);
    exit;
}

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['host'], $config['database']),
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    );

    $summary = $pdo->query(
        "SELECT COALESCE(SUM(CASE WHEN status = 'active' THEN total ELSE 0 END), 0) AS revenue,
                COUNT(*) AS orders
         FROM orders",
    )->fetch();

    $customerCount = (int) $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();

    $lowStockQuery = $pdo->prepare('SELECT COUNT(*) FROM products WHERE stock < :threshold');
    $lowStockQuery->execute(['threshold' => $threshold]);
    $lowStockCount = (int) $lowStockQuery->fetchColumn();

    $activity = $pdo->query(
        'SELECT status, COUNT(*) AS count
         FROM orders
         GROUP BY status',
    )->fetchAll();

    $latestOrderDate = $pdo->query('SELECT MAX(order_date) FROM orders')->fetchColumn();
    $latestOrder = $latestOrderDate
        ? new DateTimeImmutable((string) $latestOrderDate)
        : new DateTimeImmutable();
    $firstMonth = $latestOrder->modify('first day of this month')->modify('-5 months');
    $monthlyRevenueQuery = $pdo->prepare(
        "SELECT DATE_FORMAT(order_date, '%Y-%m') AS period,
                SUM(total) AS revenue
         FROM orders
         WHERE status = 'active'
           AND order_date >= :range_start
           AND order_date <= :range_end
         GROUP BY DATE_FORMAT(order_date, '%Y-%m')
         ORDER BY period",
    );
    $monthlyRevenueQuery->execute([
        'range_start' => $firstMonth->format('Y-m-d 00:00:00'),
        'range_end' => $latestOrder->format('Y-m-d H:i:s'),
    ]);

    $revenueByPeriod = [];
    foreach ($monthlyRevenueQuery->fetchAll() as $row) {
        $revenueByPeriod[$row['period']] = (float) $row['revenue'];
    }

    $monthlyRevenue = [];
    for ($index = 0; $index < 6; $index++) {
        $month = $firstMonth->modify("+$index months");
        $period = $month->format('Y-m');
        $monthlyRevenue[] = [
            'month' => $month->format('M'),
            'revenue' => $revenueByPeriod[$period] ?? 0,
        ];
    }

    $recentOrders = $pdo->query(
        'SELECT o.id, c.name AS customer, o.order_date, o.total, o.status
         FROM orders AS o
         LEFT JOIN customers AS c ON c.id = o.customer_id
         ORDER BY o.order_date DESC, o.id DESC
         LIMIT 5',
    )->fetchAll();

    $recentOrders = array_map(
        static fn (array $order): array => [
            'id' => (int) $order['id'],
            'customer' => $order['customer'] ?? 'Unknown customer',
            'date' => $order['order_date'],
            'amount' => (float) $order['total'],
            'status' => $order['status'],
        ],
        $recentOrders,
    );

    echo json_encode([
        'metrics' => [
            'revenue' => (float) $summary['revenue'],
            'orders' => (int) $summary['orders'],
            'customers' => $customerCount,
            'lowStock' => $lowStockCount,
        ],
        'orderActivity' => array_map(
            static fn (array $row): array => [
                'status' => $row['status'],
                'count' => (int) $row['count'],
            ],
            $activity,
        ),
        'monthlyRevenue' => $monthlyRevenue,
        'recentOrders' => $recentOrders,
        'lowStockThreshold' => $threshold,
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    error_log('Dashboard API query failed: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Could not load dashboard data from the database.']);
}
