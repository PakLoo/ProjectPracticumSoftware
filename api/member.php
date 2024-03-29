<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\MessageInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//4 members Login
$app->post("/memberLoginAndLogOut",function (Request $request,Response $response,array $args) { 
    function getPasswordFromDB($conn,$email){
        $stmt = $conn->prepare("select password from members where email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row["password"];
        }
    }

    function getInfo($conn,$email){
        $stmt = $conn->prepare("select * from members where email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result2 = $stmt->get_result();
        if ($result2->num_rows > 0){
           while ($row2 = $result2->fetch_assoc()) {
            echo $row2["id"]." ".$row2["titlename"]." ".$row2["firstname"]." ".$row2["lastname"]." ".$row2["telephone"]." ".$row2["email"]." ".$row2["password"]."<br>";
           }
        }
    }

    $conn = $GLOBALS["conn"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $dbPassword = getPasswordFromDB($conn,$email);

    if ($dbPassword != $password) {
        echo 'failed';
    }else{        
        // เช็คว่ามี session ของการ Login อยู่หรือไม่
        session_start();
        if (isset($_SESSION['logged_in'])) {
            // ถ้า Login อยู่แล้ว ให้แสดงข้อความ LogOut
            echo "LogOut";
            // ลบ session
            session_unset();
            session_destroy();
        } else {
            getInfo($conn, $email);
            // ถ้ายังไม่ได้ Login ให้แสดงข้อความ Login
            echo "Login";
            // กำหนด session เป็น Login
            $_SESSION['logged_in'] = true;
        }
        return $response;
    }
    
});

//5 members check BoothZone
$app->get('/memberCheckBoothZone', function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $sql = "SELECT Zone.ZoneID, Zone.ZoneName, Zone.ZoneDetail, COUNT(Booth.BoothID)as BoothAmount FROM Booth INNER JOIN Zone ON Booth.ZoneID = Zone.ZoneID GROUP BY Zone.ZoneID";
    $result = $conn->query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');

});

//6 members check BoothDetail
$app->get('/memberCheckBoothDetail', function (Request $request, Response $response, array $args){
    $conn = $GLOBALS['conn'];
    $sql = "SELECT BoothID, BoothName, BoothSize, BoothStatus, BoothPrice FROM Booth";
    $result = $conn->query($sql);
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data, $row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');

});

// 7 
$app->post('/booking/checkbook', function (Request $request, Response $response, array $args) {

    $usd = $_POST['id'];
    $conn = $GLOBALS['conn'];
    $stmt = $conn->prepare(" SELECT * FROM Booth WHERE id = ? ; ");
    $stmt->bind_param("s", $usd);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();

    while ($row = $result -> fetch_assoc()) {
        echo $row['BoothName'].' '.$row['BoothSize'].' '.$row['Product'].' '.$row['ZoneID'].' '.$row['BoothPrice'].' '.$row['BoothStatus'].' '.$row['id'].' '.$row['BookStatus'].'<br>';
    }
    function selectBooth($conn,$response){
        $sql = "select * from Booth";
        $result = $conn ->query($sql);
        $data=array();
        while ($row = $result -> fetch_assoc()) {
            array_push($data,$row);
        }
        $json = json_encode($data);
        $response->getBody()->write($json);
    }

    while ($row = $result->fetch_assoc()) {
        array_push($data, $row);
    }
    /* rowc ก็คือการนับจำนวนแถวรึอะไรสักอย่าง- */
    $rowc = mysqli_num_rows($result);
    return $response;
    if (($rowc > -1) and ($rowc < 4)){
        echo "you can book booth"."<br>";
        selectBooth($conn,$response);
        return $response;
    }elseif($rowc > 3){
        echo 'you have limit book';
        return $response;
    }else{
        echo 'fail';
        return $response;
    }
    
});

//8
$app->post("/member/check", function (Request $request, Response $response, array $args) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $stmt = $conn->prepare("SELECT count(userID) AS num_bookings FROM booking WHERE userID = ?");
    $stmt->bind_param("i", $bodyArr["userID"]);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($num_bookings);
    $stmt->fetch();
    if ($num_bookings <4){
        $x = 4-$num_bookings;
        echo "คุณยังสามารถจองได้อีก ".$x." บูธ".'<br>';
        $stmt = $conn->prepare("SELECT boothName from booth where boothStatus = 'ว่าง'");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        echo "บูธที่ยังว่างอยู่มีดังนี้ :";
        while ($row = $result -> fetch_assoc()){         
        echo $row['boothName']." ";}
        return $response;
    }
    else{
        echo "คุณไม่สามารถจองบูธได้อีกแล้ว";
    }
    return $response;
});


