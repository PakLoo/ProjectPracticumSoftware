<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//connect database
$servername = "151.106.124.154"; //IP เครื่อง server
$username = "u583789277_LabdbG15"; //user account ที่กําหนดให้
$password = "Chairman2567"; //รหัสผ่านที่กําหนดให้
$dbname = "u583789277_LabdbG15"; //ชื่อฐานข้อมูลที่กําหนดให้

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>