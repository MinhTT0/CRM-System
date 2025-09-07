<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// Kiểm tra đầu vào hợp lệ
if (!empty($data["id_measurement"]) && !empty($data["name"]) && !empty($data["description"])) {
    $id_measurement = intval($data["id_measurement"]);
    $name = trim($data["name"]);
    $description = trim($data["description"]);
    $sql = "UPDATE measurement SET name = ?, description = ? where id_measurement = ?";   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $description, $id_measurement);  
    $stmt->execute(); 
    $stmtres = $conn->prepare("SELECT * FROM measurement WHERE id_measurement = ?");
    $stmtres->bind_param("i", $id_measurement);
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
