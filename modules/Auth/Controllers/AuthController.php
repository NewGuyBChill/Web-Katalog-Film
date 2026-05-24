<?php

namespace Modules\Auth\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;

/**
 * AuthController — Handles login, signup, and logout.
 */
class AuthController extends Controller
{
    /**
     * GET /login — Show login page.
     */
    public function showLogin(Request $request): void
    {
        $this->view('layouts.app', [
            'content' => $this->capture('auth.login'),
            'title' => 'Login — CelesView',
        ]);
    }

    /**
     * POST /login — Process login.
     */
    public function login(Request $request): void
    {
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email dan password wajib diisi.');
            Response::redirect(url('login'));
            return;
        }

        $user = $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::flash('error', 'Email atau password salah.');
            Response::redirect(url('login'));
            return;
        }

        // Set session
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('user_email', $user['email']);
        Session::set('avatar_color', $user['avatar_color'] ?? '#00d2ff');

        Response::redirect(url('/'));
    }

    /**
     * GET /signup — Show signup page.
     */
    public function showSignup(Request $request): void
    {
        $this->view('layouts.app', [
            'content' => $this->capture('auth.signup'),
            'title' => 'Sign Up — CelesView',
        ]);
    }

    /**
     * POST /signup — Process registration.
     */
    public function register(Request $request): void
    {
        $name     = trim($request->input('name', ''));
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');

        // Basic validation
        if (empty($name) || empty($email) || empty($password)) {
            Session::flash('error', 'Semua field wajib diisi.');
            Response::redirect(url('signup'));
            return;
        }

        if (strlen($password) < 6) {
            Session::flash('error', 'Password minimal 6 karakter.');
            Response::redirect(url('signup'));
            return;
        }

        // Check duplicate email
        $existing = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            Session::flash('error', 'Email sudah terdaftar.');
            Response::redirect(url('signup'));
            return;
        }

        // Generate random avatar color
        $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8', '#FF7675', '#74B9FF', '#A29BFE'];
        $avatarColor = $colors[array_rand($colors)];

        // Insert user
        $userId = $this->db->insert('users', [
            'username'     => $name,
            'email'        => $email,
            'password'     => password_hash($password, PASSWORD_DEFAULT),
            'avatar_color' => $avatarColor,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        // Auto-login
        Session::regenerate();
        Session::set('user_id', $userId);
        Session::set('username', $name);
        Session::set('user_email', $email);
        Session::set('avatar_color', $avatarColor);

        Response::redirect(url('/'));
    }

    /**
     * GET /logout — Destroy session and redirect.
     */
    public function logout(Request $request): void
    {
        Session::destroy();
        Response::redirect(url('/'));
    }

    /**
     * Capture a view to string.
     */
    private function capture(string $view, array $data = []): string
    {
        extract($data);
        $parts = explode('.', $view);
        $module = ucfirst($parts[0]);
        $file = $parts[1] ?? 'index';
        $path = base_path("modules/{$module}/Views/{$file}.php");

        if (!file_exists($path)) {
            throw new \RuntimeException("View [{$view}] not found.");
        }

        ob_start();
        require $path;
        return ob_get_clean();
    }
}
