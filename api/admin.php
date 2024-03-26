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


//ลบข้อมูลโซน เฉพาะรายการของชื่อโซน
$app->post("/DeleteZone",function (Request $request,   Response $response,array $args) {
    $body= $request->getParsedBody();
    $conn = $GLOBALS["conn"];
    $stmt = $conn->prepare("DELETE FROM Zone WHERE ZoneName=?");
    $stmt->bind_param("s",$body['ZoneName']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content - Type","application/json");
});


//ลบข้อมูลบูธ เฉพาะรายการของชื่อบูธ
$app->post("/DeleteBooth",function (Request $request,   Response $response,array $args) {
    $body= $request->getParsedBody();
    $conn = $GLOBALS["conn"];
    $stmt = $conn->prepare("DELETE FROM Zone WHERE BoothName=?");
    $stmt->bind_param("s",$body['BoothName']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content - Type","application/json");
});


//เพิ่มข้อมูลบูธ
$app->post('/InsertDetailBooth', 
        function (Request $request, Response $response, array $args){
$conn = $GLOBALS['conn'];
$body = $request->getParsedBody();
$stmt = $conn->prepare("INSERT INTO members " . "(BoothID , BoothName, BoothSize)".
                        " values (?,?,?)".
                        "INSERT INTO Booking " . "(BoothSelling)".
                        " values (?)".
                        "INSERT INTO Zone " . "(ZoneID)".
                        " values (?);");
$stmt->bind_param("isssi",
                    $body['BoothID'], $body['BoothName'], $body['BoothSize'],
                    $body['BoothSelling'], $body['ZoneID']);
$stmt->execute();
$result = $stmt->affected_rows;
$response->getBody()->write($result."");
return $response->withHeader('Content-Type', 'application/json');
});

//แก้ไขข้อมูลบูธ
$app->post("/EditBooth",function (Request $request,   Response $response,array $args) {
    $body= $request->getParsedBody();
    $conn = $GLOBALS["conn"];
    $stmt = $conn->prepare("UPDATE Booth 
                            INNER JOIN Booking on Booking.BoothID = Booth.BoothID 
                            INNER JOIN Zone on Zone.BoothID = Booth.BoothID 
                            set BoothName = ? ,BoothSize = ? ,BoothSelling = ? ,ZoneID  = ? where BoothName = ?");
    $stmt->bind_param("sssss",$body['BoothName'],$body['BoothSize'],$body['BoothSelling'],$body['ZoneID']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content - Type","application/json");
});

?>