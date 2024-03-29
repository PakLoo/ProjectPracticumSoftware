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


//14
$app->post('/EventInsert', 
        function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $body = $request->getParsedBody();
    $stmt = $conn->prepare("INSERT INTO event " . "(EventID,EventName, EventDate, EventDateEnd)". 
                            " values (?,?,?,?)");
    $stmt->bind_param("isss",
                        $body['EventID'],$body['EventName'], $body['EventDate'], $body['EventDateEnd']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json');


});


//15 แก้ไขงาน
$app->post('/EventUpdate', 
        function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $body = $request->getParsedBody();
    $stmt = $conn->prepare("UPDATE event SET EventName=?,EventDate=?,EventDateEnd=? where EventID=?" );
    $stmt->bind_param("sssi",
                        $body['EventName'], $body['EventDate'], $body['EventDateEnd'], $body['EventID']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json');


});

//16 เพิ่มขอมูลในโซน
$app->post('/ZoneInsert', 
        function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $body = $request->getParsedBody();
    $stmt = $conn->prepare("INSERT INTO Zone " . "(ZoneID,ZoneName,EventID )". 
                            " values (?,?,?)");
    $stmt->bind_param("isi",$body['ZoneID'],$body['ZoneName'],$body['EventID']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json');

}); 

//17 แก้ไขข้อมูลในโซน
$app->post('/ZoneUpdate',function (Request $request, Response $response){
    $body = $request->getParsedBody();
    $oZN = $body['oZN'];
    $conn = $GLOBALS['conn'];
    $stmt = $conn->prepare("UPDATE Zone SET ZoneName=?  WHERE ZoneName='$oZN'");
    $stmt->bind_param("s",$body['ZoneName']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content-Type","application/json");

});


//18 ลบข้อมูลโซน เฉพาะรายการของชื่อโซน
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


//19 เพิ่มข้อมูลบูธ
$app->post('/admin/addBooth', function (Request $request, Response $response) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $stmt = $conn->prepare("INSERT INTO Booth (BoothName,BoothSize,BoothSelling,ZoneID) values (?,?,?,?) ");
    $stmt->bind_param("ssss",  $bodyArr["BoothName"], $bodyArr["BoothSize"],$bodyArr['BoothSelling'], $bodyArr["ZoneID"]);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content-Type","application/json");

});


//20 แก้ไขข้อมูลบูธ
$app->post('/admin/boothEdit', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $oBN = $body['oBN'];
    $stmt = $conn->prepare("UPDATE Booth SET BoothID=?, BoothName =?, BoothSize=?, Product=? WHERE BoothName = '$oBN'");
    $stmt->bind_param("isss", $body["BoothID"],$body["BoothName"], $body["BoothSize"], $body["Product"]);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content-Type","application/json");
});

//21 ลบข้อมูลบูธ เฉพาะรายการของชื่อบูธ
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

//22
$app->post("/member/booking", function (Request $request, Response $response, array $args) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    
    $stmt2 = $conn->prepare("SELECT bookingStatus FROM booking WHERE boothID = ?");
    $stmt2->bind_param("i", $bodyArr["boothID"]);
    $stmt2->execute();
    $stmt2->store_result();
    $stmt2->bind_result($bookingStatus);
    $stmt2->fetch();
    
    if ($bookingStatus == "อยู่ระหว่างตรวจสอบ" || $bookingStatus == "อนุมัติแล้ว" || $bookingStatus == "ชำระเงิน") {
        $response->getBody()->write(json_encode(["message" => "บูธถูกจองแล้ว"]));
        return $response->withHeader("Content-Type", "application/json");
    }
    $stmt = $conn->prepare("SELECT count(userID) AS num_bookings FROM booking WHERE userID = ?");
    $stmt->bind_param("i", $bodyArr["userID"]);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($num_bookings);
    $stmt->fetch();
    if ($num_bookings <4){
        $stmt = $conn->prepare("INSERT INTO booking (boothID, product, userID, eventID, bookingStatus, paymentDate) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiiss", $bodyArr["boothID"], $bodyArr["product"], $bodyArr["userID"], $bodyArr["eventID"], $bookingStatus, $paymentDate);
        $bookingStatus = "อยู่ระหว่างตรวจสอบ";
        $paymentDate = "0000-00-00";
        $stmt->execute();
        $result = $stmt->affected_rows;

        $stmt3 = $conn->prepare("UPDATE booth SET boothStatus=? WHERE boothID=?");
        $stmt3->bind_param("si", $boothStatus, $bodyArr["boothID"]);
        $boothStatus = "อยู่ระหว่างตรวจสอบ";
        $stmt3->execute();
        if ($result > 0) {
            $response->getBody()->write(json_encode(["message" => "จองสำเร็จ"]));
        } else {
            $response->getBody()->write(json_encode(["message" => "เกิดข้อผิดพลาดในการจอง"]));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    else{
        echo "คุณไม่สามารถจองบูธได้อีกแล้ว";
    }
    return $response;
});

//23 selectmembers
$app->get('/admin/membersSelect', function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $sql = "SELECT firstname,lastname,telephone,email  FROM members";
    $result = $conn->query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});

//25
$app->get('/admin/memberWhoPaid', function (Request $request, Response $response) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $sql = "select concat(titleName, firstName)as firstName, lastName, telephone, booth.boothName, zone.zoneName from booking inner join user on booking.userID = user.userID 
            inner join booth on booking.boothID = booth.boothID inner join zone on zone.zoneID = booth.zoneID and bookingStatus = 'ชำระเงิน'";
    $result = $conn->Query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json');
});


//26 
$app->get('/admin/memberBoothInfo', function (Request $request, Response $response) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $sql = "select concat(titleName, firstName)as firstName, lastName, telephone, booth.boothName, zone.zoneName from booking inner join user on booking.userID = user.userID 
            inner join booth on booking.boothID = booth.boothID inner join zone on zone.zoneID = booth.zoneID and boothStatus  = 'อยู่ระหว่างตรวจสอบ'";
    $result = $conn->Query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json');
});

//27
$app->get('/admin/memberBoothBook', function (Request $request, Response $response) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $sql = "select concat(titleName, firstName)as firstName, lastName, zone.zoneName, booth.boothPrice , booth.boothName, booth.boothStatus from booking inner join user on booking.userID = user.userID 
            inner join booth on booking.boothID = booth.boothID inner join zone on zone.zoneID = booth.zoneID and (boothStatus = 'จองแล้ว' or boothStatus = 'อยู่ระหว่างตรวจสอบ')";
    $result = $conn->Query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json');
});

//booth in zone
$app->get('/admin/zoneInfo', function (Request $request, Response $response) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $sql = "SELECT Zone.ZoneID, Zone.ZoneName, Zone.ZoneDetail, COUNT(Booth.BoothID)as boothAmount FROM Booth INNER JOIN Zone ON Booth.ZoneID = Zone.ZoneID GROUP BY ZoneID";
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