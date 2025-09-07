<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// Kiểm tra đầu vào hợp lệ
if (!empty($data["email"]) ) {
    $email = trim($data["email"]);
   $checkStmt = $conn->prepare("SELECT id_account FROM account WHERE email = ?");
   $checkStmt->bind_param("s", $email);
   $checkStmt->execute();
   $res = $checkStmt->get_result();
   if ($res->num_rows > 0) {
       echo json_encode(["status" => "success", "data" => $res->fetch_assoc(), "message" => "Lấy dữ liệu thành công"]);
       exit();
   } else {
        echo json_encode(["status" => "failed", "data" => true, "message" => "Lấy dữ liệu không thành công"]);
        exit();        
    }   
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$conn->close();
?>
