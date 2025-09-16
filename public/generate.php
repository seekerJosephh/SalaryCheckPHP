<?php
require_once __DIR__ . '/../includes/encryption.php';
require_once __DIR__ . '/../config/config.php';
$config = require __DIR__ . '/../config/config.php';
$secret_key = $config['encryption_key'];
$method = 'aes-256-cbc';

// Test data with valid keys
$test_data = [
    'e' => '12345',           // Emp_ID
    'k' => 'Joseph',     // KhmerName
    's' => 'ខែ មករា ២០២៥', // SalaryDTKH
    'b' => 1000,              // Basic
    'tn' => 10,               // Total_Normal_OT
    'na' => 50,               // Normal_Amount
    'an' => 5,                // Aft_Night_OT
    'oa' => 30,               // OT_Aft_Night
    'hn' => 8,                // Holiday_Normal_OT
    'th' => 48,               // Total_HOT
    'nw' => 20,               // Night_Wage
    'aa' => 10,               // Alw_Att
    'ah' => 50,               // Alw_Housing
    'ag' => 15,               // Alw_GSTARS
    'al' => 25,               // Alw_License
    'ap' => 30,               // Alw_Position
    'a1' => 10,               // Alw_Additional
    'sn' => 20,               // Seniority
    'sa' => 15,               // SaleAL
    'aj' => 5,                // Adjust
    't1' => 10068,            // Total_1
    'a2' => 1,                // Abs_Day
    'a3' => 8,                // Abs_Hour
    'au' => 4,                // Abs_Unpaid
    'am' => 20,               // Abs_Amount
    'ak' => 100,              // Alw_KHNY
    'av' => 200,              // Advance
    'dd' => 10,               // Deduct
    'pn' => 15,               // Pension
    't2' => 11423             // Total_2
];

// Convert to JSON and compress
$json = json_encode($test_data, JSON_UNESCAPED_UNICODE);
$compressed = gzcompress($json);

// Encrypt the compressed data
$enc = encryptData($compressed, $secret_key, $method);

// Use a shorter base URL
$base_url = 'localhost/SalaryCheck/public/index.php';
$url = $base_url . '?enc=' . urlencode($enc);

echo $url . "\n";
?>