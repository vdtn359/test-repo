<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "CourseEnrolments";

function getConn() {
    global $servername, $username, $password, $dbname;

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection error: " . $conn->connect_error);
    }

    return $conn;
}
?>