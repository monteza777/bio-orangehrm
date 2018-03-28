<?php
$conn2 = mysqli_connect("localhost", "root", "44wallst","orangehrm_mysql");
if (!$conn2) {
    die("Connection failed: " . mysqli_connect_error());
}

$LOG=explode('~',str_replace('%20',' ',$_SERVER['QUERY_STRING']));
$serno = $LOG[0];
$no = $LOG[1];
$name = $LOG[2];
$access = $LOG[3];
$yy = $LOG[4];
$mm = $LOG[5];
$dd = $LOG[6];
$hour = $LOG[7];
$min = $LOG[8];
$rev = $LOG[9];
$rid = $LOG[10];
$depart = $LOG[11];
$state = trim($LOG[12],' ');
$ymd = $yy.'-'.$mm.'-'.$dd.' '.$hour.':'.$min;
// $uniqueCode = $no.$yy.$mm.$dd.'.'.$state;

if ($state=="OUT"){
        $d=mktime($hour, $min, 00, $mm, $dd, $yy);
        $date=date_create($yy.'-'.$mm.'-'.$dd.' '.$hour.':'.$min);
        date_sub($date,date_interval_create_from_date_string("12 hours"));
        $yy=date_format($date,"Y");
        $mm=date_format($date,"m");
        $dd=date_format($date,"d");
                }
$uniqueCode = $yy.$mm.$dd.$no.'.'.$state;

$sqlemps = "SELECT emp_number,jaz_emp_no from hs_hr_employee where jaz_emp_no = '$no'";
                $sqlEmpsResult = $conn2->query($sqlemps);
                while($row = mysqli_fetch_assoc($sqlEmpsResult)){
                        $sqlEmpId = $row['emp_number'];	

                $sql = "INSERT INTO hs_hr_timeattendance_log (hardware_user_id,employee_id,time_log,uploaded,state,ymdis) values ('$no','$sqlEmpId','$ymd','0','$state','$uniqueCode')
                        on duplicate key update time_log = '$ymd'";
                $conn2->query($sql);	
                }

$conn2->close();
?>