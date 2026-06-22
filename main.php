<?php

require_once "AttendanceManager.php";

class main{
    public function run()
    {
        $attendanceManager =
        new AttendanceManager();

        $attendanceManager->start();
    }
}

$obj = new main();
$obj->run();