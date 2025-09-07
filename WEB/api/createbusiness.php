<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// Kiểm tra đầu vào hợp lệ
if (!empty($data["id_businesstype"]) && !empty($data["name"]) && !empty($data["address"]) && !empty($data["phone"]) && !empty($data["email"]) && !empty($data["taxnumber"])) {
    
    $id_businesstype = intval($data["id_businesstype"]);
    $name = trim($data["name"]);
    $address = trim($data["address"]);
    $phone = trim($data["phone"]);
    $email = trim($data["email"]);
    $taxnumber = trim($data["taxnumber"]);

    // Kiểm tra định dạng email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit();
    }

    // Kiểm tra số điện thoại (ít nhất 10 chữ số)
    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        echo json_encode(["status" => "error", "message" => "Invalid phone number"]);
        exit();
    }

    // Chuẩn bị truy vấn an toàn
    $sql = "INSERT INTO business (id_businesstype, name, address, phone, email, taxnumber) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $id_businesstype, $name, $address, $phone, $email, $taxnumber);

    if ($stmt->execute()) {
        $last_id = $stmt->insert_id;
        
        // Lấy thông tin vừa insert
        $sqlres = "SELECT * FROM business WHERE id_business = ?";
        $stmt_res = $conn->prepare($sqlres);
        $stmt_res->bind_param("i", $last_id);
        $stmt_res->execute();
        $result = $stmt_res->get_result();
        $row = $result->fetch_assoc();

        echo json_encode(["status" => "success", "data" => $row, "message" => "Thêm doanh nghiệp thành công"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Insert failed"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$conn->close();
?>