//10
$app->post("/member/paid", function (Request $request, Response $response, array $args) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $stmt = $conn->prepare("UPDATE booking SET paymentDate=?, bookingStatus=? WHERE boothID=?");
    $stmt->bind_param("ssi", $bodyArr["paymentDate"], $bookingStatus, $bodyArr["boothID"]);
    $bookingStatus = "ชำระเงิน";
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt3 = $conn->prepare("UPDATE booth SET boothStatus=? WHERE boothID=?");
    $stmt3->bind_param("si", $boothStatus, $bodyArr["boothID"]);
    $boothStatus = "จองแล้ว";
    $stmt3->execute();
    $stmt4 = $conn->prepare("SELECT startDate FROM event WHERE eventID=?");
    $stmt4->bind_param("i", $bodyArr["boothID"]);
    $stmt4->execute();
    $stmt4->bind_result($startDate);
    $stmt4->fetch();
    $stmt4->close();
    if ($startDate && $bodyArr["paymentDate"]) {
            $startDateTimestamp = strtotime($startDate);
            $paymentDateTimestamp = strtotime($bodyArr["paymentDate"]);
            $daysDifference = ($paymentDateTimestamp - $startDateTimestamp) / (60 * 60 * 24);

            if ($daysDifference < 5) {
                $stmt = $conn->prepare("UPDATE booking SET bookingStatus=?, userID=? WHERE boothID=?");
                $stmt->bind_param("ssi", $bookingStatus, $userID,$bodyArr["boothID"]);
                $bookingStatus = "ยกเลิกการจอง";
                $userID = 0;
                $stmt->execute();
                $result = $stmt->affected_rows;
                $stmt3 = $conn->prepare("UPDATE booth SET boothStatus=? WHERE boothID=?");
                $stmt3->bind_param("si", $boothStatus, $bodyArr["boothID"]);
                $boothStatus = "ว่าง";
                $stmt3->execute();
                $response->getBody()->write(json_encode(["message" => "ไม่สามารถชำระเงินได้"]));
                return $response->withHeader("Content-Type", "application/json");
            } else {
                $stmt = $conn->prepare("UPDATE booking SET paymentDate=?, bookingStatus=? WHERE boothID=?");
                $stmt->bind_param("ssi", $bodyArr["paymentDate"], $bookingStatus, $bodyArr["boothID"]);
                $bookingStatus = "ชำระเงิน";
                $stmt->execute();
                $result = $stmt->affected_rows;
                $stmt3 = $conn->prepare("UPDATE booth SET boothStatus=? WHERE boothID=?");
                $stmt3->bind_param("si", $boothStatus, $bodyArr["boothID"]);
                $boothStatus = "จองแล้ว";
                $stmt3->execute();
                $response->getBody()->write(json_encode(["message" => "สามารถชำระเงินได้"]));
                return $response->withHeader("Content-Type", "application/json");
            }
        }

    $response->getBody()->write($result . "");
    return $response->withHeader("Content-Type", "application/json");
});


//11
$app->post("/member/cancle", function (Request $request, Response $response, array $args) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $stmt = $conn->prepare("UPDATE booking SET bookingStatus=?, userID=? WHERE boothID=?");
    $stmt->bind_param("ssi", $bookingStatus, $userID,$bodyArr["boothID"]);
    $bookingStatus = "ยกเลิกการจอง";
    $userID = 0;
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt3 = $conn->prepare("UPDATE booth SET boothStatus=? WHERE boothID=?");
    $stmt3->bind_param("si", $boothStatus, $bodyArr["boothID"]);
    $boothStatus = "ว่าง";
    $stmt3->execute();
    $response->getBody()->write($result . "");
    return $response->withHeader("Content-Type", "application/json");
});

//12 member update firstname lastname telephone password by email search
$app->post("/memberUpdate",function (Request $request,   Response $response) {
    $body= $request->getParsedBody();
    $oEmail = $body["oEmail"];
    $conn = $GLOBALS["conn"];
    $stmt = $conn->prepare("UPDATE members set titlename=?, firstname = ? ,lastname = ? ,telephone = ? , email =?, password = ? WHERE email = '$oEmail'");
    $stmt->bind_param("ssssss",$body['titlename'],$body['firstname'],$body['lastname'],$body['telephone'],$body['email'],$body['password']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader("Content-Type","application/json");
});

//13
$app->get('/member/memberBoothBook', function (Request $request, Response $response) {
    $bodyArr = $request->getParsedBody();
    $conn = $GLOBALS['conn'];
    $sql = "select  booth.boothName, (booth.boothPrice)as Price , zone.zoneName, booth.boothStatus from booking inner join user on booking.userID = user.userID 
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


?>