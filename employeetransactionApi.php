<?php

require_once "Database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!isset($_GET["employee_id"])) {
        echo json_encode(["status" => "error", "message" => "Employee ID is required."]);
        exit;
    }

    $employee_id = $_GET["employee_id"];
    $employee_name = $_GET["employee_name"] ?? "";


    $sql = "SELECT
                attendance.attendance_date,
                employees.employee_id,
                employees.employee_name,
                employees.employee_type,
                attendance.in_time,
                attendance.out_time,
                attendance.working_hours,
                attendance.later_arrival,
                attendance.early_logout
            FROM attendance
            INNER JOIN employees
            ON attendance.employee_id = employees.id
            WHERE employees.employee_id = '$employee_id'";

    if ($employee_name != "") {
        $sql .= " AND employees.employee_name LIKE '%$employee_name%'";
    }


    $result = $conn->query($sql);


    if (!$result) {
        echo json_encode(["status" => "error", "message" => $conn->error]);
        exit;
    }

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    if (count($data) == 0) {
        echo json_encode(["status" => "error", "message" => "No attendance found"]);
        exit;
    }

    echo json_encode(["status" => "success", "data" => $data]);
}
