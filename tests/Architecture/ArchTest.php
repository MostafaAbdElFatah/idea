<?php

arch('models do not depend on controllers')
    ->expect('App\\Models')
    ->not->toUse('App\\Http\\Controllers')
    ->group('architecture');

arch('controllers extend the application controller')
    ->expect('App\\Http\\Controllers')
    ->toExtend('App\\Http\\Controllers\\Controller')
    ->group('architecture');

arch('application contains no debug calls')
    ->expect(['dd', 'dump', 'var_dump', 'ray'])
    ->not->toBeUsed()
    ->group('architecture');

arch('application does not call env outside configuration')
    ->expect('App')
    ->not->toUse('env')
    ->group('architecture');
