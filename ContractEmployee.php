<?php

require_once "Employee.php";

class ContractEmployee extends Employee
{
    public function __construct($_employee_id, $_employee_name) {
        parent::__construct( $_employee_id, $_employee_name);
    }
    public function getEmployeeCategory() 
    {
        return "Contract Employee";
    }
}