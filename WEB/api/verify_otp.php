<?php
header("Content-Type: application/json");
include "db.php"; // Đảm bảo db.php kết nối bằng MySQLi

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (!empty($data["id_account"]) && !empty($data["otpcode"])) {
    $id_account = intval($data["id_account"]);
    $otpcode = trim($data["otpcode"]);

    // Kiểm tra OTP
    $stmt = $conn->prepare("SELECT id_account FROM otp WHERE id_account = ? AND otpcode = ? AND otpexpire > NOW()");
    $stmt->bind_param("is", $id_account, $otpcode);
    $stmt->execute();
    $stmt->store_result(); // Giải phóng bộ nhớ của SELECT
    if ($stmt->num_rows > 0) {
        $stmt->close();
		$stmt = $conn->prepare("DELETE FROM otp WHERE id_account = ?");
        $stmt->bind_param("i", $id_account);
    	$stmt->execute();
        // Tạo access_token
        $accessToken = bin2hex(random_bytes(32));

        // Cập nhật access_token trong bảng users
        $stmt = $conn->prepare("UPDATE users SET access_token = ? WHERE id_account = ?");
        $stmt->bind_param("si", $accessToken, $id_account);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "data" => $accessToken, "message" => "Xác thực thành công"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi cập nhật access_token"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Sai mã OTP hoặc OTP đã hết hạn"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ"]);
}

$conn->close();
?>
