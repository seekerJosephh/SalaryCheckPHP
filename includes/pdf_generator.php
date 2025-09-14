<?php
// includes/pdf_generator.php

require_once __DIR__ . '/../vendor/autoload.php'; // Load Composer autoloader (mPDF)

use Mpdf\Mpdf;

/**
 * Generates a salary slip PDF using mPDF.
 *
 * @param array $employee_data The data array with keys like 'Emp_ID', 'KhmerName', etc.
 */
function generateSalaryPDF($employee_data) {
    // Define custom temp directory
    $tempDir = __DIR__ . '/../tmp';

    // Ensure temp directory exists and is writable
    try {
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                throw new Exception("Failed to create temporary directory: $tempDir");
            }
        }
        if (!is_writable($tempDir)) {
            if (!chmod($tempDir, 0755)) {
                throw new Exception("Temporary directory $tempDir is not writable");
            }
        }
    } catch (Exception $e) {
        error_log("PDF Temp Dir Error: " . $e->getMessage());
        http_response_code(500);
        echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        exit;
    }

    // mPDF configuration with custom temp directory and Khmer support
    $mpdfConfig = [
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P', // Portrait
        'tempDir' => $tempDir, // Custom writable temp directory
        'fontDir' => [__DIR__ . '/../fonts'], // Optional: Custom font directory
        'fontdata' => [
            'khmer' => [
                'R' => 'khmerosmoul', // Built-in mPDF Khmer font
                'useOTL' => 0xFF, // Enable OpenType Layout for Khmer
                'useKashida' => 75,
            ],
            'notokhmer' => [
                'R' => 'NotoSansKhmer-Regular.ttf', // Place in /fonts if using
                'B' => 'NotoSansKhmer-Bold.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ],
        ],
        'default_font' => 'khmer', // Use built-in Khmer font
        'default_font_size' => 10,
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 16,
        'margin_bottom' => 16,
        'margin_header' => 9,
        'margin_footer' => 9,
    ];

    try {
        // Initialize mPDF
        $mpdf = new Mpdf($mpdfConfig);

        // CSS for styling (Bootstrap-inspired, minimal for PDF)
        $css = '
        @import url(https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;500;600;700&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Khmer&display=swap);
        body { font-family: "khmer", "Noto Sans Khmer", sans-serif; font-size: 10pt; color: #000; }
        .container { max-width: 800px; margin: 0 auto; padding: 10px; }
        .card { border: 1px solid #000; padding: 10px; }
        .header1 { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 10px; }
        .header { text-align: center; font-size: 12pt; font-weight: bold; margin: 10px 0; }
        .employee-info { margin-bottom: 10px; }
        .employee-info div { margin-bottom: 5px; }
        .table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .table td { padding: 5px; text-align: left; vertical-align: top; border: 1px solid #000; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #f9f9f9; }
        .disable { border: none; }
        .fon { font-weight: bold; }
        ';

        // HTML content (matches index.php structure)
        $html = '
        <div class="container">
            <div class="card">
                <div class="header1">
                    <h4>ព័ត៌មានប្រាក់បៀវត្សន៏</h4>
                </div>
                <div class="card-body">
                    <!-- Employee Info -->
                    <div class="employee-info">
                        <div><strong>ល.អ.ត ៖</strong> ' . htmlspecialchars($employee_data['Emp_ID']) . '</div>
                        <div><strong>ឈ្មោះ ៖</strong> ' . htmlspecialchars($employee_data['KhmerName']) . '</div>
                        <div><strong>បៀវត្សន៏ខែ ៖</strong> ' . htmlspecialchars($employee_data['SalaryDTKH']) . '</div>
                    </div>
                    <!-- Basic Salary -->
                    <div class="header">
                        <h4>ប្រាក់ខែគោល ៖ ' . number_format($employee_data['Basic']) . ' $</h4>
                    </div>
                    <!-- OT Table -->
                    <div class="table-responsive">
                        <table class="table table-striped ot-table">
                            <tbody>
                                <tr><td></td><td>ថែមម៉ោង៖</td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="disable"></td><td>ពេលថ្ងៃ៖</td><td>' . number_format($employee_data['Total_Normal_OT']) . '&nbsp;ម៉ោង</td><td></td><td>ចំនួនទឹកប្រាក់៖</td><td>' . number_format($employee_data['Normal_Amount']) . '&nbsp;$</td></tr>
                                <tr><td></td><td>ពេលយប់៖</td><td>' . number_format($employee_data['Aft_Night_OT']) . '&nbsp;ម៉ោង</td><td></td><td>ចំនួនទឹកប្រាក់៖</td><td>' . number_format($employee_data['OT_Aft_Night']) . '&nbsp;$</td></tr>
                                <tr><td></td><td>ថ្ងៃឈប់៖</td><td>' . number_format($employee_data['Holiday_Normal_OT']) . '&nbsp;ម៉ោង</td><td></td><td>ចំនួនទឹកប្រាក់៖</td><td>' . number_format($employee_data['Total_HOT']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់បន្ថែម&nbsp;វេនយប់៖</td><td>' . number_format($employee_data['Night_Wage']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់ឧបត្ថម្ភ&nbsp;វត្តមាន៖</td><td>' . number_format($employee_data['Alw_Att']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់ឧបត្ថម្ភ&nbsp;ការស្នាក់នៅ៖</td><td>' . number_format($employee_data['Alw_Housing']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់បន្ថែម ជីស្ដា(G-STARS)៖</td><td>' . number_format($employee_data['Alw_GSTARS']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់បន្ថែម License៖</td><td>' . number_format($employee_data['Alw_License']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់បន្ថែម&nbsp;មុខតំណែង៖</td><td>' . number_format($employee_data['Alw_Position']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់បន្ថែម ផ្សេងៗ៖</td><td>' . number_format($employee_data['Alw_Additional']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់អតីតភាពការងារ៖</td><td>' . number_format($employee_data['Seniority']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>ប្រាក់លក់ថ្ងៃឈប់ប្រចាំឆ្នាំ៖</td><td>' . number_format($employee_data['SaleAL']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td>កែសម្រួល៖</td><td>' . number_format($employee_data['Adjust']) . '&nbsp;$</td></tr>
                            </tbody>
                        </table>
                        <div class="header">
                            <h4>ប្រាក់បៀវត្សន៏សរុប៖ ' . number_format($employee_data['Total_1']) . '&nbsp;$</h4>
                        </div>
                        <table class="table table-striped ot-table">
                            <tbody>
                                <tr><td></td><td>អវត្តមាន៖</td><td></td><td></td><td></td><td></td></tr>
                                <tr><td></td><td>មាន&nbsp;ច្បាប់៖&nbsp;' . number_format($employee_data['Abs_(Day)']) . '&nbsp;ថ្ងៃ&nbsp;' . number_format($employee_data['Abs_(Hour)']) . '&nbsp;ម៉ោង</td><td></td><td></td><td></td><td></td></tr>
                                <tr><td></td><td>គ្មានច្បាប់៖&nbsp;' . number_format($employee_data['Abs_(Unpaid)']) . '&nbsp;ម៉ោង</td><td></td><td></td><td><span class="fon">ចំនួនទឹកប្រាក់៖</span></td><td>' . number_format($employee_data['Abs_Amount']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td><span class="fon">ប្រាក់ឧបត្ថម្ភចូលឆ្នាំខ្មែរ&nbsp;៖</span></td><td>' . number_format($employee_data['Alw_KHNY']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td><span class="fon">ប្រាក់បៀវត្សន៏&nbsp;លើកទី&nbsp;១&nbsp;៖</span></td><td>' . number_format($employee_data['Advance']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td><span class="fon">ការផាកពិន័យផ្សេងៗ&nbsp;៖</span></td><td>' . number_format($employee_data['Deduct']) . '&nbsp;$</td></tr>
                                <tr><td></td><td></td><td></td><td></td><td><span class="fon">ភាគទានសោធន&nbsp;៖</span></td><td>' . number_format($employee_data['Pension']) . '&nbsp;$</td></tr>
                            </tbody>
                        </table>
                        <div class="header">
                            <h4>ប្រាក់បៀវត្សន៏&nbsp;លើកទី&nbsp;២៖ ' . number_format($employee_data['Total_2']) . '&nbsp;$</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        // Write CSS and HTML to mPDF
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        // Generate filename with Emp_ID
        $filename = 'salary_slip_' . ($employee_data['Emp_ID'] ?? 'unknown') . '.pdf';

        // Output as download
        $mpdf->Output($filename, 'D'); // 'D' = Download

    } catch (\Mpdf\MpdfException $e) {
        // Handle mPDF errors
        error_log('mPDF Error: ' . $e->getMessage());
        http_response_code(500);
        echo '<div class="alert alert-danger">PDF Generation Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        exit;
    }
}
?>