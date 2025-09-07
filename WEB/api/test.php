<?php
header("Access-Control-Allow-Origin: *"); // Cho phép truy cập từ mọi nguồn
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php"; // Kết nối MySQL

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uploadDir = "uploads/"; // Thư mục lưu ảnh
    $uploadFile = $uploadDir . basename($_FILES["image"]["name"]);

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadFile)) {
        $name = $_FILES["image"]["name"];

        // Lưu đường dẫn vào database
        $stmt = $conn->prepare("INSERT INTO images (name, path) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $uploadFile);
        $stmt->execute();

        echo json_encode(["message" => "Ảnh đã được lưu!", "path" => $uploadFile]);
    } else {
        echo json_encode(["message" => "Lỗi khi tải ảnh lên!"]);
    }
} else {
    echo json_encode(["message" => "Chỉ chấp nhận phương thức POST"]);
}
?>
