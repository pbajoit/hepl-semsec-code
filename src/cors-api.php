<?php
// include this file to build the CORS headers

const DEVELOPMENT = True;
if (DEVELOPMENT) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

/**
 * @return void
 */
function add_headers_origin($host, $dev = False, $acceptAllHost = False, $showCorsHeaders = False)
{
    $possibleOrigins = [
        $host
    ];

    // detect the origin of the API and accept any host
    if ($acceptAllHost) {
        $possibleOrigins[] = "https://" . $_SERVER['HTTP_HOST'];
    }

    // accept developer origin working on localhost webserver or file://
    if ($dev) {
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
        if ($showCorsHeaders) {
            header('Access-Control-Expose-Headers: ".
            "Vary,Access-Control-Allow-Origin,Access-Control-Allow-Methods,Access-Control-Allow-Headers');
        }
    }

    header('Cache-Control: no-cache');
    header('X-Content-Type-Options: nosniff');
}
