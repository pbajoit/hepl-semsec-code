<?php
// include this file to build the CORS headers

const DEVELOPMENT = True;
const ACCEPT_ALL_HOST = True;

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
    $possibleOrigins = [
        "https://semsec.hepl-e-business.be"
    ];

    // detect the origin of the API and accept any host
    if (ACCEPT_ALL_HOST) {
        $possibleOrigins[] = "https://" . $_SERVER['HTTP_HOST'];
    }

    // accept developer origin working on localhost
    if (DEVELOPMENT) {
        $possibleOrigins[] = 'http://localhost:5173';
        $possibleOrigins[] = 'http://localhost:5174';
        $possibleOrigins[] = 'http://localhost:63342';
        $possibleOrigins[] = 'null';
    }

    $headers = apache_request_headers();

    if (!empty($headers['Origin'])) {
        $origin = $headers['Origin'];

        // the origin has to be listed in the $possibleOrigins
        if (!in_array($origin, $possibleOrigins)) {
            $origin = $possibleOrigins[0];
        }

        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Authorization,Accept,Content-Type");
        // header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
    }

    header('Cache-Control: no-cache');
    header('X-Content-Type-Options: nosniff');

    // if relevant, set the content type
    // header('Content-Type: application/json; charset=UTF-8');
}
