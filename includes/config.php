<?php
/*
 * Application configuration.
 *
 * Credentials are read from environment variables so the same code can be
 * deployed to different environments (local XAMPP, VPS, container, or a PaaS
 * such as Railway/Render) without editing source files. When the variables are
 * not set, the local XAMPP defaults are used.
 *
 * Generic names are checked first, then the names injected by common managed
 * MySQL providers:
 *   DB_HOST / DB_PORT / DB_USER / DB_PASS / DB_NAME
 *   MYSQLHOST / MYSQLPORT / MYSQLUSER / MYSQLPASSWORD / MYSQLDATABASE
 */

if (!function_exists('pglife_env')) {
    function pglife_env($names, $default)
    {
        foreach ($names as $name) {
            $value = getenv($name);
            if ($value !== false && $value !== '') {
                return $value;
            }
        }
        return $default;
    }
}

if (!defined('DB_HOST')) {
    define('DB_HOST', pglife_env(array('DB_HOST', 'MYSQLHOST', 'MYSQL_HOST'), '127.0.0.1'));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', pglife_env(array('DB_PORT', 'MYSQLPORT', 'MYSQL_PORT'), '3306'));
}
if (!defined('DB_USER')) {
    define('DB_USER', pglife_env(array('DB_USER', 'MYSQLUSER', 'MYSQL_USER'), 'root'));
}
if (!defined('DB_PASS')) {
    define('DB_PASS', pglife_env(array('DB_PASS', 'MYSQLPASSWORD', 'MYSQL_PASSWORD'), 'password'));
}
if (!defined('DB_NAME')) {
    define('DB_NAME', pglife_env(array('DB_NAME', 'MYSQLDATABASE', 'MYSQL_DATABASE'), 'pglife'));
}
if (!defined('APP_ENV')) {
    define('APP_ENV', pglife_env(array('APP_ENV'), 'production'));
}
