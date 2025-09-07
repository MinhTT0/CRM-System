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

if (!empty($data["id_user"])) {
    $res = [];
    $id_user = $conn->real_escape_string($data["id_user"]);
    $stmtupdate = $conn->prepare("
    UPDATE work
    SET status = 2
    WHERE (NOW() + INTERVAL 7 HOUR) > duedate AND id_user = ? AND status = 0
");

    $stmtupdate->bind_param("s", $id_user);
    $stmtupdate->execute();
    
    // Kiểm tra tài khoản trong database
    $stmt = $conn->prepare("SELECT * FROM work WHERE id_user = ?");
    $stmt->bind_param("s", $id_user);
    $stmt->execute();
    $resuser = $stmt->get_result();
    if ($resuser->num_rows > 0) {
        while ($row = $resuser->fetch_assoc()) {
            $res[] = $row;
        }
    }
    $stmt->close();
    echo json_encode(["status" => "success", "data" => $res, "message" => "Get information successfully"]);
} else {
    if (!empty($data["id_business"])) {
        $res = [];
        $id_business = $conn->real_escape_string($data["id_business"]);

        $stmtus = $conn->prepare("SELECT * FROM account INNER JOIN users ON account.id_account = users.id_account WHERE account.id_business = ?");
        $stmtus->bind_param("s", $id_business);
        $stmtus->execute();
        $resus = $stmtus->get_result();
        if ($resus->num_rows > 0) {
            while ($rowus = $resus->fetch_assoc()) {
                $stmtupdate = $conn->prepare("
                UPDATE work
                SET status = 2
                WHERE (NOW() + INTERVAL 7 HOUR) > duedate AND id_user = ? AND status = 0");

                $stmtupdate->bind_param("s", $rowus['id_user']);
                $stmtupdate->execute();
            }
        }

        
        
        // Kiểm tra tài khoản trong database
        $stmt = $conn->prepare("SELECT work.id_work, work.content, work.createdate, work.duedate, work.status, work.id_user, user.name
        FROM work 
        INNER JOIN users ON work.id_user = users.id_user INNER JOIN account ON users.id_account = account.id_account
        WHERE account.id_business = ?");
        $stmt->bind_param("s", $id_business);
        $stmt->execute();
        $resuser = $stmt->get_result();
        if ($resuser->num_rows > 0) {
            while ($row = $resuser->fetch_assoc()) {
                $res[] = $row;
            }
        }
        $stmt->close();
        echo json_encode(["status" => "success", "data" => $res, "message" => "Get information successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error!"]);
    }
}

$conn->close();
?>
