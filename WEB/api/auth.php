<?php
header('Content-Type: application/json');

$headers = getallheaders(); // Lấy tất cả headers
echo json_encode($headers, JSON_PRETTY_PRINT);
?>
