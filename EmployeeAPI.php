<?php

require_once "Database.php";

header("Content-Type: application/json");

$database = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {

    $input = json_decode(file_get_contents("php://input"), true);

    if (
        !isset($input["employeeId"]) ||
        !isset($input["employeeName"]) ||
        !isset($input["employeeType"])
    ) {
        echo json_encode([
            "status" => "error",
            "message" => "Required fields are missing."
        ]);
        exit;
    }

    $employeeId = $conn->real_escape_string($input["employeeId"]);
    $employeeName = $conn->real_escape_string($input["employeeName"]);
    $employeeType = $conn->real_escape_string($input["employeeType"]);

    $shiftStart = isset($input["shiftStart"]) ? $conn->real_escape_string($input["shiftStart"]) : null;
    $shiftEnd   = isset($input["shiftEnd"]) ? $conn->real_escape_string($input["shiftEnd"]) : null;

    // Check if employee already exists
    $check = $conn->query("SELECT * FROM employees WHERE employeeId='$employeeId'");

    if ($check->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Employee ID already exists."
        ]);
        exit;
    }

    $sql = "INSERT INTO employees
            (employeeId, employeeName, employeeType, shiftStart, shiftEnd)
            VALUES (
                '$employeeId',
                '$employeeName',
                '$employeeType',
                " . ($shiftStart ? "'$shiftStart'" : "NULL") . ",
                " . ($shiftEnd ? "'$shiftEnd'" : "NULL") . "
            )";

    if ($conn->query($sql)) {
        echo json_encode([
            "status" => "success",
            "message" => "Employee added successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $conn->error
        ]);
    }
}