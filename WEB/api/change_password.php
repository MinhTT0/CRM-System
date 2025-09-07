<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// Kiểm tra đầu vào hợp lệ
if (!empty($data["id_account"]) && !empty($data["password"])) {
    $id_account = intval($data["id_account"]);
    $password = trim($data["password"]);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $sql = "UPDATE account SET password = ? where id_account = ?";   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $id_account);  
    $stmt->execute(); 
    echo json_encode(["status" => "success", "message" => "Đổi mật khẩu thành công"]);
    $stmt->close();        
    
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$conn->close();
?>
