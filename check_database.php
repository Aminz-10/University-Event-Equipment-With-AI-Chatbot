<?php
/**
 * Check Database Tables and Structure
 */
session_start();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Check</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-top: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Database Structure Check</h1>";

include "db.php";

if (!isset($connect)) {
    echo "<div class='status error'>❌ Database connection failed!</div>";
    echo "</div></body></html>";
    exit();
}

echo "<div class='status success'>✅ Database connected</div>";

// Get all tables
echo "<h2>Tables in Database</h2>";
$result = $connect->query("SHOW TABLES");
$tables = [];

if ($result) {
    echo "<table>";
    echo "<tr><th>Table Name</th><th>Rows</th></tr>";
    
    while ($row = $result->fetch_array()) {
        $table_name = $row[0];
        $tables[] = $table_name;
        
        // Count rows
        $count_result = $connect->query("SELECT COUNT(*) as cnt FROM `$table_name`");
        $count_row = $count_result->fetch_assoc();
        $row_count = $count_row['cnt'];
        
        echo "<tr><td><strong>$table_name</strong></td><td>$row_count rows</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div class='status error'>❌ Could not fetch tables: " . $connect->error . "</div>";
}

// Check specific tables needed for chatbot
echo "<h2>Required Tables Status</h2>";
$required_tables = ['equipment', 'booking', 'booking_equipment', 'user'];

foreach ($required_tables as $table) {
    if (in_array($table, $tables)) {
        echo "<div class='status success'>✅ $table exists</div>";
        
        // Show columns
        $col_result = $connect->query("SHOW COLUMNS FROM `$table`");
        echo "<p><strong>Columns in $table:</strong></p>";
        echo "<table>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($col_row = $col_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><code>" . $col_row['Field'] . "</code></td>";
            echo "<td>" . $col_row['Type'] . "</td>";
            echo "<td>" . ($col_row['Null'] == 'YES' ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($col_row['Key'] ?: 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        $sample = $connect->query("SELECT * FROM `$table` LIMIT 3");
        if ($sample && $sample->num_rows > 0) {
            echo "<p><strong>Sample data from $table:</strong></p>";
            $fields = $sample->fetch_fields();
            echo "<table>";
            echo "<tr>";
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";
            
            $sample = $connect->query("SELECT * FROM `$table` LIMIT 3");
            while ($data_row = $sample->fetch_assoc()) {
                echo "<tr>";
                foreach ($data_row as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<div class='status error'>❌ $table does NOT exist</div>";
    }
}

echo "</div></body></html>";
?>
