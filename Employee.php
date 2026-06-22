<?php

class Employee
{
    private $employeeId;
    private $employeeName;
    private $shiftStart;
    private $shiftEnd;

    public function __construct($employeeId, $employeeName, $shiftStart, $shiftEnd){
        $this->employeeId = $employeeId;
        $this->employeeName = $employeeName;
        $this->shiftStart = $shiftStart;
        $this->shiftEnd = $shiftEnd;
    }
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    public function setEmployeeName($employeeName)
    {
        $this->employeeName = $employeeName;
    }

    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    public function setShiftStart($shiftStart)
    {
        $this->shiftStart = $shiftStart;
    }

    public function getShiftStart()
    {
        return $this->shiftStart;
    }

    public function setShiftEnd($shiftEnd)
    {
        $this->shiftEnd = $shiftEnd;
    }

    public function getShiftEnd()
    {
        return $this->shiftEnd;
    }
}