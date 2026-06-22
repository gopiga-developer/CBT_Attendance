<?php

require_once "Employee.php";
/**Manages employee attendance operations.*/

class AttendanceManager
{
    private $employees;
    /* Load employee data from JSON file.*/

    public function __construct()
    {
        $data = json_decode( file_get_contents("masterlist.json"), true);

        foreach ($data as $emp) 
        {
            $this->employees[] = new Employee(
                $emp["employeeId"],
                $emp["employeeName"],
                $emp["shiftStart"],
                $emp["shiftEnd"]
            );
        }
    }
/* Start attendance process. */
    public function start()
    {
        $employee = $this->getEmployee();

        $inTime = $this->getInTime();

        $outTime = $this->getOutTime();

        $workingHours = $this->calculateWorkingHours($inTime, $outTime );

        $lateArrival = $this->isLateArrival( $inTime, $employee->getShiftStart());

        $earlyLogout = $this->isEarlyLogout(  $outTime, $employee->getShiftEnd() );

        $this->saveAttendance($employee, $inTime, $outTime, $workingHours, $lateArrival, $earlyLogout );

        $this->displayResult($employee, $workingHours, $lateArrival, $earlyLogout );
        $this->viewEmployeeTransactions();
    }

    public function getEmployee()
    {
        $attempts = 0;

        while ($attempts < 3) {

            $employeeId = readline( "Enter Employee ID : ");

            foreach ($this->employees as $employee) 
            {

                if ( $employee->getEmployeeId() === $employeeId) 
                {
                    return $employee;
                }
            }

            $attempts++;

            echo "Invalid Employee ID. Attempts Left : ". (3 - $attempts). "\n";
        }

        echo "Failed to Login\n";
        exit;
    }
    public function getDate()
    {
    return readline("Enter Date (YYYY-MM-DD): ");
    }
    public function getInTime()
    {
        return readline("Enter In Time (HH:MM) : " );
    }

    public function getOutTime()
    {
        return readline( "Enter Out Time (HH:MM) : " );
    }

    public function calculateWorkingHours($inTime, $outTime) 
    {
        return ( strtotime($outTime) - strtotime($inTime) ) / 3600;
    }

    public function isLateArrival( $inTime, $shiftStart) 
    {
        return strtotime($inTime) > strtotime($shiftStart);
    }

    public function isEarlyLogout( $outTime, $shiftEnd) 
    {
        return strtotime($outTime) < strtotime($shiftEnd);
    }
    public function viewEmployeeTransactions()
{
    $employeeId = readline("Enter Employee ID : ");

    if (!file_exists("attendance.json"))
    {
        echo "No attendance records found.\n";
        return;
    }

    $attendance = json_decode(
        file_get_contents("attendance.json"),
        true
    );

    $found = false;

    foreach ($attendance as $record)
    {
        if ($record["employeeId"] === $employeeId)
        {
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

    if (!$found)
    {
        echo "No transactions found for Employee ID: "
             . $employeeId . "\n";
    }
}
    public function saveAttendance($employee, $inTime, $outTime, $workingHours, $lateArrival, $earlyLogout ) {

        $record = [
            "employeeId" => $employee->getEmployeeId(),
            "employeeName" => $employee->getEmployeeName(),
            "date" => date("Y-m-d"),
            "inTime" => $inTime,
            "outTime" => $outTime,
            "workingHours" => $workingHours,
            "lateArrival" => $lateArrival ? "Yes" : "No",
            "earlyLogout" => $earlyLogout ? "Yes" : "No"
        ];

    

        $attendance = [];

        if (file_exists( "attendance.json" )
        ) {
            $attendance =json_decode( file_get_contents( "attendance.json" ), true );
        }

        $attendance[] = $record;

        file_put_contents( "attendance.json", json_encode( $attendance, JSON_PRETTY_PRINT ));
    }

    public function displayResult($employee, $workingHours, $lateArrival, $earlyLogout) 
    {

        echo "\nEmployee : " . $employee->getEmployeeName() ."\nDate : " . date("Y-m-d").  "\nWorking Hours : "
            . $workingHours . "\nLate Arrival : ". ( $lateArrival ? "Yes": "No" ) . "\nEarly Logout : "
            . ( $earlyLogout ? "Yes" : "No" );
    }
}