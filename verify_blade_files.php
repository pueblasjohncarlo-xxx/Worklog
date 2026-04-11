<?php
$files = [
    'resources/views/student/leaves/index.blade.php',
    'resources/views/student/tasks/index.blade.php',
    'resources/views/student/announcements/index.blade.php',
    'resources/views/student/reports/index.blade.php'
];

echo "Verifying Blade File Structure\n";
echo "==============================\n\n";

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $startCount = substr_count($content, '<x-student-layout>');
        $endCount = substr_count($content, '</x-student-layout>');
        echo "$file:\n";
        echo "  Length: " . strlen($content) . " bytes\n";
        echo "  Opening tags: $startCount\n";
        echo "  Closing tags: $endCount\n";
        echo "  Status: " . ($startCount === $endCount ? "✓ OK" : "✗ MISMATCH") . "\n\n";
    } else {
        echo "$file: NOT FOUND\n\n";
    }
}
?>
