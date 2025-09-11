<?php
// public/index.php

// Include the encryption functions
require_once __DIR__ . '/../includes/encryption.php';

// Load configuration (e.g., secret key)
require_once __DIR__ . '/../config/config.php';

// Hardcoded secret key (move to config.php in production)
$secret_key = $config['encryption_key'] ?? 'your_secret_key_32_chars_long'; // 32 bytes for AES-256
$method = 'aes-256-cbc';

// Get the encrypted parameter from URL
$enc = isset($_GET['enc']) ? $_GET['enc'] : '';

// Decrypt if present, otherwise use mock data
$employee_data = [
    'id' => '',
    'name' => '',
    'position' => '',
    'department' => '',
    'baseSalary' => 0.00,
    'ot_normal_hours' => 0,
    'ot_special_hours' => 0,
    'ot_after_midnight_hours' => 0,
    'ot_holiday_hours' => 0,
    'total_normal_ot' => 0.00,
    'normal_amount_out' => 0.00,
    'after_night_ot' => 0.00,
    'ot_after_night' => 0.00,
    'holiday_normal_ot' => 0.00,
    'total_salary' => 0.00,
];

if ($enc) {
    try {
        // Decrypt the data
        $decrypted_json = decryptData($enc, $secret_key, $method);
        
        if ($decrypted_json) {
            $data = json_decode($decrypted_json, true);
            if ($data) {
                // Map to employee_data (in real app, use ID to fetch from DB)
                $employee_data['id'] = $data['id'] ?? '';
                $employee_data['name'] = $data['name'] ?? '';
                $employee_data['position'] = $data['position'] ?? '';
                $employee_data['department'] = $data['department'] ?? '';
                $employee_data['baseSalary'] = $data['baseSalary'] ?? 0.00;
                $employee_data['ot_normal_hours'] = $data['ot_normal_hours'] ?? 0;
                $employee_data['ot_special_hours'] = $data['ot_special_hours'] ?? 0;
                $employee_data['ot_after_midnight_hours'] = $data['ot_after_midnight_hours'] ?? 0;
                $employee_data['ot_holiday_hours'] = $data['ot_holiday_hours'] ?? 0;
                $employee_data['total_normal_ot'] = $data['total_normal_ot'] ?? 0.00;
                $employee_data['normal_amount_out'] = $data['normal_amount_out'] ?? 0.00;
                $employee_data['after_night_ot'] = $data['after_night_ot'] ?? 0.00;
                $employee_data['ot_after_night'] = $data['ot_after_night'] ?? 0.00;
                $employee_data['holiday_normal_ot'] = $data['holiday_normal_ot'] ?? 0.00;
                // Calculate total salary
                $employee_data['total_salary'] = $employee_data['baseSalary'] + $employee_data['total_normal_ot'] + $employee_data['normal_amount_out'] + $employee_data['after_night_ot'] + $employee_data['ot_after_night'] + $employee_data['holiday_normal_ot'];
            }
        }
    } catch (Exception $e) {
        // Handle decryption error
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit;
    }
} else {
    // Mock data if no enc param
    $mock_data = [
        'id' => 'EMP001',
        'name' => 'ចិត្រ្តា ប៉ាង',
        'position' => 'បុគ្គលិកអប់រំ',
        'department' => 'HR',
        'baseSalary' => 500.00,
        'ot_normal_hours' => 5,
        'ot_special_hours' => 3,
        'ot_after_midnight_hours' => 2,
        'ot_holiday_hours' => 1,
        'total_normal_ot' => 50.00,
        'normal_amount_out' => 30.00,
        'after_night_ot' => 20.00,
        'ot_after_night' => 15.00,
        'holiday_normal_ot' => 10.00,
        'total_salary' => 625.00,
    ];
    // Encrypt mock data for demonstration
    $encrypted_mock = encryptData(json_encode($mock_data), $secret_key, $method);
    // Simulate URL parameter for testing
    $_GET['enc'] = $encrypted_mock;
    $decrypted_json = decryptData($encrypted_mock, $secret_key, $method);
    if ($decrypted_json) {
        $data = json_decode($decrypted_json, true);
        if ($data) {
            $employee_data = $data;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Check</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Google Fonts: Noto Sans Khmer for Khmer script -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS for improvements -->
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container my-5">
        <div class="card">
            <div class="header1">
                <h4>ព័ត៌មានប្រាក់បៀវត្ស</h4>
            </div>
            <div class="card-body">
                <!-- Employee Info -->
                <div class="employee-info row">
                    <div class="col-md-4">
                        <strong>ល.អ.ត ៖</strong> <?php echo htmlspecialchars($employee_data['id']); ?>
                    </div>
                    <div class="col-md-4">
                        <strong>ឈ្មោះ ៖</strong> <?php echo htmlspecialchars($employee_data['name']); ?>
                    </div>
                    <div class="col-md-4">
                        <strong>ប្រាក់បៀវត្ស ៖</strong> <?php echo number_format($employee_data['baseSalary'], 2); ?> $
                    </div>
                </div>

                <!-- OT Summary Table -->
                <div class="header">
                    <h4>ប្រាក់ខែគោល ៖ <?php echo number_format($employee_data['baseSalary'], 2); ?> $</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped ot-table">
                        <tbody>
                            <tr>
                                <td>ថែមម៉ោង៖</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>ពេលថ្ងៃ៖</td>
                                <td><?php echo $employee_data['ot_normal_hours']; ?> ម៉ោង</td>
                                <td>Total Normal OT៖</td>
                                <td><?php echo number_format($employee_data['total_normal_ot'], 2); ?> $</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>ពេលយប់ ៖</td>
                                <td><?php echo $employee_data['ot_special_hours']; ?> ម៉ោង</td>
                                <td>Normal Amount out៖</td>
                                <td><?php echo number_format($employee_data['normal_amount_out'], 2); ?> $</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>ថ្ងៃយប់ ៖</td>
                                <td><?php echo $employee_data['ot_after_midnight_hours']; ?> ម៉ោង</td>
                                <td>After Night OT៖</td>
                                <td><?php echo number_format($employee_data['after_night_ot'], 2); ?> $</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>OT After Night៖</td>
                                <td><?php echo number_format($employee_data['ot_after_night'], 2); ?> $</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="2"></td>
                                <td>Holiday Normal OT៖</td>
                                <td><?php echo number_format($employee_data['holiday_normal_ot'], 2); ?> $</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="header">
                        <h4>ប្រាក់ខែគោល ៖ <?php echo number_format($employee_data['baseSalary'], 2); ?> $</h4>
                    </div>
                    <table class="table table-striped ot-table">
                        <tbody>
                            <tr>
                                <td>ថែមម៉ោង៖</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>ពេលថ្ងៃ ៖</td>
                                <td><?php echo $employee_data['ot_normal_hours']; ?> ម៉ោង</td>
                                <td>Total Normal OT៖</td>
                                <td><?php echo number_format($employee_data['total_normal_ot'], 2); ?> $</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>ពេលយប់ ៖</td>
                                <td><?php echo $employee_data['ot_special_hours']; ?> ម៉ោង</td>
                                <td>Normal Amount out៖</td>
                                <td><?php echo number_format($employee_data['normal_amount_out'], 2); ?> $</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>ថ្ងៃយប់ ៖</td>
                                <td><?php echo $employee_data['ot_after_midnight_hours']; ?> ម៉ោង</td>
                                <td>After Night OT៖</td>
                                <td><?php echo number_format($employee_data['after_night_ot'], 2); ?> $</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>OT After Night៖</td>
                                <td><?php echo number_format($employee_data['ot_after_night'], 2); ?> $</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="2"></td>
                                <td>Holiday Normal OT៖</td>
                                <td><?php echo number_format($employee_data['holiday_normal_ot'], 2); ?> $</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="header">
                        <h4>ប្រាក់ខែសរុប ៖ <?php echo number_format($employee_data['total_salary'], 2); ?> $</h4>
                    </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional for interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Custom JS if needed (e.g., for dynamic updates) -->
    <script>
        // Example: You can add JS to fetch more data via AJAX if needed, but for now, static
        console.log('Page loaded');
    </script>
</body>
</html>