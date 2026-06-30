<?php

require_once "Database.php";

header("Content-Type: application/json");

$database  = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {

    if (isset($_GET['employeeId'])) {

        $employeeId = intval($_GET['employeeId']);

        $sql = "SELECT attendance.id, employees.employee_id, employees.employee_name, employees.employee_type, attendance.attendance_date, attendance.in_time, attendance.out_time, attendance.working_hours, attendance.later_arrival, attendance.early_logout FROM attendance INNER JOIN employees ON attendance.employee_id = employees.id";

        $result = $conn->query($sql);

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success","data" => $data]);

    } else {

        $sql = "SELECT attendance.id, employees.employee_id, employees.employee_name, employees.employee_type, attendance.attendance_date, attendance.in_time, attendance.out_time, attendance.working_hours, attendance.later_arrival, attendance.early_logout FROM attendance INNER JOIN employees ON attendance.employee_id = employees.id";
        $result = $conn->query($sql);
 
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success","data" => $data]);
    }
}