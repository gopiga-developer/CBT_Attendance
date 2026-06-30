<?php 

require_once "Employee.php";

class ContractEmployee extends Employee
{
    public function __construct($id, $employeeId, $employeeName)
    {
        parent::__construct($id, $employeeId, $employeeName);
    }

    public function getEmployeeCategory()
    {
        return "Contract Employee";
    }
}