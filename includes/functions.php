<?php
/*
 * Shared helpers: session bootstrap, output escaping and CSRF protection.
 *
 * Requiring this file also starts the hardened session (includes/session.php),
 * so it replaces the bare session_start() calls throughout the app.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

/* Never leak PHP errors to visitors on a production deployment. */
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED);
}

if (!function_exists('e')) {
    /* Escape untrusted values before printing them into HTML. */
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /* Return the per-session CSRF token, creating it on first use. */
    function csrf_token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_valid')) {
    /* Constant-time check of a submitted CSRF token. */
    function csrf_valid($token)
    {
        return is_string($token)
            && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}
