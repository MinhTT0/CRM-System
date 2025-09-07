<?php
header('Content-Type: application/json');
include "db.php";

// Bật chế độ báo lỗi dưới dạng exception
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($conn->connect_error) {
        throw new Exception("Lỗi kết nối cơ sở dữ liệu");
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        $data = $_POST;
    }

    if (!empty($data["id_type"])) {
        $id_type = $conn->real_escape_string($data["id_type"]);

        $stmt = $conn->prepare("DELETE FROM customertype WHERE id_type = ?");
        $stmt->bind_param("s", $id_type);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Xoá loại khách hàng thành công"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu id_type"]);
    }

} catch (mysqli_sql_exception $e) {
    // Cụ thể lỗi từ MySQL (ví dụ: ràng buộc khóa ngoại)
    echo json_encode(["status" => "error", "message" => "Không thể xoá: Loại khách hàng đang được sử dụng"]);
} catch (Exception $e) {
    // Lỗi khác
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
