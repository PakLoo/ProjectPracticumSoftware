<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\MessageInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app->post('/SignUp', 
        function (Request $request, Response $response, array $args){
$conn = $GLOBALS['conn'];
$body = $request->getParsedBody();
$stmt = $conn->prepare("insert into members " . "(titlename, firstName, lastName, 
                        telephone, email , password)".
                        " values (?,?,?,?,?,?)");
$stmt->bind_param("ssssss",
                    $body['titlename'], $body['firstName'], $body['lastName'],
                    $body['telephone'], $body['email'], $body['password']);
$stmt->execute();
$result = $stmt->affected_rows;
$response->getBody()->write($result."");
return $response->withHeader('Content-Type', 'application/json');

});








?>