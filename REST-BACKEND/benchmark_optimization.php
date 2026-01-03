<?php

$url = "http://localhost:8000/offer/gourl/5/18/?&tid=test_bench&eai=rgoyal";
$iterations = 5;
$totalTime = 0;

echo "Benchmarking URL: $url\n";
echo "Iterations: $iterations\n\n";

for ($i = 0; $i < $iterations; $i++) {
    $start = microtime(true);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true); // We just want the headers/redirect info
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow, just measure the first hit
    curl_setopt($ch, CURLOPT_USERAGENT, 'BenchmarkBot/1.0');

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    $end = microtime(true);
    $duration = $end - $start;
    $totalTime += $duration;

    echo "Iteration " . ($i + 1) . ": " . number_format($duration, 4) . "s (HTTP Code: " . $info['http_code'] . ")\n";

    // Small sleep to avoid rate limiting if any
    usleep(100000);
}

$avg = $totalTime / $iterations;
echo "\nAverage Response Time: " . number_format($avg, 4) . "s\n";
