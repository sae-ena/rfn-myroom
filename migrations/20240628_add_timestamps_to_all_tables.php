<?php
// Migration: Add created_at and deleted_at to all tables if not present
// Usage: php migrations/20240628_add_timestamps_to_all_tables.php

require_once __DIR__ . '/../admin/dbConnect.php';

$dbNameResult = $conn->query("SELECT DATABASE()");
if (!$dbNameResult) {
    die("❌ Could not determine current database.\n");
}
$dbName = $dbNameResult->fetch_row()[0];
echo "✅ Connected to DB: $dbName\n";

// Get all table names
$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    echo "⏳ Found " . count($tables) . " tables.\n";
} else {
    die("❌ Failed to fetch tables: " . $conn->error . "\n");
}

foreach ($tables as $table) {
    echo "\n🔍 Checking table: `$table`\n";

    // Check for created_at
    $checkCreatedAt = $conn->prepare("
        SELECT COUNT(*) FROM information_schema.columns 
        WHERE table_schema = ? AND table_name = ? AND column_name = 'created_at'");
    $checkCreatedAt->bind_param("ss", $dbName, $table);
    $checkCreatedAt->execute();
    $checkCreatedAt->bind_result($createdAtExists);
    $checkCreatedAt->fetch();
    $checkCreatedAt->close();

    // Check for deleted_at
    $checkDeletedAt = $conn->prepare("
        SELECT COUNT(*) FROM information_schema.columns 
        WHERE table_schema = ? AND table_name = ? AND column_name = 'deleted_at'");
    $checkDeletedAt->bind_param("ss", $dbName, $table);
    $checkDeletedAt->execute();
    $checkDeletedAt->bind_result($deletedAtExists);
    $checkDeletedAt->fetch();
    $checkDeletedAt->close();

    // Build ALTER statement if needed
    $alterParts = [];
    if (!$createdAtExists) {
        $alterParts[] = "ADD `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP";
    }
    if (!$deletedAtExists) {
        $alterParts[] = "ADD `deleted_at` DATETIME NULL DEFAULT NULL";
    }

    if (!empty($alterParts)) {
        $alterSQL = "ALTER TABLE `$table` " . implode(", ", $alterParts);
        echo "🚧 Running: $alterSQL\n";

        if ($conn->query($alterSQL)) {
            echo "✅ `$table`: Columns added successfully.\n";
        } else {
            echo "❌ `$table`: Error - " . $conn->error . "\n";
        }
    } else {
        echo "ℹ️ `$table`: Both columns already exist.\n";
    }
}

$conn->close();
echo "\n✅ Migration complete.\n";
