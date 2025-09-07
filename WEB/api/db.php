<?php
$host = "fdb1028.awardspace.net"; // Thường là "db.awardspace.net"
$user = "4602295_doantotnghiep";
$pass = "laplainickms1";
$dbname = "4602295_doantotnghiep";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
