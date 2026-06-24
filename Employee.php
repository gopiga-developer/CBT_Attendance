<?php

class Employee
{
    private $employeeId;
    private $employeeName;
    private $shiftStart;
    private $shiftEnd;

    public function __construct($_employee_id, $_employee_name, $_shift_start, $_shift_end){
        $this->employeeId = $_employee_id;
        $this->employeeName = $_employee_name;
        $this->shiftStart = $_shift_start;
        $this->shiftEnd = $_shift_end;
    }
    public function setEmployeeId($_employee_id)
    {
        $this->employeeId = $_employee_id;
    }

    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    public function setEmployeeName($_employee_name)
    {
        $this->employeeName = $_employee_name;
    }

    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    public function setShiftStart($_shift_start)
    {
        $this->shiftStart = $_shift_start;
    }

    public function getShiftStart()
    {
        return $this->shiftStart;
    }

    public function setShiftEnd($_shift_end)
    {
        $this->shiftEnd = $_shift_end;
    }

    public function getShiftEnd()
    {
        return $this->shiftEnd;
    }
}