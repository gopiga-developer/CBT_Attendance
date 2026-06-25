<?php

require_once "AttendanceManager.php";

class main
{
    public function run()
    {
        $attendanceManager = new AttendanceManager();
        $attendanceManager->showMenu();
    }
}

$obj = new main();
$obj->run();