<?php
/*
 * Creates the shared MySQL connection.
 *
 * Credentials come from includes/config.php (environment variables with
 * local XAMPP defaults). On failure the request is stopped with a generic
 * message so no connection details leak to the browser.
 */

require_once __DIR__ . '/config.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int) DB_PORT);

if (!$conn) {
    http_response_code(500);
    die('Failed to connect to the database. Please contact the admin.');
}

mysqli_set_charset($conn, 'utf8mb4');
