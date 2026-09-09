<?php

declare(strict_types=1);
use Illuminate\Foundation\Http\FormRequest;

arch('form requests extend FormRequest and expose rules')
    ->expect('App\Http\Requests')
    ->toExtend(FormRequest::class)
    ->toHaveSuffix('Request')
    ->toHaveMethod('rules')
    ->toHaveMethod('authorize')
    ->group('architecture', 'requests');
