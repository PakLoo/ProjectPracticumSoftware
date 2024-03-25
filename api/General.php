<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\MessageInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $app->post('/employees/empTextInsert', 
            function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $body = $request->getBody();
    $bodyArray = json_decode($body, true);
    $stmt = $conn->prepare("insert into members " . "(titlename, firstName, lastName, 
                            telephone, email , password)".
                            " values (?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssss",
                        $bodyArray['titlename'], $bodyArray['firstName'], $bodyArray['lastName'],
                        $bodyArray['telephone'], $bodyArray['email'], $bodyArray['password']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json');


    });



}elseif ($_SERVER['REQUEST_METHOD'] === 'GET')
    echo 'Hello';




?>