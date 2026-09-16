<?php

declare(strict_types=1);

use Illuminate\Auth\Access\Response;

/**
 * Normalise a policy result that may be a boolean or an authorization response.
 */
function allowed(bool|Response $result): bool
{
    return $result instanceof Response ? $result->allowed() : $result;
}
