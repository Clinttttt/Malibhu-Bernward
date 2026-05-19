<?php
// Simple deployment health check
$checks = [];

// Check 1: PHP Version
$checks['PHP Version'] = [
    'status' => version_compare(PHP_VERSION, '7.0.0', '>='),
    'value' => PHP_VERSION,
    'required' => '7.0+'
];

// Check 2: SQLite Support
$checks['SQLite Support'] = [
    'status' => class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers()),
    'value' => class_exists('PDO') ? 'Available' : 'Not Available',
    'required' => 'Required'
];

// Check 3: Database Connection
try {
    $conn = new PDO('sqlite:' . __DIR__ . '/database.db');
    $checks['Database Connection'] = [
        'status' => true,
        'value' => 'Connected',
        'required' => 'Required'
    ];
} catch(PDOException $e) {
    $checks['Database Connection'] = [
        'status' => false,
        'value' => 'Failed: ' . $e->getMessage(),
        'required' => 'Required'
    ];
}

// Check 4: Database Writable
$checks['Database Writable'] = [
    'status' => is_writable(__DIR__ . '/database.db') || is_writable(__DIR__),
    'value' => is_writable(__DIR__ . '/database.db') ? 'Yes' : 'Check permissions',
    'required' => 'Required'
];

// Check 5: Sessions
$checks['Session Support'] = [
    'status' => function_exists('session_start'),
    'value' => function_exists('session_start') ? 'Available' : 'Not Available',
    'required' => 'Required'
];

// Check 6: Cookies
$checks['Cookie Support'] = [
    'status' => ini_get('session.use_cookies'),
    'value' => ini_get('session.use_cookies') ? 'Enabled' : 'Disabled',
    'required' => 'Required'
];

$allPassed = true;
foreach($checks as $check) {
    if(!$check['status']) {
        $allPassed = false;
        break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>System Health Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; font-weight: bold; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
        .summary { padding: 20px; border-radius: 5px; margin: 20px 0; }
        .summary.pass { background: #d4edda; color: #155724; }
        .summary.fail { background: #f8d7da; color: #721c24; }
        .note { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🏥 Malibhu Reservation System - Health Check</h1>
    
    <div class="summary <?= $allPassed ? 'pass' : 'fail' ?>">
        <?php if($allPassed): ?>
            ✅ <strong>All checks passed!</strong> Your system is ready to use.
        <?php else: ?>
            ❌ <strong>Some checks failed.</strong> Please fix the issues below.
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Check</th>
                <th>Status</th>
                <th>Current Value</th>
                <th>Required</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($checks as $name => $check): ?>
            <tr>
                <td><?= $name ?></td>
                <td class="<?= $check['status'] ? 'pass' : 'fail' ?>">
                    <?= $check['status'] ? '✅ PASS' : '❌ FAIL' ?>
                </td>
                <td><?= $check['value'] ?></td>
                <td><?= $check['required'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if($allPassed): ?>
    <div class="note">
        <strong>Next Steps:</strong><br>
        1. Go to <a href="index.php">Home Page</a><br>
        2. Register a new user account<br>
        3. Login as admin at <a href="admin/login.php">Admin Panel</a> (admin / admin123)<br>
        4. Delete this health-check.php file for security
    </div>
    <?php else: ?>
    <div class="note">
        <strong>Troubleshooting:</strong><br>
        - If database is not writable, set database.db permissions to 666<br>
        - If database connection fails, check if SQLite is enabled<br>
        - Contact your hosting support if issues persist
    </div>
    <?php endif; ?>

    <p style="text-align: center; color: #666; margin-top: 40px;">
        <small>Malibhu View Resort Reservation System v1.0</small>
    </p>
</body>
</html>
