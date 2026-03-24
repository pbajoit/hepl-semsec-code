<?php
const DEVELOPMENT = True;
if (DEVELOPMENT) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

/**
 * @return void
 */
function add_headers_origin()
{
    // detect the origin of the API
    $possibleOrigins = [
        "https://" . $_SERVER['HTTP_HOST']
    ];

    if (DEVELOPMENT) {
        // accept working developer origin
        $possibleOrigins[] = 'http://localhost:5173';
        $possibleOrigins[] = 'http://localhost:5174';
    }

    $headers = apache_request_headers();
    $origin = (!empty($headers['Origin'])) ? $headers['Origin'] : '';

    if (!in_array($origin, $possibleOrigins)) {
        $origin = $possibleOrigins[0];
    }

    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS');
    header("Access-Control-Allow-Headers: Authorization,Accept,Content-Type");
    // header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
    header('Cache-Control: no-cache');
    header('X-Content-Type-Options: nosniff');

    header('Content-Type: application/json; charset=UTF-8');
}
