<?php
$servername = "sql213.infinityfree.com";
$username = "if0_39662035";
$password = "GC18xYBIpgknE";
$dbname = "if0_39662035_car_workshop";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection Failed: ". $conn->connect_error);
} else {
    mysqli_select_db($conn, $dbname);
}
?>
