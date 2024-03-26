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

$app->post("/userLogin",function (Request $request,Response $response,array $args) { 
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
        if ($result2->num_rows >0){
           while ($row2 = $result2->fetch_assoc()) {
            echo $row2["titlename"]." ".$row2["firstname"]." ".$row2["lastname"]." ".$row2["telephone"]." ".$row2["email"]."<br>";
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
        getInfo($conn,$email);
    }
});

$app->get('/ZoneBooth', function (Request $request, Response $response, array $args){
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

$app->get('/DetailBooth', function (Request $request, Response $response, array $args){
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