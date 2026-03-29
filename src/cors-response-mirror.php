<?php
include 'cors-api.php';

add_headers_origin("https://" . $_SERVER['HTTP_HOST'], True, True, True);

header('Content-Type: application/json; charset=UTF-8');
?>
{"response":"OK"}

