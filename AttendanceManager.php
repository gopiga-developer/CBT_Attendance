<?php

require_once "Employee.php";
require_once "RegularEmployee.php";
require_once "ContractEmployee.php";
require_once "Database.php";


/**Manages employee attendance operations.*/
class AttendanceManager extends Database
{
    private array $employees = [];

    /**
     * Load employee data from JSON file.
     */
    public function __construct()
    {
        parent::__construct();
        $query = "SELECT * FROM employees";
        $result = $this->connection->query($query);

        while ($employee = $result->fetch_assoc()) {

    if (str_starts_with($employee["employeeId"], "EMP")) {

        $this->employees[$employee["employeeId"]] =
            new RegularEmployee(
                $employee["id"],
                $employee["employeeId"],
                $employee["employeeName"],
                $employee["shiftStart"],
                $employee["shiftEnd"]
            );

    } else {

        $this->employees[$employee["employeeId"]] =
            new ContractEmployee(
                $employee["id"],
                $employee["employeeId"],
                $employee["employeeName"]
            );
    }
    }  
    }
    /* Start attendance process. */
    public function start()
    {
        if (!$this->validateWeeklyOff()) {
            return;
        }

        $employee = $this->getEmployee();

        if (!$this->validateAttendance($employee->getEmployeeId())) {
            return;
        }

        $in_time = $this->getInput("Enter In Time (HH:MM) : ");

        $out_time = $this->getInput("Enter Out Time (HH:MM) : ");

        // Common calculation for both Regular and Contract Employee
        $working_hours = $employee->calculateWorkingHours($in_time, $out_time);

        if ($employee instanceof ContractEmployee) {
            // Contract employees don't have fixed shift timings
            $late_arrival = false;
            $early_logout = false;
        } else {

            // Regular employees only
            $late_arrival = $employee->isLateArrival($in_time, $employee->getShiftStart());

            $early_logout = $employee->isEarlyLogout($out_time, $employee->getShiftEnd());
        }

        $this->saveAttendance($employee, $in_time, $out_time, $working_hours, $late_arrival, $early_logout);

        $this->displayResult($employee, $working_hours, $late_arrival, $early_logout);

        return;
    }
    public function showMenu()
    {
        while (true) {

            echo "\n========== Attendance System ==========\n";
            echo "1. Mark Attendance\n";
            echo "2. View Employee Transactions\n";
            echo "3. Exit\n";

            $choice = trim(readline("Enter Choice : "));

            switch ($choice) {

                case "1":
                    $this->start();

                    break;

                case "2":
                    $this->viewEmployeeTransactions();

                    break;

                case "3":
                    exit("\nThank You\n");

                default:
                    echo "\nInvalid Choice\n";
            }
        }
    }

    public function validateWeeklyOff()
    {
        $day = date("l");

        if ($day === "Saturday" || $day === "Sunday") {

            echo "\nToday is {$day}.\n";
            echo "Weekly Off. Attendance cannot be marked.\n";

            return false;
        }

        return true;
    }

    public function validateAttendance(string $_employee_id)
    {
        $query = "SELECT * FROM attendance 
              WHERE employeeId='$_employee_id' 
              AND attendanceDate=CURDATE()";

        $result = $this->connection->query($query);

        if ($result->num_rows > 0) {

            echo "\nAttendance already marked for today.\n";

            return false;
        }

        return true;
    }
    public function getEmployee()
    {
        $attempts = 0;

        while ($attempts < 3) {
            $employee_id = trim(readline("\nEnter Employee ID : "));

            if (isset($this->employees[$employee_id])) {
                echo "Valid Employee\n";
                return $this->employees[$employee_id];
            }

            $attempts++;

            echo "Invalid Employee ID. Attempts Left : " . (3 - $attempts) . "\n";
        }

        echo "\nFailed To Login\n";
        exit;
    }
    public function getInput(string $_message)
    {
        $attempts = 0;

        while ($attempts < 3) {
            $time = trim(readline($_message));

            if (preg_match("/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $time)) {
                return $time;
            }

            $attempts++;

            echo "Invalid Time Format. Attempts Left : " . (3 - $attempts) . "\n";
        }

        echo "\nUnable to store your attendance.\n";
        exit;
    }
   public function viewEmployeeTransactions()
    {
    $employeeId = readline("Enter Employee ID: ");

    // Get employee details
    $employeeQuery = "SELECT * FROM employees WHERE employeeId='$employeeId'";
    $employeeResult = $this->connection->query($employeeQuery);
    $employee = $employeeResult->fetch_assoc();

    if (!$employee) {
        echo "Employee not found\n";
        return;
    }

    // Get attendance details
    $attendanceQuery = "SELECT * FROM attendance WHERE employeeId='$employeeId'";
    $attendanceResult = $this->connection->query($attendanceQuery);

    if ($attendanceResult->num_rows == 0) {
        echo "No attendance records found\n";
        return;
    }

    while ($record = $attendanceResult->fetch_assoc()) {

        echo "\n------------------------\n";
        echo "Date                : " . $record["attendanceDate"] . "\n";
        echo "Employee ID         : " . $record["employeeId"] . "\n";
        echo "Employee Name       : " . $employee["employeeName"] . "\n";
        echo "Employee Category   : " . $employee["employeeType"] . "\n";
        echo "In Time             : " . $record["inTime"] . "\n";
        echo "Out Time            : " . $record["outTime"] . "\n";
        echo "Working Hours       : " . $record["workingHours"] . "\n";
        echo "Late Arrival        : " . ($record["lateArrival"] ? "Yes" : "No") . "\n";
        echo "Early Logout        : " . ($record["earlyLogout"] ? "Yes" : "No") . "\n";
    }
    }

    public function saveAttendance($_employee, string $_in_time, string $_out_time, float $_working_hours, bool $_late_arrival, bool $_early_logout)
    {
        $lateArrival = $_late_arrival ? 1 : 0;
        $earlyLogout = $_early_logout ? 1 : 0;

        $query = "INSERT INTO attendance
        (employeeId, attendanceDate, inTime, outTime, workingHours, lateArrival, earlyLogout)
         VALUES ( '{$_employee->getId()}', CURDATE(), '$_in_time', '$_out_time', $_working_hours, $lateArrival, $earlyLogout)";

        $this->connection->query($query);

        echo "\nAttendance marked successfully.\n";
    }

    public function displayResult($_employee, float $_working_hours, bool $_late_arrival, bool $_early_logout)
    {
        echo "Employee : " . $_employee->getEmployeeName() . "\n";
        "Employee Category : " . $_employee->getEmployeeCategory() . "\n";
        "Date : " . date("Y-m-d") . "\n";
        "Working Hours : " . $_working_hours . "\n";
        "Late Arrival : " . ($_late_arrival ? "Yes" : "No") . "\n";
        "Early Logout : " . ($_early_logout ? "Yes" : "No");
    }
}
