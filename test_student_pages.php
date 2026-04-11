<?php
/**
 * Test student pages to verify they load with proper layout
 */

$pages = [
    '/student/leaves',
    '/student/tasks', 
    '/student/announcements',
    '/student/reports'
];

echo "Testing Student Pages Layout Fixes\n";
echo "===================================\n\n";

foreach ($pages as $page) {
    $url = 'http://localhost' . $page;
    $response = @file_get_contents($url);
    
    if ($response !== false) {
        $hasSidebar = strpos($response, 'student-sidebar') !== false ? 'YES' : 'NO';
        $hasHeader = strpos($response, '<header') !== false ? 'YES' : 'NO';
        $status = 'LOADED';
        
        echo "$page:\n";
        echo "  Status: $status\n";
        echo "  Has Sidebar: $hasSidebar\n";
        echo "  Has Header: $hasHeader\n\n";
    } else {
        echo "$page:\n";
        echo "  Status: ERROR - Could not load\n\n";
    }
}

echo "\nTest Complete!\n";
?>
