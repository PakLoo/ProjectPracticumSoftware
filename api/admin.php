<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\MessageInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//select all members
$app->get('/members', function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $sql = "select * from members";
    $result = $conn->query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');


});


//14
$app->post('/EventInsert', 
        function (Request $request, Response $response, array $args){
$conn = $GLOBALS['conn'];
$body = $request->getParsedBody();
$stmt = $conn->prepare("insert into event " . "(EventID,EventName, EventDate, EventDateEnd)". 
                        " values (?,?,?,?)");
$stmt->bind_param("isss",
                    $body['EventID'],$body['EventName'], $body['EventDate'], $body['EventDateEnd']);
$stmt->execute();
$result = $stmt->affected_rows;
$response->getBody()->write($result."");
return $response->withHeader('Content-Type', 'application/json');
echo "Insert Success";

});
//แสดงข้อมูล * ใน Event
$app->get('/EventSelect', function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $sql = "select * from event";
    $result = $conn->query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});
//15
$app->post('/EventUpdate', 
        function (Request $request, Response $response, array $args){
$conn = $GLOBALS['conn'];
$body = $request->getParsedBody();
$stmt = $conn->prepare("UPDATE event SET EventName=?,EventDate=?,EventDateEnd=? where EventID=?" );
$stmt->bind_param("ssss",
                    $body['EventName'], $body['EventDate'], $body['EventDateEnd'], $body['EventID']);
$stmt->execute();
$result = $stmt->affected_rows;
$response->getBody()->write($result."");
return $response->withHeader('Content-Type', 'application/json');
echo "Update Success";

});
//16
$app->post('/ZoneInsert', 
        function (Request $request, Response $response, array $args){
$conn = $GLOBALS['conn'];
$body = $request->getParsedBody();
$stmt = $conn->prepare("insert into Zone " . "(ZoneQuantity,ZoneName, ZoneDetail)". 
                        " values (?,?,?)");
$stmt->bind_param("sss",
                     $body['ZoneQuantity'],$body['ZoneName'], $body['ZoneDetail']);
$stmt->execute();
$result = $stmt->affected_rows;
$response->getBody()->write($result."");
return $response->withHeader('Content-Type', 'application/json');
echo "Insert Success";

});
//selectZone
$app->get('/ZoneSelect', function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $sql = "select * from Zone";
    $result = $conn->query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});

