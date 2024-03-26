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
echo "Sign Up Success";

});

$app->get('/VisitorZoneBooth', function (Request $request, Response $response, array $args){
        $conn = $GLOBALS['conn'];
        $sql = "select ZoneID, ZoneName, ZoneDetail, count(BoothID)as BoothID FROM Zone";
        $result = $conn->query($sql);
        $data = array();
        while($row = $result->fetch_assoc()){
            array_push($data, $row);
        }
        $json = json_encode($data);
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    
    
});

$app->get('/VisitorDetailBooth', function (Request $request, Response $response, array $args){
        $conn = $GLOBALS['conn'];
        $sql = "select BoothID, BoothName, BoothSize, BoothStatus, BoothPrice FROM Booth";
        $result = $conn->query($sql);
        $data = array();
        while($row = $result->fetch_assoc()){
            array_push($data, $row);
        }
        $json = json_encode($data);
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    
    
});



?>