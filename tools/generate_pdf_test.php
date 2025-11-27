<?php
// tools/generate_pdf_test.php
// Standalone test to generate a filled PDF using FPDI without routing through CI controllers.
require_once __DIR__ . '/../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

$sigDataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==';
$post = [
    'clientFirstName' => 'Standalone',
    'clientLastName' => 'Runner',
    'streetAddress' => '123 Test St',
    'city' => 'Testville',
    'postalZip' => '12345',
    'termOfContract' => '12 months',
    'startDate' => date('Y-m-d'),
    'monthlyPayment' => '1000'
];

// Decode signature
$sigBase64 = preg_replace('#^data:image/[^;]+;base64,#i', '', $sigDataUrl);
$sigBytes = base64_decode($sigBase64);
if ($sigBytes === false) {
    echo "Invalid signature base64\n";
    exit(1);
}

$sigDir = __DIR__ . '/../writable/contracts/signatures/';
if (!is_dir($sigDir)) mkdir($sigDir, 0755, true);
$sigFilename = 'sig_test_' . time() . '.png';
$sigPath = $sigDir . $sigFilename;
file_put_contents($sigPath, $sigBytes);

$template = __DIR__ . '/../public/assets/PDFContract/Contract-Agreement.pdf';
if (!file_exists($template)) {
    echo "Template not found: $template\n";
    exit(1);
}

try {
    $pdf = new Fpdi();
    $pageCount = $pdf->setSourceFile($template);
    $tpl = $pdf->importPage(1);
    $size = $pdf->getTemplateSize($tpl);
    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($tpl);

    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(0,0,0);

    $pdf->SetXY(30, 60);
    $pdf->Write(0, trim($post['clientFirstName'] . ' ' . $post['clientLastName']));
    $pdf->SetXY(30, 80);
    $pdf->Write(0, $post['streetAddress'] . ', ' . $post['city'] . ' ' . $post['postalZip']);
    $pdf->SetXY(30, 95);
    $pdf->Write(0, $post['termOfContract'] . ' starting ' . $post['startDate']);
    $pdf->SetXY(140, 120);
    $pdf->Write(0, '₱' . $post['monthlyPayment']);

    // place signature image
    $sigX = 30; $sigY = 200; $sigW = 50;
    $pdf->Image($sigPath, $sigX, $sigY, $sigW, 0, 'PNG');

    $outDir = __DIR__ . '/../writable/contracts/';
    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
    $outFilename = 'contract_test_' . time() . '.pdf';
    $outPath = $outDir . $outFilename;
    $pdf->Output($outPath, 'F');

    echo "Generated PDF: " . $outPath . "\n";
    echo "Saved signature: " . $sigPath . "\n";
    exit(0);
} catch (Throwable $e) {
    echo "FPDI error: " . $e->getMessage() . "\n";
    exit(1);
}

?>