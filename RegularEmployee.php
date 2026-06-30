<?php
class RegularEmployee extends Employee
{
    private $shiftStart;
    private $shiftEnd;

    public function __construct($id, $employeeId, $employeeName, $shiftStart, $shiftEnd)
    {
        parent::__construct($id, $employeeId, $employeeName);

        $this->shiftStart = $shiftStart;
        $this->shiftEnd = $shiftEnd;
    }

    public function getShiftStart()
    {
        return $this->shiftStart;
    }

    public function getShiftEnd()
    {
        return $this->shiftEnd;
    }
}