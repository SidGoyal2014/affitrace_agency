<?php
echo "################################################\n";
echo "STARTING CONNECTION TEST\n";
echo "################################################\n";

try {
    $host = '82.29.160.75';
    $db = 'affitrace';
    $user = 'root3';
    $pass = 'Test#123??';
    $port = 5432;
    $sslmode = 'disable';

    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode";

    echo "DSN: $dsn\n";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    echo "SUCCESS: Connected to PDO!\n";

    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "PG VERSION: $version\n";

} catch (\Exception $e) {
    echo "FAILURE: " . $e->getMessage() . "\n";
}
echo "################################################\n";
