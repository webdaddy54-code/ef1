<?php
/**
 * EnterF1.com — SQLite to MySQL Data Migration Script
 * 
 * Usage: php migrate-sqlite-to-mysql.php
 * 
 * Prerequisites:
 *   1. MySQL database created on Cloudways
 *   2. schema-mysql.sql already run (tables exist)
 *   3. config-mysql.php filled in with MySQL credentials (or edit below)
 *   4. database.sqlite in the same directory as this script
 *
 * What it does:
 *   - Reads all data from every SQLite table
 *   - Inserts it into the equivalent MySQL table
 *   - Reports row counts per table
 *   - Handles the race_results table (no PK in SQLite, adds one in MySQL)
 *   - Handles the tickets table (text 'Id' column dropped, uses auto-increment)
 *
 * IMPORTANT: Run this ONCE on the server (or locally with remote MySQL access).
 *            Do not run twice — it does not truncate tables first.
 */

// ------------------------------------------------------------------
// Configuration
// ------------------------------------------------------------------

$sqliteFile = __DIR__ . '/database.sqlite';

$mysql = [
    'host'    => 'localhost',
    'dbname'  => 'YOUR_DATABASE_NAME',
    'user'    => 'YOUR_USERNAME',
    'pass'    => 'YOUR_PASSWORD',
    'charset' => 'utf8mb4',
];

// ------------------------------------------------------------------
// Connect to both databases
// ------------------------------------------------------------------

echo "EnterF1.com — SQLite → MySQL Migration\n";
echo str_repeat('=', 50) . "\n\n";

// SQLite
try {
    $sqlite = new PDO("sqlite:" . $sqliteFile);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "✅ SQLite connected: {$sqliteFile}\n";
} catch (PDOException $e) {
    die("❌ SQLite connection failed: " . $e->getMessage() . "\n");
}

// MySQL
try {
    $dsn = "mysql:host={$mysql['host']};dbname={$mysql['dbname']};charset={$mysql['charset']}";
    $mysqlPdo = new PDO($dsn, $mysql['user'], $mysql['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    echo "✅ MySQL connected: {$mysql['host']}/{$mysql['dbname']}\n\n";
} catch (PDOException $e) {
    die("❌ MySQL connection failed: " . $e->getMessage() . "\n");
}

// ------------------------------------------------------------------
// Tables to migrate (in dependency order — parents before children)
// ------------------------------------------------------------------

$tables = [
    'drivers',
    'races',
    'teams',
    'race_results',
    'race_points',
    'tickets',
    'ticket_providers',
    'race_ticket_providers',
    'seating_guides',
    'grandstands',
    'tv_schedule',
    'site_settings',
];

$totalRows = 0;
$errors = 0;

foreach ($tables as $table) {
    echo "Migrating: {$table}... ";
    
    try {
        // Read all from SQLite
        $stmt = $sqlite->query("SELECT * FROM {$table}");
        $rows = $stmt->fetchAll();
        
        if (empty($rows)) {
            echo "0 rows (empty, skipping)\n";
            continue;
        }
        
        // Build INSERT statement
        $columns = array_keys($rows[0]);
        
        // Handle tables where MySQL schema differs from SQLite
        if ($table === 'race_results') {
            // SQLite has no 'id' column; MySQL adds one as AUTO_INCREMENT
            // Remove 'id' from columns if it exists in SQLite data
            $columns = array_filter($columns, fn($c) => $c !== 'id');
            $columns = array_values($columns);
        }
        
        if ($table === 'tickets') {
            // SQLite has 'Id' as TEXT; MySQL uses auto-increment 'id'
            $columns = array_filter($columns, fn($c) => strtolower($c) !== 'id');
            $columns = array_values($columns);
        }
        
        $columnList = implode(', ', array_map(fn($c) => "`{$c}`", $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        
        $insertSql = "INSERT INTO {$table} ({$columnList}) VALUES ({$placeholders})";
        $insertStmt = $mysqlPdo->prepare($insertSql);
        
        $count = 0;
        $mysqlPdo->beginTransaction();
        
        foreach ($rows as $row) {
            $values = [];
            foreach ($columns as $col) {
                $values[] = $row[$col] ?? null;
            }
            $insertStmt->execute($values);
            $count++;
        }
        
        $mysqlPdo->commit();
        $totalRows += $count;
        echo "{$count} rows ✅\n";
        
    } catch (Exception $e) {
        if ($mysqlPdo->inTransaction()) {
            $mysqlPdo->rollBack();
        }
        $errors++;
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "Migration complete: {$totalRows} total rows migrated";
if ($errors > 0) {
    echo " ({$errors} errors)";
}
echo "\n";

// ------------------------------------------------------------------
// Post-migration: SQL syntax changes needed in PHP files
// ------------------------------------------------------------------

echo "\n📋 REMINDER — Files that need DATE('now') → CURDATE() for MySQL:\n";
echo "   1. config.php (line ~39) — getNextRace()\n";
echo "   2. where-to-buy-f1-tickets.php (line ~343)\n";
echo "   3. races/race.php (line ~676)\n";
echo "\n   If you're using config-mysql.php, change #1 is already done.\n";
echo "   You still need to manually update files #2 and #3.\n\n";
