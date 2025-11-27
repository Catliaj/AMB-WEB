<?php
// tools/run_fill_debug.php
// CLI helper to simulate POST /debug/fillPdfNoAuth and capture output
// Run: php tools/run_fill_debug.php

// Minimal POST payload
$_POST = [
    'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==',
    'clientFirstName' => 'CLI',
    'clientLastName' => 'Tester',
    'streetAddress' => '1 CLI St',
    'city' => 'CLICity',
    'postalZip' => '00000',
    'termOfContract' => '12 months',
    'startDate' => date('Y-m-d'),
    'monthlyPayment' => '5000'
];

// Simulate server variables for a POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
// Some setups require index.php in the request URI; try both variants via rewrite.
$_SERVER['REQUEST_URI'] = '/index.php/debug/fillPdfNoAuth';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PATH_INFO'] = '/debug/fillPdfNoAuth';

// Ensure constants and paths are resolved by public/index.php
// Bootstrap CodeIgniter for console context so Services are available
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/Paths.php';
// Ensure environment constant exists for console bootstrap
if (! defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}
$paths = new \Config\Paths();
// Define FCPATH so core helpers referencing it don't error when booting from CLI
if (! defined('FCPATH')) {
    define('FCPATH', realpath(__DIR__ . '/../public') . DIRECTORY_SEPARATOR);
}
\CodeIgniter\Boot::bootConsole($paths);

// Instantiate controller and call the no-auth debug method
// Properly initialize controller with request/response/logger
$controller = new \App\Controllers\UserController();
// Force an HTTP-style request so getPost() reads from PHP globals
\Config\Services::createRequest(new \Config\App(), false);
$request = service('request');
$response = service('response');
$logger = service('logger');
if (method_exists($controller, 'initController')) {
    $controller->initController($request, $response, $logger);
}

$result = $controller->fillTemplatePdfNoAuth();

// If the controller returned a ResponseInterface, output its body
$output = '';
if (is_object($result) && method_exists($result, 'getBody')) {
    $output = (string) $result->getBody();
} elseif (is_array($result) || is_string($result)) {
    $output = json_encode($result);
}

// Print result and list files
echo "--- CONTROLLER OUTPUT ---\n";
echo $output . "\n";

// List generated files
$contractsDir = __DIR__ . '/../writable/contracts';
$sigsDir = $contractsDir . '/signatures';

echo "--- contracts dir: " . $contractsDir . " ---\n";
if (is_dir($contractsDir)) {
    $files = array_values(array_filter(scandir($contractsDir), function($f){ return !in_array($f, ['.', '..']); }));
    foreach ($files as $f) {
        echo $f . "\n";
    }
} else {
    echo "(missing)\n";
}

echo "--- signatures dir: " . $sigsDir . " ---\n";
if (is_dir($sigsDir)) {
    $files = array_values(array_filter(scandir($sigsDir), function($f){ return !in_array($f, ['.', '..']); }));
    foreach ($files as $f) {
        echo $f . "\n";
    }
} else {
    echo "(missing)\n";
}


?>