<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Core\Response;

/**
 * AuthMiddleware — Blocks unauthenticated users.
 * 
 * If the user is not logged in, redirects to the login page.
 * For AJAX requests, returns a 401 JSON response instead.
 */
class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        if (!Session::isLoggedIn()) {
            if ($request->isAjax()) {
                Response::json(['error' => 'Unauthorized. Please login.'], 401);
            }
            Session::flash('error', 'Silakan login terlebih dahulu.');
            Response::redirect(url('login'));
            return false;
        }

        return true;
    }
}
