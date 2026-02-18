<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    // Add URIs to exclude from CSRF protection if needed
    protected $except = [
        //
    ];
}
