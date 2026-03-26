<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization,Accept,Content-Type');
header('Access-Control-Expose-Headers: Vary,Access-Control-Allow-Origin,Access-Control-Allow-Methods,Access-Control-Allow-Headers');
header('Vary: Origin');

header('Content-Type: application/json; charset=UTF-8');

?>
{"response":"OK"}

