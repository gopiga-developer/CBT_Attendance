<?php

require_once "Database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    $employee_id = $data["employee_id"];
    $in_time = $data["in_time"];
    $out_time = $data["out_time"];

    $result = $conn->query("SELECT * FROM employees WHERE employee_id = '$employee_id'");

    if ($result->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Employee not found"]);
        exit;
    }

    $employee = $result->fetch_assoc();

    $check = $conn->query("SELECT * FROM attendance WHERE employee_id='{$employee['id']}' AND attendance_date=CURDATE()");

    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Already marked"]);
        exit;
    }

    $working_hours = (strtotime($out_time) - strtotime($in_time)) / 3600;

    $late = 0;
    $early = 0;

    if ($employee["employee_type"] == "Regular") {

        if ($in_time > $employee["shift_start"]) {
            $late = 1;
        }

        if ($out_time < $employee["shift_end"]) {
            $early = 1;
        }
    }

    $sql = "INSERT INTO attendance(employee_id, attendance_date, in_time, out_time, working_hours, later_arrival, early_logout)
    VALUES('{$employee['id']}', CURDATE(), '$in_time', '$out_time', '$working_hours', '$late', '$early')";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Attendance marked"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}