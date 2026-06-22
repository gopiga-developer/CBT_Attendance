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

    public function saveAttendance($employee, $inTime, $outTime, $workingHours, $lateArrival, $earlyLogout ) {

        $record = [
            "employeeId" => $employee->getEmployeeId(),
            "employeeName" => $employee->getEmployeeName(),
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

        echo "\nEmployee : " . $employee->getEmployeeName() .  "\nWorking Hours : "
            . $workingHours . "\nLate Arrival : ". ( $lateArrival ? "Yes": "No" ) . "\nEarly Logout : "
            . ( $earlyLogout ? "Yes" : "No" );
    }
}