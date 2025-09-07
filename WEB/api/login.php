<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (!empty($data["username"]) && !empty($data["password"])) {
    $username = $conn->real_escape_string($data["username"]);
    $password = $data["password"];

    // Kiểm tra tài khoản trong database
    $stmt = $conn->prepare("SELECT id_account, password FROM account WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_account, $hashed_password);
        $stmt->fetch();

        // Xác thực mật khẩu
        if (password_verify($password, $hashed_password)) {
            $accessToken = bin2hex(random_bytes(32)); // Tạo access_token
            $tokenExpiry = date("Y-m-d H:i:s", strtotime("+1 day")); // Hết hạn sau 1 ngày

            // Lưu access_token vào database
            $stmt = $conn->prepare("UPDATE users SET access_token = ? WHERE id_account = ?");
            $stmt->bind_param("si", $accessToken, $id_account);
            $stmt->execute();
            
            echo json_encode([
                "status" => "success",
                "message" => "Đăng nhập thành công",
                "access_token" => $accessToken
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Sai mật khẩu"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Tài khoản không tồn tại"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu username hoặc password"]);
}

$conn->close();
?>
