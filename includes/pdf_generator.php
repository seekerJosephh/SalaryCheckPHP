<?php
// includes/pdf_generator.php
require_once __DIR__ . '/../vendor/autoload.php';
use Mpdf\Mpdf;

function generateSalaryPDF($employee_data) {
    // Define directories
    $tempDir = __DIR__ . '/../tmp';
    $fontDir = __DIR__ . '/../tmp/mpdf/ttfonts';

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

    // mPDF configuration
    $mpdfConfig = [
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P',
        'tempDir' => $tempDir,
        'fontDir' => [$fontDir],
        'fontdata' => [
            'battambang' => [
                'R' => 'KhmerOS.ttf',
                'B' => 'KhmerOS.ttf',
                'useOTL' => 0xFF,
            ],
            'khmermuol' => [
                'R' => 'KhmerOSmuol.ttf',
                'useOTL' => 0xFF,
            ],
            'notokhmer' => [
                'R' => 'NotoSansKhmer-Regular.ttf',
                'B' => 'NotoSansKhmer.ttf',
                'useOTL' => 0xFF,
            ],
        ],
        'default_font' => 'battambang',
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

        // CSS for styling
        $css = '
        body { font-family: "battambang", sans-serif; font-size: 10pt; color: #000; }
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

        // HTML content with updated keys
        $html = '
        <div class="container">
            <div class="card">
                <div class="header1">
                    <h4>ព័ត៌មានប្រាក់បៀវត្សន៏</h4>
                </div>
                <div class="card-body">
                    <div class="employee-info">
                        <div><strong>ល.អ.ត ៖</strong> ' . htmlspecialchars($employee_data['Emp_ID']) . '</div>
                        <div><strong>ឈ្មោះ ៖</strong> ' . htmlspecialchars($employee_data['KhmerName']) . '</div>
                        <div><strong>បៀវត្សន៏ខែ ៖</strong> ' . htmlspecialchars($employee_data['SalaryDTKH']) . '</div>
                    </div>
                    <div class="header">
                        <h4>ប្រាក់ខែគោល ៖ ' . number_format($employee_data['Basic']) . ' $</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped ot-table">
                            <tbody>
                                <tr><td colspan="2" class="fon">ថែមម៉ោង</td></tr>
                                <tr><td>ពេលថ្ងៃ</td><td>' . number_format($employee_data['Total_Normal_OT']) . '&nbsp;ម៉ោង</td><td>ចំនួនទឹកប្រាក់</td><td>' . number_format($employee_data['Normal_Amount']) . '&nbsp;$</td></tr>
                                <tr><td>ពេលយប់</td><td>' . number_format($employee_data['Aft_Night_OT']) . '&nbsp;ម៉ោង</td><td>ចំនួនទឹកប្រាក់</td><td>' . number_format($employee_data['OT_Aft_Night']) . '&nbsp;$</td></tr>
                                <tr><td>ថ្ងៃឈប់</td><td>' . number_format($employee_data['Holiday_Normal_OT']) . '&nbsp;ម៉ោង</td><td>ចំនួនទឹកប្រាក់</td><td>' . number_format($employee_data['Total_HOT']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់បន្ថែម&nbsp;វេនយប់</td><td>' . number_format($employee_data['Night_Wage']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់ឧបត្ថម្ភ&nbsp;វត្តមាន</td><td>' . number_format($employee_data['Alw_Att']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់ឧបត្ថម្ភ&nbsp;ការស្នាក់នៅ</td><td>' . number_format($employee_data['Alw_Housing']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់បន្ថែម ជីស្ដា(G-STARS)</td><td>' . number_format($employee_data['Alw_GSTARS']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់បន្ថែម License</td><td>' . number_format($employee_data['Alw_License']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់បន្ថែម&nbsp;មុខតំណែង</td><td>' . number_format($employee_data['Alw_Position']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់បន្ថែម ផ្សេងៗ</td><td>' . number_format($employee_data['Alw_Additional']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់អតីតភាពការងារ</td><td>' . number_format($employee_data['Seniority']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់លក់ថ្ងៃឈប់ប្រចាំឆ្នាំ</td><td>' . number_format($employee_data['SaleAL']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>កែសម្រួល</td><td>' . number_format($employee_data['Adjust']) . '&nbsp;$</td></tr>
                            </tbody>
                        </table>
                        <div class="header">
                            <h4>ប្រាក់បៀវត្សន៏សរុប៖ ' . number_format($employee_data['Total_1']) . '&nbsp;$</h4>
                        </div>
                        <table class="table table-striped ot-table">
                            <tbody>
                                <tr><td colspan="2" class="fon">អវត្តមាន</td></tr>
                                <tr><td colspan="2">មាន&nbsp;ច្បាប់&nbsp;' . number_format($employee_data['Abs_Day']) . '&nbsp;ថ្ងៃ&nbsp;' . number_format($employee_data['Abs_Hour']) . '&nbsp;ម៉ោង</td><td></td><td></td></tr>
                                <tr><td colspan="2">គ្មានច្បាប់&nbsp;' . number_format($employee_data['Abs_Unpaid']) . '&nbsp;ម៉ោង</td><td>ចំនួនទឹកប្រាក់</td><td>' . number_format($employee_data['Abs_Amount']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់ឧបត្ថម្ភចូលឆ្នាំខ្មែរ</td><td>' . number_format($employee_data['Alw_KHNY']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ប្រាក់បៀវត្សន៏&nbsp;លើកទី&nbsp;១</td><td>' . number_format($employee_data['Advance']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ការផាកពិន័យផ្សេងៗ</td><td>' . number_format($employee_data['Deduct']) . '&nbsp;$</td></tr>
                                <tr><td colspan="2"></td><td>ភាគទានសោធន</td><td>' . number_format($employee_data['Pension']) . '&nbsp;$</td></tr>
                            </tbody>
                        </table>
                        <div class="header">
                            <h4>ប្រាក់បៀវត្សន៏&nbsp;លើកទី&nbsp;២៖ ' . number_format($employee_data['Total_2']) . '&nbsp;$</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        // Write CSS and HTML
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        // Generate filename
        $filename = 'salary_slip_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', ($employee_data['Emp_ID'] ?? 'unknown')) . '.pdf';

        // Output PDF
        $mpdf->Output($filename, 'D');

    } catch (\Mpdf\MpdfException $e) {
        error_log('mPDF Error: ' . $e->getMessage());
        http_response_code(500);
        echo '<div class="alert alert-danger">PDF Generation Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        exit;
    }
}
?>