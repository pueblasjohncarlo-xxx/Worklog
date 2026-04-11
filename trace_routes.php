<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========== STUDENT MODULE ROUTE TRACE ==========\n\n";

// Get all routes
$routes = \Illuminate\Support\Facades\Route::getRoutes();

echo "STUDENT ROUTES REGISTERED:\n";
echo str_repeat("=", 100) . "\n";

foreach ($routes as $route) {
    $uri = $route->uri;
    if (strpos($uri, 'student') !== false) {
        $methods = implode('|', $route->methods);
        $action = $route->action['controller'] ?? 'Closure';
        $name = $route->name ?? 'unnamed';
        
        echo "$methods  | $uri\n";
        echo "    ├─ Name: $name\n";
        echo "    └─ Controller: $action\n\n";
    }
}

echo "\nSTUDENT CONTROLLERS IN FOLDER:\n";
echo str_repeat("=", 100) . "\n";

$studentDir = 'app/Http/Controllers/Student';
if (is_dir($studentDir)) {
    $files = glob($studentDir . '/*.php');
    foreach ($files as $file) {
        $className = basename($file, '.php');
        echo "  • $className\n";
    }
}

echo "\nMAIN STUDENT CONTROLLER:\n";
echo str_repeat("=", 100) . "\n";
echo "  • StudentController (app/Http/Controllers/StudentController.php)\n";

echo "\nVIEW FILES:\n";
echo str_repeat("=", 100) . "\n";

$viewDirs = [
    'resources/views/dashboards/student.blade.php',
    'resources/views/student/tasks/index.blade.php',
    'resources/views/student/leaves/index.blade.php',
    'resources/views/student/journal/index.blade.php',
    'resources/views/student/reports/index.blade.php',
    'resources/views/student/announcements/index.blade.php',
    'resources/views/student/worklogs/create.blade.php',
];

foreach ($viewDirs as $view) {
    $exists = file_exists($view) ? '✓' : '✗';
    echo "  $exists $view\n";
}

echo "\nLAYOUT FILES:\n";
echo str_repeat("=", 100) . "\n";
$layouts = [
    'resources/views/layouts/student-layout.blade.php',
    'resources/views/layouts/student-sidebar.blade.php',
    'app/View/Components/StudentLayout.php',
];

foreach ($layouts as $layout) {
    $exists = file_exists($layout) ? '✓' : '✗';
    echo "  $exists $layout\n";
}

echo "\n✓ Route and file inventory complete\n";
