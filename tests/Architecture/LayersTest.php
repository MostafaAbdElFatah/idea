<?php

declare(strict_types=1);

arch('models do not depend on controllers')
    ->expect('App\Models')->not->toUse('App\Http\Controllers')->group('architecture', 'models');

arch('models do not depend on requests or policies')
    ->expect('App\Models')->not->toUse(['App\Http\Requests', 'App\Policies'])->group('architecture', 'models');

arch('http layer is not used outside http')
    ->expect('App\Http')->toOnlyBeUsedIn(['App\Http', 'App\Providers'])->group('architecture');

arch('application contains no debug calls')
    ->expect(['dd', 'dump', 'var_dump', 'ray', 'ddd'])->not->toBeUsed()->group('architecture');

arch('application does not call env outside configuration')
    ->expect('App')->not->toUse('env')->group('architecture');

arch('all app code uses strict types')
    ->expect('App')->toUseStrictTypes()->group('architecture');

arch('enums are backed and live in the Enums namespace')
    ->expect('App\Enums')->toBeEnums()->toBeStringBackedEnums()->group('architecture', 'models');
