<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Base Controller — All API controllers extend this class.
 *
 * Provides shared behaviour such as authorization checks.
 */
abstract class Controller
{
    use AuthorizesRequests;
}
