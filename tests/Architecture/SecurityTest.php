<?php

declare(strict_types=1);

arch('no insecure functions')
    ->expect(['md5', 'sha1', 'eval', 'exec', 'shell_exec', 'system', 'passthru', 'unserialize', 'extract'])
    ->not->toBeUsed()->group('architecture');

arch('mass assignment is explicit')
    ->expect('App\Models')->not->toHaveProperty('guarded')
    ->group('architecture', 'models');
