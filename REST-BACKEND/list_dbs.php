<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbs = DB::select("SELECT datname FROM pg_database WHERE datname NOT LIKE 'template%' AND datname != 'postgres'");
foreach ($dbs as $db) {
    echo $db->datname . PHP_EOL;
}
