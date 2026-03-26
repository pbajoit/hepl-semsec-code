<?php
include 'cors-api.php';

add_headers_origin();
header('Access-Control-Expose-Headers: Vary,Access-Control-Allow-Origin,Access-Control-Allow-Methods,Access-Control-Allow-Headers');

header('Content-Type: application/json; charset=UTF-8');

?>
{"response":"OK"}

