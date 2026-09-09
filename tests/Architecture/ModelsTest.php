<?php

declare(strict_types=1);
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

arch('models extend Eloquent')
    ->expect('App\Models')->toExtend(Model::class)
    ->group('architecture', 'models');

arch('models use factories')
    ->expect('App\Models')->toUseTrait(HasFactory::class)
    ->group('architecture', 'models');

arch('models do not use facades DB or Request')
    ->expect('App\Models')->not->toUse([DB::class, Request::class])
    ->group('architecture', 'models');

arch('models are only used by the application layers that own them')
    ->expect('App\Models')->toOnlyBeUsedIn(['App\Models', 'App\Http', 'App\Policies', 'App\Providers', 'Database'])
    ->group('architecture', 'models');
