<?php

class Employee
{
    protected $id;
    protected $employeeId;
    protected $employeeName;

    public function __construct($id, $employeeId, $employeeName)
    {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->employeeName = $employeeName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    public function getEmployeeCategory()
    {
        return "Employee";
    }

    public function calculateWorkingHours(string $_in_time, string $_out_time)
    {
        $inTime = str_replace(".", ":", $_in_time);
        $outTime = str_replace(".", ":", $_out_time);

        return (strtotime($outTime) - strtotime($inTime)) / 3600;
    }

    public function isLateArrival(string $_in_time, string $_shift_start)
    {
        $inTime = str_replace(".", ":", $_in_time);
        $shiftStart = str_replace(".", ":", $_shift_start);

        return strtotime($inTime) > strtotime($shiftStart);
    }

    public function isEarlyLogout(string $_out_time, string $_shift_end)
    {
        $outTime = str_replace(".", ":", $_out_time);
        $shiftEnd = str_replace(".", ":", $_shift_end);

        return strtotime($outTime) < strtotime($shiftEnd);
    }
}