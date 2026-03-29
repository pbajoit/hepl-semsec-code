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
function add_headers_origin($host, $dev, $acceptAllHost, $showCorsHeaders)
{
    $headers = apache_request_headers();
    $response = [];

    $response[] = "add_headers_origin for host: $host";
    $possibleOrigins = [
        $host
    ];

    // detect the origin of the API and accept any host
    if ($acceptAllHost && !empty($headers['Origin'])) {
        $possibleOrigins[] = $headers['Origin'];
        $response[] = "accepting any host ie $headers[Origin]";
    }

    // accept developer origin working on localhost webserver or file://
    if ($dev) {
        $possibleOrigins[] = 'http://localhost:5173';
        $possibleOrigins[] = 'http://localhost:5174';
        $possibleOrigins[] = 'http://localhost:63342';
        $possibleOrigins[] = 'null';
    }

    if (!empty($headers['Origin'])) {
        $origin = $headers['Origin'];
        $response[] = "looking for origin: $origin";
        $response[] = "  in list: " . join(',', $possibleOrigins);

        // the origin has to be listed in the $possibleOrigins
        if (!in_array($origin, $possibleOrigins)) {
            $origin = $possibleOrigins[0];
            $response[] = "no valid Origin";
        } else {
            $response[] = "valid Origin found in list";
        }

        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Authorization,Accept,Content-Type");
        // header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
        if ($showCorsHeaders) {
            header('Access-Control-Expose-Headers: Vary,Access-Control-Allow-Origin,Access-Control-Allow-Methods,Access-Control-Allow-Headers');
        }
    } else {
        $response[] = "no Origin in the request";
    }

    header('Cache-Control: no-cache');
    header('X-Content-Type-Options: nosniff');

    header('X-Debug-Response: ' . json_encode($response));
}
