<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// Kiểm tra đầu vào hợp lệ
if (!empty($data["id_type"]) && !empty($data["name"]) && !empty($data["description"])) {
    $id_type = intval($data["id_type"]);
    $name = trim($data["name"]);
    $description = trim($data["description"]);
    $sql = "UPDATE customertype SET name = ?, description = ? where id_type = ?";   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $description, $id_type);  
    $stmt->execute(); 
    $stmtres = $conn->prepare("SELECT * FROM customertype WHERE id_type = ?");
    $stmtres->bind_param("i", $id_type);
    $stmtres->execute();
    $resuser = $stmtres->get_result();

    $rowuser = $resuser->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $rowuser, "message" => "Cập nhật thành công"]);
    $stmtres->close();
    $stmt->close();        
    
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$conn->close();
?>
