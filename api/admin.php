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
//15 แก้ไขงาน
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
//16 เพิ่มขอมูลในโซน
$app->post('/ZoneInsert', 
        function (Request $request, Response $response, array $args){
$conn = $GLOBALS['conn'];
$body = $request->getParsedBody();
$stmt = $conn->prepare("insert into Zone " . "(ZoneID,ZoneName,ZoneDetail)". 
                        " values (?,?,?)");
$stmt->bind_param("sss",
                     $body['ZoneID'],$body['ZoneName'], $body['ZoneDetail']);
$stmt->execute();
$result = $stmt->affected_rows;
$response->getBody()->write($result."");
return $response->withHeader('Content-Type', 'application/json');
});          
//17 แก้ไขข้อมูลในโซน
$app->post('/ZoneUpdate', 
        function (Request $request, Response $response, array $args){
$conn = $GLOBALS['conn'];
$body = $request->getParsedBody();
$stmt = $conn->prepare("UPDATE Zone SET ZoneName=?,ZoneDetail=? where BoothID=?" );
$stmt->bind_param("sss",
                    $body['ZoneName'], $body['ZoneDetail'], $body['BoothID']);
$stmt->execute();
$result = $stmt->affected_rows;
$response->getBody()->write($result."");
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
    return $response;
});


//ลบข้อมูลบูธ เฉพาะรายการของชื่อบูธ
$app->post("/DeleteBooth",function (Request $request,   Response $response,array $args) {
    $body= $request->getParsedBody();
    $conn = $GLOBALS["conn"];
    $stmt = $conn->prepare("DELETE FROM Booth WHERE BoothName=?");
    $stmt->bind_param("s",$body['BoothName']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response;
});


//เพิ่มข้อมูลบูธ
$app->post('/admin/addBooth', function (Request $request, Response $response) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $stmt = $conn->prepare("insert into booth (boothName,boothSize,product,zoneID) values (?,?,?,?) ");
    $stmt->bind_param("ssss",  $bodyArr["boothName"], $bodyArr["boothSize"],$bodyArr['product'], $bodyArr["zoneID"]);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content-Type","application/json");

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



//แก้ไขข้อมูลบูธ
$app->post('/admin/boothEdit', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $oBN = $body['oBN'];
    $stmt = $conn->prepare("UPDATE Booth SET BoothName =?, BoothSize=?, BoothSelling=? where BoothName = '$oBN'");
    $stmt->bind_param("sss", $body["BoothName"], $body["BoothSize"], $body["BoothSelling"]);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content-Type","application/json");
});

//booth in zone
$app->get('/member/zoneInfo', function (Request $request, Response $response) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $sql = "SELECT zone.zoneID, zone.zoneName, zone.zoneDetail, COUNT(booth.boothID)as boothAmount FROM boothINNER JOIN zone ON booth.zoneID = zone.zoneID GROUP BY zoneID";
    $result = $conn->Query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json');
});
?>