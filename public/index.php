<?php
// public/index.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/encryption.php';
require_once __DIR__ . '/../config/config.php';

$secret_key = $config['encryption_key'] ?? 'your_secret_key_32_chars_long';
$method = 'aes-256-cbc';

// Initialize employee_data with valid keys
$employee_data = [
    'Emp_ID' => '',
    'KhmerName' => '',
    'SalaryDTKH' => '',
    'Basic' => 0,
    'Total_Normal_OT' => 0,
    'Normal_Amount' => 0,
    'Aft_Night_OT' => 0,
    'OT_Aft_Night' => 0,
    'Holiday_Normal_OT' => 0,
    'Total_HOT' => 0,
    'Night_Wage' => 0,
    'Alw_Att' => 0,
    'Alw_Housing' => 0,
    'Alw_GSTARS' => 0,
    'Alw_License' => 0,
    'Alw_Position' => 0,
    'Alw_Additional' => 0,
    'Seniority' => 0,
    'SaleAL' => 0,
    'Adjust' => 0,
    'Total_1' => 0,
    'Abs_Day' => 0,        // Updated key
    'Abs_Hour' => 0,       // Updated key
    'Abs_Unpaid' => 0,     // Updated key
    'Abs_Amount' => 0,
    'Alw_KHNY' => 0,
    'Advance' => 0,
    'Deduct' => 0,
    'Pension' => 0,
    'Total_2' => 0
];

// Updated key mapping to match generate.php
$key_mapping = [
    'e' => 'Emp_ID',
    'k' => 'KhmerName',
    's' => 'SalaryDTKH',
    'b' => 'Basic',
    'tn' => 'Total_Normal_OT',
    'na' => 'Normal_Amount',
    'an' => 'Aft_Night_OT',
    'oa' => 'OT_Aft_Night',
    'hn' => 'Holiday_Normal_OT',
    'th' => 'Total_HOT',
    'nw' => 'Night_Wage',
    'aa' => 'Alw_Att',
    'ah' => 'Alw_Housing',
    'ag' => 'Alw_GSTARS',
    'al' => 'Alw_License',
    'ap' => 'Alw_Position',
    'a1' => 'Alw_Additional',
    'sn' => 'Seniority',
    'sa' => 'SaleAL',
    'aj' => 'Adjust',
    't1' => 'Total_1',
    'a2' => 'Abs_Day',      // Updated key
    'a3' => 'Abs_Hour',     // Updated key
    'au' => 'Abs_Unpaid',   // Updated key
    'am' => 'Abs_Amount',
    'ak' => 'Alw_KHNY',
    'av' => 'Advance',
    'dd' => 'Deduct',
    'pn' => 'Pension',
    't2' => 'Total_2'
];

// Get the encrypted parameter from URL
$enc = isset($_GET['enc']) ? $_GET['enc'] : '';

if ($enc) {
    try {
        // Decrypt the data
        $compressed = decryptData($enc, $secret_key, $method);
        if ($compressed === false) {
            throw new Exception('Decryption failed');
        }

        // Decompress the data
        $json = gzuncompress($compressed);
        if ($json === false) {
            throw new Exception('Decompression failed');
        }

        // Decode JSON
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode failed: ' . json_last_error_msg());
        }

        // Map short keys to long keys
        foreach ($key_mapping as $short => $long) {
            if (isset($data[$short])) {
                $employee_data[$long] = $data[$short];
            }
        }

        // Validate all required keys are present
        foreach ($employee_data as $key => $value) {
            if ($value === '' || $value === 0) {
                throw new Exception("Missing or invalid data for key: $key");
            }
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit;
    }
} else {
    // Use mock data only if no enc parameter is provided
    $employee_data = [
        'Emp_ID' => '12345',
        'KhmerName' => 'សុខ ស៊ីណា',
        'SalaryDTKH' => 'ខែ មករា ២០២៥',
        'Basic' => 300,
        'Total_Normal_OT' => 10,
        'Normal_Amount' => 50,
        'Aft_Night_OT' => 5,
        'OT_Aft_Night' => 30,
        'Holiday_Normal_OT' => 8,
        'Total_HOT' => 48,
        'Night_Wage' => 20,
        'Alw_Att' => 10,
        'Alw_Housing' => 50,
        'Alw_GSTARS' => 15,
        'Alw_License' => 25,
        'Alw_Position' => 30,
        'Alw_Additional' => 10,
        'Seniority' => 20,
        'SaleAL' => 15,
        'Adjust' => 5,
        'Total_1' => 568,
        'Abs_Day' => 1,      // Updated key
        'Abs_Hour' => 8,     // Updated key
        'Abs_Unpaid' => 4,   // Updated key
        'Abs_Amount' => 20,
        'Alw_KHNY' => 100,
        'Advance' => 200,
        'Deduct' => 10,
        'Pension' => 15,
        'Total_2' => 423
    ];
}

