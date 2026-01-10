<?php

namespace LaraModule\Core\Middleware;

use Illuminate\Http\Middleware\TrustProxies as BaseTrustProxies;
use Illuminate\Http\Request;

class TrustProxies extends BaseTrustProxies
{
    protected $proxies = '*';

    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
