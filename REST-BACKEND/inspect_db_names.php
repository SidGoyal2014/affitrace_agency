<?php
echo "################################################\n";
echo "INSPECTING DB NAMES\n";
echo "################################################\n";

try {
    $host = '82.29.160.75';
    $db = 'affitrace';
    $user = 'root3';
    $pass = 'Test#123??';
    $port = 5432;
    $sslmode = 'disable';

    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    echo "Connected to Main DB.\n";

    // Get last 5 users and their tenant DBs
    // Get last 5 users and their tenant DBs
    echo "Databases found:\n";
    $stmt = $pdo->query("SELECT datname FROM pg_database WHERE datname LIKE 'affitrace%' OR datname = 'affitrace'");
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($dbs);

} catch (\Exception $e) {
    echo "FAILURE: " . $e->getMessage() . "\n";
}
echo "################################################\n";
