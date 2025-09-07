<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// Kiểm tra đầu vào hợp lệ
if (!empty($data["id_custominfo"]) && !empty($data["name"]) && !empty($data["type"]) && !empty($data["description"])) {
    $id_custominfo = intval($data["id_custominfo"]);
    $name = trim($data["name"]);
    $type = trim($data["type"]);
    $description = trim($data["description"]);
    $sql = "UPDATE custominfo SET name = ?, type = ?, description = ? where id_custominfo = ?";   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $type, $description, $id_custominfo);  
    $stmt->execute(); 
    $stmtres = $conn->prepare("SELECT * FROM custominfo WHERE id_custominfo = ?");
    $stmtres->bind_param("i", $id_custominfo);
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
