<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// Kiểm tra đầu vào hợp lệ
if (!empty($data["id_account"]) && !empty($data["otpcode"])) {
    
    $id_account = intval($data["id_account"]);
    $otpcode = trim($data["otpcode"]);
    $otpexpire = date("Y-m-d H:i:s", strtotime("+5 minutes"));
	$sql_del = "DELETE FROM otp WHERE id_account = ?";
    $stmt_del = $conn->prepare($sql_del);
    $stmt_del->execute([$id_account]);
    // Chuẩn bị truy vấn an toàn
    $sql = "INSERT INTO otp (id_account, otpcode, otpexpire) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_account, $otpcode, $otpexpire]);
    echo json_encode(["status" => "success", "message" => "OTP đã được gửi!", "otp" => $otpcode]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$conn->close();
?>
