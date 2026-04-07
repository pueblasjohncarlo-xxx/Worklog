<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Dropping database worklog_system (if exists)...\n";
    $pdo->exec('DROP DATABASE IF EXISTS worklog_system');

    echo "Creating database worklog_system...\n";
    $pdo->exec('CREATE DATABASE worklog_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

    echo "Database created successfully.\n";
} catch (PDOException $e) {
    echo 'Error: '.$e->getMessage()."\n";
    exit(1);
}
