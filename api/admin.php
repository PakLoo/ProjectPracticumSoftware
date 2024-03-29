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
    $stmt = $conn->prepare("INSERT INTO event " . "(EventID,EventName, EventDate, EventDateEnd)". 
                            " values (?,?,?,?)");
    $stmt->bind_param("isss",
                        $body['EventID'],$body['EventName'], $body['EventDate'], $body['EventDateEnd']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
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


// 7 bbbb
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
        // $sql = ("SELECT * FROM booth where userId=$usd");
        // $result = $conn->query($sql);
        // $data2 = array();
        // while($row = $result->fetch_assoc()) {
        // array_push($data2, $row);
        // }
        // $json = json_encode($data2);
        // $response->getBody()->write($json." ");


        selectBooth($conn,$response);
        return $response;
    }elseif($rowc > 3){
        echo 'you have limit book';
        // $sql = ("SELECT * FROM booth where userId=$usd");
        // $result = $conn->query($sql);
        // $data2 = array();
        // while($row = $result->fetch_assoc()) {
        // array_push($data2, $row);
        // }
        // $json = json_encode($data2);
        // $response->getBody()->write($json.'');
        return $response;
    }else{
        echo 'fail';
        // $sql = ("SELECT * FROM booth where userId=$usd");
        // $result = $conn->query($sql);
        // $data2 = array();
        // while($row = $result->fetch_assoc()) {
        // array_push($data2, $row);
        // }
        // $json = json_encode($data2);
        // $response->getBody()->write($json);
        return $response;
    }
    
    // return $response->withHeader('Content-Type', 'application/json');
});

//
/* insert into booth (datebook, datepaid, booth_id, price, pathslip, book_status, product, userID, organize_id) ต้องมี 9ตัวนี้*/
$app->post('/BoothSelect', function (Request $request, Response $response, array $args) {
    
    function getPrice($conn,$bID){
        $stmt = $conn->prepare("SELECT BoothPrice FROM Booth WHERE BoothID = ?");
        $stmt->bind_param("s",$bID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row["BoothPrice"];
        }
    }
      
    function getStartDate($conn,$organize_id){
        $stmt = $conn->prepare("SELECT EventDateStart FROM event WHERE EventID = ?");
        $stmt->bind_param("s",$organize_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row["EventDateStart"];
        }
    }

    function checkRow($conn,$usd){
        $stmt = $conn->prepare(" SELECT id FROM Booth WHERE id = ? ; ");
        $stmt->bind_param("s", $usd);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            array_push($data, $row);
        }
        /* rowc ก็คือการนับจำนวนแถวรึอะไรสักอย่าง- */
        $rowc = mysqli_num_rows($result);
        return $rowc;
    }

    $conn = $GLOBALS['conn'];
    $body= $request->getParsedBody();
    $organize_id = $body['EventID'];
    $usd = $body['id'];
    $startDate = getStartDate($conn,$organize_id);
    $today = $body['BookingDate'];
    $todays = date ('Y-m-d',strtotime($today));
    $startDates = date("Y-m-d",strtotime("-5 day ",strtotime($startDate)));//21/3/2567 -> 16/3/2567
    
    $bID = $body['BoothID'];

    $rowCheck = checkRow($conn,$usd);
    
    function checkEmpty($conn,$bID){
        $stmt = $conn->prepare("SELECT BoothStatus FROM Booth WHERE BoothID = ?");
        $stmt->bind_param("s",$bID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row["BoothStatus"];
        }
    }

    $checkEmpty = checkEmpty($conn,$bID);
    

    
    if ($rowCheck<4){
        if ($checkEmpty == "empty"){
            if($todays < $startDates){
                $conn = $GLOBALS['conn'];
                $body= $request->getParsedBody();
                $bID = $body['BoothID'];
                $product = $body['Product'];
                $orID = $body['EventID'];
                $bName = $body['BoothName'];
                $price = getPrice($conn,$bID);
                $stmt = $conn->prepare(" INSERT INTO Booking (BookingDate, PaymentDate, BoothID, BoothPrice, BookStatus, Product, id, EventID) values (?,?,?,?,'book',?,?,?) ");    
                $stmt->bind_param("ssissis",  $body['BookingDate'],$body['PaymentDate'] , $body['BoothID'], $price, $body['Product'], $body['id'], $body['EventID']);
                $stmt->execute();

                function setCurrent($conn,$bName,$usd,$product,$orID,$bID){
                    $stmt = $conn->prepare("UPDATE Booth SET BoothName=? ,BoothStatus = 'checking' , BoothStatus = 'waiting for paid' ,Product = ? ,id = ? , EventID = ? WHERE BoothID = ?");
                    $stmt->bind_param("ssiii",$bName,$product,$usd,$orID,$bID);
                    $stmt->execute();
                }

                setCurrent($conn,$bName,$usd,$product,$orID,$bID);
            }else{
                $conn = $GLOBALS['conn'];
                $body= $request->getParsedBody();
                $bID = $body['BoothID'];
                $product = $body['Product'];
                $bName = $body['BoothName'];
                $oID = $body['EventID'];
                $price = getPrice($conn,$bID);
                $stmt = $conn->prepare(" INSERT INTO Booking (BookingDate, PaymentDate, BoothID, BoothPrice, BookStatus, Product, id, EventID) values (?,?,?,?,'book',?,?,?) ");    
                $stmt->bind_param("ssissis",  $body['BookingDate'],$body['PaymentDate'] , $body['BoothID'], $price, $body['Product'], $body['id'], $body['EventID']);
                $stmt->execute();
                function setCurrent3($conn,$bName,$product,$usd,$bID,$oID){
                    $stmt = $conn->prepare("UPDATE Booth SET BoothName=? ,BoothStatus = 'checking' , BoothStatus = 'waiting for paid' ,Product = ? ,id = ? , EventID = ? WHERE BoothID = ?");
                    $stmt->bind_param("ssiii",$bName,$product,$usd,$oID,$bID);
                    $stmt->execute();
                }
                setCurrent3($conn,$bName,$product,$usd,$bID,$oID);
            }
        }else{
            
            echo 'you can not Booking this Booth';
        }
    }else{
        echo "you have limit Book";
    };
    return $response;
});

