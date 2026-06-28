<?php

require_once "FileManager.php";
require_once "Employee.php";
require_once "RegularEmployee.php";
require_once "ContractEmployee.php";


/**Manages employee attendance operations.*/
class AttendanceManager extends FileManager
{
    private array $employees = [];
    private array $attendance = [];

    /**
     * Load employee data from JSON file.
     */
    public function __construct()
    {
        $data = $this->readJson("masterdetails.json");

        foreach ($data as $employee) {

            if (str_starts_with($employee["employeeId"], "EMP")) {

                $this->employees[$employee["employeeId"]] =
                    new RegularEmployee( $employee["employeeId"], $employee["employeeName"], $employee["shiftStart"],$employee["shiftEnd"]);

            } else {

                $this->employees[$employee["employeeId"]] =
                    new ContractEmployee( $employee["employeeId"], $employee["employeeName"]);
            }
        }

        $this->attendance = $this->readJson("attendance.json") ?? [];
    }
    /* Start attendance process. */
    public function start()
    {
        if (!$this->validateWeeklyOff()) {
            return;
        }

        $employee = $this->getEmployee();

        if (!$this->validateAttendance($this->attendance, $employee->getEmployeeId())) {
            return;
        }
        $this->validateAttendance($this->attendance, $employee->getEmployeeId());

        $in_time = $this->getInput("Enter In Time (HH:MM) : ");

        $out_time = $this->getInput("Enter Out Time (HH:MM) : ");

        // Common calculation for both Regular and Contract Employee
        $working_hours = $employee->calculateWorkingHours($in_time,$out_time);

        if ($employee instanceof ContractEmployee) {
            // Contract employees don't have fixed shift timings
            $late_arrival = false;
            $early_logout = false;

        } else {

            // Regular employees only
            $late_arrival = $employee->isLateArrival($in_time, $employee->getShiftStart() );

            $early_logout = $employee->isEarlyLogout( $out_time, $employee->getShiftEnd());
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

    public function validateAttendance(array $_attendance, string $_employee_id) {
        $today = date("Y-m-d");

        foreach ($_attendance as $record) {

            if ($record["employeeId"] === $_employee_id && $record["date"] === $today) {

                echo "\nAttendance already marked for today.\n";

                return false;
            }
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
        $employeeId = readline("\nEnter Employee ID : ");

        if (!file_exists("attendance.json")) {

            echo "No attendance records found.\n";
            return;
        }
        $found = false;

        foreach ($this->attendance as $record) {

            if ($record["employeeId"] === $employeeId) {
                $found = true;

                echo "\n------------------------\n";
                echo "Date                : " . $record["date"] . "\n";
                echo "Employee ID         : " . $record["employeeId"] . "\n";
                echo "Employee Name       : " . $record["employeeName"] . "\n";
                echo "Employee Category   : " . $record["employeeCategory"] . "\n";
                echo "In Time             : " . $record["inTime"] . "\n";
                echo "Out Time            : " . $record["outTime"] . "\n";
                echo "Working Hours       : " . $record["workingHours"] . "\n";
                echo "Late Arrival        : " . $record["lateArrival"] . "\n";
                echo "Early Logout        : " . $record["earlyLogout"] . "\n";
            }
        }

        if (!$found) {

            echo "No transactions found for Employee ID: " . $employeeId . "\n";
        }
    }

    public function saveAttendance($_employee, string $_in_time, string $_out_time, float $_working_hours, bool $_late_arrival, bool $_early_logout)
    {
        $record = [
            "employeeId" => $_employee->getEmployeeId(),
            "employeeName" => $_employee->getEmployeeName(),
            "employeeCategory" => $_employee->getEmployeeCategory(),
            "date" => date("Y-m-d"),
            "inTime" => $_in_time,
            "outTime" => $_out_time,
            "workingHours" => $_working_hours,
            "lateArrival" => $_late_arrival ? "Yes" : "No",
            "earlyLogout" => $_early_logout ? "Yes" : "No",         
        ];

        $this->attendance[] = $record;

        $this->writeJson("attendance.json", $this->attendance);

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

