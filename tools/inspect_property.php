<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\PropertyModel;
use App\Models\PropertyImageModel;

// Simple CLI script: optionally pass property ID as first arg
$argv = $_SERVER['argv'];
$pid = isset($argv[1]) ? intval($argv[1]) : null;

$propertyModel = new PropertyModel();
$imageModel = new PropertyImageModel();

if ($pid) {
    $p = $propertyModel->find($pid);
    if (!$p) {
        echo "Property ID $pid not found\n";
        exit(1);
    }
    $images = $imageModel->where('PropertyID', $pid)->findAll();
    echo "Property: " . ($p['Title'] ?? $p['PropertyID'] ?? 'unknown') . " (ID: $pid)\n";
    echo "Images (" . count($images) . "):\n";
    foreach ($images as $img) {
        $fn = $img['Image'] ?? '';
        $file = __DIR__ . '/../public/uploads/properties/' . $fn;
        echo " - " . $fn . " => " . ($fn ? (file_exists($file) ? 'exists' : 'MISSING') : 'empty') . "\n";
    }
    exit(0);
}

// No PID provided: list up to 10 properties and first images
$props = $propertyModel->findAll(10);
if (!$props) {
    echo "No properties found\n";
    exit(1);
}
foreach ($props as $p) {
    $id = $p['PropertyID'];
    echo "Property ID: $id - " . ($p['Title'] ?? '') . "\n";
    $images = $imageModel->where('PropertyID', $id)->findAll();
    if (!$images) {
        echo "  Images: none\n";
    } else {
        foreach ($images as $img) {
            $fn = $img['Image'] ?? '';
            $file = __DIR__ . '/../public/uploads/properties/' . $fn;
            echo "  - $fn => " . ($fn ? (file_exists($file) ? 'exists' : 'MISSING') : 'empty') . "\n";
        }
    }
}

echo "Done.\n";