$app->post('/book/paid', function (Request $request,  Response $response, array $args) { 
    function getOr($conn,$bID){
        $stmt = $conn->prepare("SELECT EventID FROM Booth WHERE BoothID = ?");
        $stmt->bind_param("s",$bID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row["organize_id"];
        }
    }
    function getStartDate2($conn,$organize_id){
        $stmt = $conn->prepare("SELECT start_date FROM organize WHERE organize_id = ?");
        $stmt->bind_param("s",$organize_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row["start_date"];
        }
    }
    
    $conn = $GLOBALS['conn'];
    $body= $request->getParsedBody();
    $bID = $body['booth_id'];
    $organize_id = getOr($conn,$bID);
    // $organize_id = $body['organize_id'];
    $startDate = getStartDate2($conn,$organize_id);
    $today = $body['bookPaid'];
    $todays = date ('Y-m-d',strtotime($today));
    $startDates = date("Y-m-d",strtotime("-5 day ",strtotime($startDate)));

    if( $todays < $startDates ){
        $default_payment = ""; 
        $payment = isset($body["payment"]) ? $body["payment"] : $default_payment;
        $stmt = $conn->prepare('update book set book_status = "paid" , bookPaid = ? , payment = ? where booth_id = ?  ');
        $stmt->bind_param('sss', $body['bookPaid'], $payment, $body['booth_id']) ;
        $stmt->execute();
        $bID = $body['booth_id'];
        function setCurrent2($conn,$bID){
            $stmt = $conn->prepare("UPDATE booth SET current_status = 'checking' , book_status = 'paid'  WHERE booth_id = ?");
            $stmt->bind_param("s",$bID);
            $stmt->execute();
        }
        setCurrent2($conn,$bID);
    }else{
        echo 'You can not paid Bill payment';
        function setCurrent2($conn,$bID){
            $stmt = $conn->prepare("UPDATE booth SET booth_name = '', product = '', userID = '',organize_id = '' , current_status = 'empty' , book_status = 'not paid'  WHERE booth_id = ?");
            $stmt->bind_param("s",$bID);
            $stmt->execute();
        }
        setCurrent2($conn,$bID);
        
    }
    return $response;
});     


$app ->post('/book/cancleBook', function (Request $request, Response $response,  array $args) {
        $conn = $GLOBALS['conn'];
        $body= $request->getParsedBody();
        $bID = $body['booth_id'];
        $stmt = $conn->prepare("UPDATE booth SET product ='',booth_name = '',current_status = 'empty' , book_status = 'cancle' ,userId='',organize_id = ''  WHERE booth_id = ?");
        $stmt->bind_param("s",$bID);
        $stmt->execute();
        return $response->withHeader('Content-Type', 'application/json');
});
?>