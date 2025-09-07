<?php
include "db.php"; // Kết nối MySQL


$stmt = $conn->prepare("SELECT path FROM images");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $imagePath = $row['path'];
    header("Content-Type: image/png"); // Thay đổi theo loại ảnh (jpeg, png)
    readfile($imagePath);
} else {
    echo "Ảnh không tồn tại!";
}
?>
