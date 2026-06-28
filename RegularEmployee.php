<?php

require_once "Employee.php";

class RegularEmployee extends Employee
{
    private $shiftStart;
    private $shiftEnd;

    public function __construct($_employee_id, $_employee_name, $_shift_start, $_shift_end) {
        parent::__construct($_employee_id, $_employee_name);

        $this->shiftStart = $_shift_start;
        $this->shiftEnd = $_shift_end;
    }

    public function getShiftStart()
    {
        return $this->shiftStart;
    }

    public function getShiftEnd()
    {
        return $this->shiftEnd;
    }
    public function getEmployeeCategory()
    {
        return "Regular Employee";
    }
}