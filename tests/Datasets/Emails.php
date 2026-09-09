<?php

declare(strict_types=1);

dataset('invalid emails', [
    'empty' => [''],
    'not an email' => ['not-an-email'],
    'missing domain' => ['a@'],
    'too long' => [str_repeat('a', 256).'@x.com'],
]);