if (isset($_GET['action']) && $_GET['action'] === 'download_pdf') {
    // Use the already populated $employee_data
    require_once __DIR__ . '/../includes/pdf_generator.php';
    generateSalaryPDF($employee_data);
    exit;
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Khmer&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
    <div class="container my-5">
        <div class="card" id="salaryCard">
            <div class="card-header text-center">
                <h4 class="mb-0">ព័ត៌មានប្រាក់បៀវត្សន៏</h4>
            </div>
            <div class="card-body">
                <div class="vertical-line-container">
                    <div class="upper-content">
                        <div class="employee-info row gy-2 mb-4">
                            <div class="col-12 col-md-4">
                                <strong>ល.អ.ត ៖</strong> <?php echo htmlspecialchars($employee_data['Emp_ID']); ?>
                            </div>
                            <div class="col-12 col-md-4">
                                <strong>ឈ្មោះ ៖</strong> <?php echo htmlspecialchars($employee_data['KhmerName']); ?>
                            </div>
                            <div class="col-12 col-md-4">
                                <strong>បៀវត្សន៏ខែ ៖</strong> <?php echo htmlspecialchars($employee_data['SalaryDTKH']); ?>
                            </div>
                        </div>
                        <div class="header text-center mb-4">
                            <h4 class="mb-0">ប្រាក់ខែគោល ៖ <?php echo number_format($employee_data['Basic']); ?> $</h4>
                        </div>
                    </div>
                    <div class="vertical-line"></div>
                    <div class="lower-content">
                        <div class="table-responsive">
                            <table class="table table-striped ot-table">
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td>ថែមម៉ោង៖</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="disable"></td>
                                        <td>ពេលថ្ងៃ៖</td>
                                        <td><?php echo number_format($employee_data['Total_Normal_OT']); ?>&nbsp;ម៉ោង</td>
                                        <td></td>
                                        <td>ចំនួនទឹកប្រាក់៖</td>
                                        <td><?php echo number_format($employee_data['Normal_Amount']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>ពេលយប់៖</td>
                                        <td><?php echo number_format($employee_data['Aft_Night_OT']); ?>&nbsp;ម៉ោង</td>
                                        <td></td>
                                        <td>ចំនួនទឹកប្រាក់៖</td>
                                        <td><?php echo number_format($employee_data['OT_Aft_Night']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>ថ្ងៃឈប់៖</td>
                                        <td><?php echo number_format($employee_data['Holiday_Normal_OT']); ?>&nbsp;ម៉ោង</td>
                                        <td></td>
                                        <td>ចំនួនទឹកប្រាក់៖</td>
                                        <td><?php echo number_format($employee_data['Total_HOT']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់បន្ថែម&nbsp;វេនយប់៖</td>
                                        <td><?php echo number_format($employee_data['Night_Wage']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់ឧបត្ថម្ភ&nbsp;វត្តមាន៖</td>
                                        <td><?php echo number_format($employee_data['Alw_Att']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់ឧបត្ថម្ភ&nbsp;ការស្នាក់នៅ៖</td>
                                        <td><?php echo number_format($employee_data['Alw_Housing']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់បន្ថែម ជីស្ដា(G-STARS)៖</td>
                                        <td><?php echo number_format($employee_data['Alw_GSTARS']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់បន្ថែម License៖</td>
                                        <td><?php echo number_format($employee_data['Alw_License']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់បន្ថែម&nbsp;មុខតំណែង៖</td>
                                        <td><?php echo number_format($employee_data['Alw_Position']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់បន្ថែម ផ្សេងៗ៖</td>
                                        <td><?php echo number_format($employee_data['Alw_Additional']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់អតីតភាពការងារ៖</td>
                                        <td><?php echo number_format($employee_data['Seniority']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>ប្រាក់លក់ថ្ងៃឈប់ប្រចាំឆ្នាំ៖</td>
                                        <td><?php echo number_format($employee_data['SaleAL']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>កែសម្រួល៖</td>
                                        <td><?php echo number_format($employee_data['Adjust']); ?>&nbsp;$</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="header text-center mb-4">
                                <h4 class="mb-0">ប្រាក់បៀវត្សន៏សរុប៖ <?php echo number_format($employee_data['Total_1']); ?>&nbsp;$</h4>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped ot-table">
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td>អវត្តមាន៖</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>មាន&nbsp;ច្បាប់៖&nbsp;<?php echo number_format($employee_data['Abs_Day']); ?>&nbsp;ថ្ងៃ&nbsp;<?php echo number_format($employee_data['Abs_Hour']); ?>&nbsp;ម៉ោង</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>គ្មានច្បាប់៖&nbsp;<?php echo number_format($employee_data['Abs_Unpaid']); ?>&nbsp;ម៉ោង</td>
                                        <td></td>
                                        <td></td>
                                        <td><span class="fon">ចំនួនទឹកប្រាក់៖</span></td>
                                        <td><?php echo number_format($employee_data['Abs_Amount']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><span class="fon">ប្រាក់ឧបត្ថម្ភចូលឆ្នាំខ្មែរ&nbsp;៖</span></td>
                                        <td><?php echo number_format($employee_data['Alw_KHNY']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><span class="fon">ប្រាក់បៀវត្សន៏&nbsp;លើកទី&nbsp;១&nbsp;៖</span></td>
                                        <td><?php echo number_format($employee_data['Advance']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><span class="fon">ការផាកពិន័យផ្សេងៗ&nbsp;៖</span></td>
                                        <td><?php echo number_format($employee_data['Deduct']); ?>&nbsp;$</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><span class="fon">ភាគទានសោធន&nbsp;៖</span></td>
                                        <td><?php echo number_format($employee_data['Pension']); ?>&nbsp;$</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="header text-center mb-4">
                                <h4 class="mb-0">ប្រាក់បៀវត្សន៏&nbsp;លើកទី&nbsp;២៖ <?php echo number_format($employee_data['Total_2']); ?>&nbsp;$</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3 text-end">
            <a href="?action=download_pdf&enc=<?php echo urlencode($enc); ?>" class="btn btn-success me-2">ទាញយកជា PDF</a>
            <button onclick="downloadImage()" class="btn btn-success">ទាញយកជារូបភាព</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function downloadImage() {
            html2canvas(document.getElementById('salaryCard'), {
                scale: 2,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'salary_slip.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>
</html>