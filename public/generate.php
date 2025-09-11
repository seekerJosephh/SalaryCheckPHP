<?php
require_once __DIR__ . '/../includes/encryption.php';
require_once __DIR__ . '/../config/config.php';
$config = require __DIR__ . '/../config/config.php'; // Load config
$secret_key = $config['encryption_key'];
$method = 'aes-256-cbc';

$test_data = ['id' => 'TEST002', 'name' => 'Test User', 'baseSalary' => 600.00]; // Custom test data
$json = json_encode($test_data);
$enc = encryptData($json, $secret_key, $method);
$url = 'http://localhost/SalaryCheck/public/index.php?enc=' . $enc;
echo "Test URL: " . $url . "\n";
?>