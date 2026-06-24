<?php

require_once "FileManager.php";
require_once "Employee.php";

/**Manages employee attendance operations.*/
class AttendanceManager extends FileManager
{
    private array $employees = [];

    /**
     * Load employee data from JSON file.
     */
    public function __construct()
    {
        $data = $this->readJson("masterdetails.json");

        foreach ($data as $employee) {
            $this->employees[$employee["employeeId"]] = new Employee(
                $employee["employeeId"],
                $employee["employeeName"],
                $employee["shiftStart"],
                $employee["shiftEnd"]
            );
        }
    }

    /* Start attendance process. */
    public function start()
    {
        $employee = $this->getEmployee();

        $this->validateAttendance($employee->getEmployeeId());

        $in_time = $this->getInput("Enter In Time (HH:MM) : ");

        $out_time = $this->getInput("Enter Out Time (HH:MM) : ");

        $working_hours = $this->calculateWorkingHours($in_time, $out_time);

        $late_arrival = $this->isLateArrival($in_time, $employee->getShiftStart());

        $early_logout = $this->isEarlyLogout($out_time, $employee->getShiftEnd());

        $this->saveAttendance($employee, $in_time, $out_time, $working_hours, $late_arrival, $early_logout);

        $this->displayResult($employee, $working_hours, $late_arrival, $early_logout);

        $this->viewEmployeeTransactions();
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

    public function validateAttendance(string $_employee_Id)
    {
        $attendance = $this->readJson("attendance.json") ?? [];

        $today = date("Y-m-d");

        foreach ($attendance as $record) {

            if ($record["employeeId"] === $_employee_Id && $record["date"] === $today) {
                echo "\nAttendance already marked for today.\n";
                $this->viewEmployeeTransactions();
                exit;
            }
        }
    }

    public function getInput(string $_message)
    {
        $attempts = 0;

        while ($attempts < 3) {
            $time = trim(readline($_message));

            if (preg_match("/^(0[0-9]|1[0-9]|2[3])\.[0-5][0-9]$/", $time)) {
                return $time;
            }

            $attempts++;

            echo "Invalid Time Format. Attempts Left : " . (3 - $attempts) . "\n";
        }

        echo "\nUnable to store your attendance.\n";
        exit;

    }
    public function calculateWorkingHours(string $_in_time, string $_out_time)
    {
        return (strtotime($_out_time) - strtotime($_in_time)) / 3600;
    }

    public function isLateArrival(string $_in_time, string $_shift_start)
    {
        return strtotime($_in_time) > strtotime($_shift_start);
    }

    public function isEarlyLogout(string $_out_time, string $_shift_end)
    {
        return strtotime($_out_time) < strtotime($_shift_end);
    }

    public function viewEmployeeTransactions()
    {
        $employeeId = readline("\nEnter Employee ID : ");

        if (!file_exists("attendance.json")) {

            echo "No attendance records found.\n";
            return;
        }

        $attendance = $this->readJson("attendance.json");

        $found = false;

        foreach ($attendance as $record) {

            if ($record["employeeId"] === $employeeId) {
                $found = true;

                echo "\n------------------------\n";
                echo "Date          : " . $record["date"] . "\n";
                echo "Employee ID   : " . $record["employeeId"] . "\n";
                echo "Employee Name : " . $record["employeeName"] . "\n";
                echo "In Time       : " . $record["inTime"] . "\n";
                echo "Out Time      : " . $record["outTime"] . "\n";
                echo "Working Hours : " . $record["workingHours"] . "\n";
                echo "Late Arrival  : " . $record["lateArrival"] . "\n";
                echo "Early Logout  : " . $record["earlyLogout"] . "\n";
            }
        }

        if (!$found) {

            echo "No transactions found for Employee ID: " . $employeeId . "\n";
        }
    }

    public function saveAttendance(Employee $_employee, string $_in_time, string $_out_time, float $_working_hours, bool $_late_arrival, bool $_early_logout)
    {
        $attendance = $this->readJson("attendance.json") ?? [];

        $record = [
            "employeeId" => $_employee->getEmployeeId(),
            "employeeName" => $_employee->getEmployeeName(),
            "date" => date("Y-m-d"),
            "inTime" => $_in_time,
            "outTime" => $_out_time,
            "workingHours" => $_working_hours,
            "lateArrival" => $_late_arrival ? "Yes" : "No",
            "earlyLogout" => $_early_logout ? "Yes" : "No"
        ];

        $attendance[] = $record;

        $this->writeJson("attendance.json", $attendance);

        echo "\nAttendance marked successfully.\n";
    }

    public function displayResult(Employee $_employee, float $_working_hours, bool $_late_arrival, bool $_early_logout)
    {
        echo "Employee : " . $_employee->getEmployeeName() . "\n";
        "Date : " . date("Y-m-d") . "\n";
        "Working Hours : " . $_working_hours . "\n";
        "Late Arrival : " . ($_late_arrival ? "Yes" : "No") . "\n";
        "Early Logout : " . ($_early_logout ? "Yes" : "No");
    }
}

