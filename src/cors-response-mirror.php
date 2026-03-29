<?php
include 'cors-api.php';

add_headers_origin("https://semsec.hepl-e-business.be", True, True, True);

header('Content-Type: application/json; charset=UTF-8');
?>
{"response":"OK"}

