<?php
//-header('Access-Control-Allow-Origin: *');
+header('Access-Control-Allow-Origin: http://localhost:8080');
+header('Access-Control-Allow-Credentials: true');
+//header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE,');
include ('../app/RestServer.php');
$restServer  = new RestServer();
$restServer->run();
//$restServer->responseMethod($result);
