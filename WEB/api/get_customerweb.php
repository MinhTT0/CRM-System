<?php
header('Content-Type: application/json');
include "db.php";
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Lỗi kết nối cơ sở dữ liệu"]));
}
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (!empty($data["id_business"])) {
    $id_business = $conn->real_escape_string($data["id_business"]);
    $customer = []; 
    // Kiểm tra tài khoản trong database
    $stmt = $conn->prepare("SELECT id_customer, name, phone, email, birthday, address, gender, id_type, more FROM customerinfo WHERE id_business = ?");
    $stmt->bind_param("s", $id_business);
    $stmt->execute();
    $resuser = $stmt->get_result();
    if ($resuser->num_rows > 0) {
        while ($row = $resuser->fetch_assoc()) {
            $more_details = [];

            if (!empty($row['more'])) {
                $more_items = explode(",", $row['more']); // Tách dữ liệu trong cột "more"
    
                // Lấy danh sách trường từ bảng custominfo theo id_business
                $stmt_custom = $conn->prepare("SELECT name FROM custominfo WHERE id_business = ? ORDER BY id_custominfo ASC");
                $stmt_custom->bind_param("s", $id_business);
                $stmt_custom->execute();
                $res_custom = $stmt_custom->get_result();
    
                $custom_fields = [];
                while ($custom = $res_custom->fetch_assoc()) {
                    $custom_fields[] = $custom['name'];
                }
                $stmt_custom->close();
    
                // Kết hợp dữ liệu từ "more" với các trường trong "custominfo"
                foreach ($more_items as $index => $value) {
                    if (isset($custom_fields[$index])) { // Đảm bảo không bị lỗi index
                        $more_details[] = [
                            "key" => $custom_fields[$index],
                            "value" => trim($value) // Loại bỏ khoảng trắng thừa
                        ];
                    }
                }
            }
            $row['more'];
            $row['more_details'] = $more_details;
            $customer[] = $row; // Thêm từng user vào mảng
        }
    }
    $stmt->close();
    echo json_encode(["status" => "success", "data" => $customer, "message" => "Get information successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error!"]);
}

$conn->close();
?>
