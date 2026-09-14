<?php

declare(strict_types=1);

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

arch('controllers extend the application controller')
    ->expect('App\Http\Controllers')->toExtend(Controller::class)
    ->ignoring(Controller::class)->group('architecture', 'controllers');

arch('controllers are suffixed')
    ->expect('App\Http\Controllers')->toHaveSuffix('Controller')->group('architecture', 'controllers');

arch('controllers do not query the database directly')
    ->expect('App\Http\Controllers')->not->toUse(DB::class)
    ->group('architecture', 'controllers');

arch('controllers validate through form requests')
    ->expect('App\Http\Controllers')->not->toUse(Validator::class)
    ->group('architecture', 'controllers');
