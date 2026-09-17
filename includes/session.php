<?php
/*
 * Starts a hardened PHP session.
 *
 * - use_strict_mode rejects uninitialised session ids.
 * - The session cookie is HTTP-only and SameSite=Lax to reduce the impact
 *   of XSS and CSRF.
 *
 * Safe to require multiple times.
 */

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    /* Mark the cookie Secure when the request is (or is proxied as) HTTPS. */
    $is_https = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params(array(
            'lifetime' => 0,
            'path' => '/',
            'secure' => $is_https,
            'httponly' => true,
            'samesite' => 'Lax',
        ));
    } else {
        session_set_cookie_params(0, '/; samesite=Lax', '', false, true);
    }

    session_start();
}
