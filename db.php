<?php
$host = "your host name(ex. Localhosst)";
$user = "name of db user";
$pass = "password of db";
$dbname = "name of your db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
