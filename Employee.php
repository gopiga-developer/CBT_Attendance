<?php

class Employee
{
    protected $employeeId;
    protected $employeeName;

    public function __construct($_employee_id,$_employee_name) 
    {
        $this->employeeId = $_employee_id;
        $this->employeeName = $_employee_name;
    }

    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    public function getEmployeeName()
    {
        return $this->employeeName;
    }
    // Method Overriding
    public function getEmployeeCategory()
    {
        return "Employee";
    }
    public function calculateWorkingHours(string $_in_time, string $_out_time) {
        $inTime = str_replace(".", ":", $_in_time);
        $outTime = str_replace(".", ":", $_out_time);

        return (strtotime($outTime) - strtotime($inTime)) / 3600;
    }

    public function isLateArrival(string $_in_time, string $_shift_start) {
        $inTime = str_replace(".", ":", $_in_time);
        $shiftStart = str_replace(".", ":", $_shift_start);

        return strtotime($inTime) > strtotime($shiftStart);
    }

    public function isEarlyLogout(string $_out_time,string $_shift_end) {
        $outTime = str_replace(".", ":", $_out_time);
        $shiftEnd = str_replace(".", ":", $_shift_end);

        return strtotime($outTime) < strtotime($shiftEnd);
    }

}