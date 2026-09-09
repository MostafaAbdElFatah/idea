<?php

declare(strict_types=1);

/**
 * Thresholds for GET /
 *   failed requests : 0
 *   median duration : < 200 ms
 *   p95 duration    : < 500 ms
 *
 * Runs only via `composer test:stress` against a dedicated, seeded environment
 * whose base URL is given by STRESS_BASE_URL. Requires k6 on the machine.
 */
$baseUrl = getenv('STRESS_BASE_URL');

it('serves the home page under 50 concurrent users', function () use ($baseUrl): void {
    $result = stress($baseUrl.'/')
        ->concurrently(50)
        ->for(10)->seconds();

    expect($result->requests()->failed()->count())->toBe(0)
        ->and($result->requests()->duration()->med())->toBeLessThan(200)
        ->and($result->requests()->duration()->p95())->toBeLessThan(500);
})->group('stress', 'controllers')
    ->skip($baseUrl === false || $baseUrl === '', 'Set STRESS_BASE_URL to run stress tests.');

it('returns 404 quickly for unknown paths under load', function () use ($baseUrl): void {
    $result = stress($baseUrl.'/does-not-exist')
        ->concurrently(20)
        ->for(5)->seconds();

    expect($result->requests()->duration()->p95())->toBeLessThan(500);
})->group('stress', 'controllers')
    ->skip($baseUrl === false || $baseUrl === '', 'Set STRESS_BASE_URL to run stress tests.');
