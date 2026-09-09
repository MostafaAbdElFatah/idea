<?php

declare(strict_types=1);

arch('policies are suffixed and only used by the auth layer')
    ->expect('App\Policies')->toHaveSuffix('Policy')
    ->toOnlyBeUsedIn(['App\Providers', 'App\Policies'])
    ->group('architecture', 'policies');

arch('policies expose the full ability set')
    ->expect('App\Policies')
    ->toHaveMethods(['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'])
    ->group('architecture', 'policies');
