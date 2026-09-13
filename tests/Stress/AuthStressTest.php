<?php

declare(strict_types=1);

use function Pest\Stressless\stress;

/**
 * Thresholds for the public auth pages
 *   failed requests : 0
 *   median duration : < 200 ms
 *   p95 duration    : < 500 ms
 *
 * Runs only via `composer test:stress` against a dedicated, seeded environment
 * whose base URL is given by STRESS_BASE_URL. Requires k6 on the machine.
 */
$baseUrl = getenv('STRESS_BASE_URL');

it('serves the login page under 50 concurrent users', function () use ($baseUrl): void {
    $result = stress($baseUrl.'/login')
        ->concurrently(50)
        ->for(10)->seconds();

    expect($result->requests()->failed()->count())->toBe(0)
        ->and($result->requests()->duration()->med())->toBeLessThan(200)
        ->and($result->requests()->duration()->p95())->toBeLessThan(500);
})->group('stress', 'controllers')
    ->skip($baseUrl === false || $baseUrl === '', 'Set STRESS_BASE_URL to run stress tests.');

it('serves the register page under 50 concurrent users', function () use ($baseUrl): void {
    $result = stress($baseUrl.'/register')
        ->concurrently(50)
        ->for(10)->seconds();

    expect($result->requests()->failed()->count())->toBe(0)
        ->and($result->requests()->duration()->med())->toBeLessThan(200)
        ->and($result->requests()->duration()->p95())->toBeLessThan(500);
})->group('stress', 'controllers')
    ->skip($baseUrl === false || $baseUrl === '', 'Set STRESS_BASE_URL to run stress tests.');
