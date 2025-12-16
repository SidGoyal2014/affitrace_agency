<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    echo "Laravel Booted Successfully.\n";

    if (trait_exists('App\Http\Traits\MultiTenantResolver')) {
        echo "Trait Exists.\n";
    } else {
        echo "Trait NOT Found.\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
