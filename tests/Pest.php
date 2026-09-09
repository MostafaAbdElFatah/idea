<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->in('Unit');

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Browser');

pest()->group('architecture')->in('Architecture');
pest()->group('stress')->in('Stress');

foreach (glob(__DIR__.'/Helpers/*.php') ?: [] as $helper) {
    require_once $helper;
}
