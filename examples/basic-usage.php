<?php

declare(strict_types=1);

/**
 * Example: Using Simple_Circuit_Breaker to protect an HTTP call.
 *
 * Simple_Circuit_Breaker uses in-memory storage — it resets each request.
 * Use Advanced_Circuit_Breaker with Symfony_Cache for persistent state.
 */

use PrestaShop\CircuitBreaker\Factory_Settings;
use PrestaShop\CircuitBreaker\Simple_Circuit_Breaker_Factory;

require_once __DIR__ . '/../vendor/autoload.php';

// --- Settings ---
// Allow 3 failures before opening, 2-second timeout per call,
// wait 10 seconds before attempting half-open probe.
$settings = new Factory_Settings(
    failures: 3,
    timeout: 2.0,
    threshold: 10
);

$factory = new Simple_Circuit_Breaker_Factory();
$breaker = $factory->create($settings);

$service_url = 'https://api.example.com/v1/products';

$fallback = static fn (): string => '{"products":[]}';

// --- Call through the circuit breaker ---
$response = $breaker->call(
    $service_url,
    [],          // query parameters
    $fallback    // used when circuit is open or retries exhausted
);

echo $response . PHP_EOL;
