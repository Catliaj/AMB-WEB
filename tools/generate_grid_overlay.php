<?php
// tools/generate_grid_overlay.php
// Draw a 5mm grid overlay on the existing Contract-Agreement.pdf template
require_once __DIR__ . '/../vendor/autoload.php';
use setasign\Fpdi\Fpdi;

$template = __DIR__ . '/../public/assets/PDFContract/Contract-Agreement.pdf';
if (!file_exists($template)) {
    echo "Template not found: $template\n";
    exit(1);
}

try {
    $pdf = new Fpdi();
    $tplIdx = $pdf->setSourceFile($template);
    $tpl = $pdf->importPage(1);
    $size = $pdf->getTemplateSize($tpl);
    $width = $size['width'];
    $height = $size['height'];

    $pdf->AddPage($size['orientation'], [$width, $height]);
    $pdf->useTemplate($tpl);

    // Draw grid every 10 mm (also smaller ticks every 5 mm)
    $step = 10; // mm
    $tick = 5;  // small tick

    $pdf->SetDrawColor(200,200,200);
    $pdf->SetLineWidth(0.05);

    // Vertical lines
    for ($x = 0; $x <= $width; $x += $step) {
        $pdf->Line($x, 0, $x, $height);
        // label X at top
        $pdf->SetFont('Helvetica', '', 6);
        $pdf->SetTextColor(150, 0, 0);
        $pdf->SetXY($x + 1, 1);
        $pdf->Cell(0, 4, (string) round($x, 1));
    }

    // Horizontal lines
    for ($y = 0; $y <= $height; $y += $step) {
        $pdf->Line(0, $y, $width, $y);
        // label Y at left
        $pdf->SetFont('Helvetica', '', 6);
        $pdf->SetTextColor(0, 100, 0);
        $pdf->SetXY(1, $y + 1);
        $pdf->Cell(0, 4, (string) round($y, 1));
    }

    // smaller tick lines (every 5mm) using lighter color
    $pdf->SetDrawColor(230,230,230);
    for ($x = $tick; $x <= $width; $x += $tick) {
        if ($x % $step == 0) continue;
        $pdf->Line($x, 0, $x, $height);
    }
    for ($y = $tick; $y <= $height; $y += $tick) {
        if ($y % $step == 0) continue;
        $pdf->Line(0, $y, $width, $y);
    }

    // Save output
    $outDir = __DIR__ . '/../writable/contracts/';
    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
    $outPath = $outDir . 'grid-overlay.pdf';
    $pdf->Output($outPath, 'F');

    echo "Generated grid overlay: " . $outPath . "\n";
    exit(0);
} catch (Throwable $e) {
    echo "Error generating grid overlay: " . $e->getMessage() . "\n";
    exit(1);
}
