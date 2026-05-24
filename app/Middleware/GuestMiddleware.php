<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Core\Response;

/**
 * GuestMiddleware — Blocks authenticated users.
 * 
 * If the user is already logged in, redirects them to the home page.
 * Used on login/signup pages to prevent already-logged-in users from accessing them.
 */
class GuestMiddleware
{
    public function handle(Request $request): bool
    {
        if (Session::isLoggedIn()) {
            Response::redirect(url('/'));
            return false;
        }

        return true;
    }
}
