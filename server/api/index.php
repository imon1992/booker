<?php
header('Access-Control-Allow-Origin: http://localhost:8080');
//header('Access-Control-Expose-Headers: Set-Cookie');
//header('Access-Control-Allow-Headers: cookie');
header('Access-Control-Allow-Credentials: true');
setcookie("asd", 'sad', time()-2,'/','http://localhost:8080/#/');
//setcookie("aaa", 'sad', time()-2);
//header('asd: 123');
//header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE,');
//header('Content-Type: application/json');
include ('../app/RestServer.php');
$c = new RestServer();
echo json_encode($c->run());
