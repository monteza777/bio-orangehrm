<?php
include('db.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>TIME ATTENDANCE</title>
    <meta http-equiv="refresh" content="3">
</head>
<?php
$sql_log = "select CAST(hardware_user_id as unsigned) as empid,employee_id, WORKDAY, date_sub(MAX(time_log_in),interval 8 hour) as login_utc,
8 as login_offset,MAX(time_log_in) as login,date_sub(MAX(time_log_out),interval 8 hour) as logout_utc,8 as logout_offset, MAX(time_log_out) as logout,
 CONCAT('PUNCHED ',MAX(state)) as state from (select hardware_user_id, state, SUBSTRING_INDEX(REPLACE(TRIM(ymdis),' ','.'),'.',1) as WORKDAY,
 	case when state = 'IN' then time_log end as time_log_in,case when state = 'OUT' then time_log end as time_log_out,employee_id FROM hs_hr_timeattendance_log
 	 where hardware_user_id !='' and uploaded=0) as Q1 group by WORKDAY";

$resultTimeLogDate = $conn->query($sql_log);
 // looping for hs_hr_time_attendance_log
 while($row1 = mysqli_fetch_assoc($resultTimeLogDate)){
    
    $Employee_ID = $row1['employee_id'];
    $workday = $row1['WORKDAY'];
    $punchin_utc = $row1['login_utc'];
    $punchin_offset = $row1['login_offset'];
    $punchin = $row1['login'];
    $punchout_utc = $row1['logout_utc'];
    $punchout_offset = $row1['logout_offset'];
    $punchout = $row1['logout'];
    $state = $row1['state'];
    
            $sql_id = "SELECT last_id FROM hs_hr_unique_id WHERE table_name = 'ohrm_attendance_record'";
            $resultMaxAttendance = $conn->query($sql_id);
            while($row2 = mysqli_fetch_assoc($resultMaxAttendance)){
              $maxAttendanceId = $row2['last_id'] + 1; // + 1
            }
            // start save last ID to hs_hr_unique_id
            $sql_idup = "UPDATE hs_hr_unique_id SET last_id = " . $maxAttendanceId . " WHERE table_name = 'ohrm_attendance_record'";
            $conn->query($sql_idup);
            
            $sql_rec = "INSERT INTO ohrm_attendance_record " .
                         "(id, punch_in_note, employee_id, punch_in_utc_time, punch_in_user_time, punch_out_utc_time, punch_out_user_time, punch_in_time_offset, punch_out_time_offset,state,bio) VALUES " .
                         "(" . $maxAttendanceId . ",'".$workday."', " . $Employee_ID . ", '" . $punchin_utc . "','" . $punchin . "', '" . $punchout_utc . "','" . $punchout . "', '" . $punchin_offset . "','" . $punchout_offset . "','" . $state . "',1)";
            
            // start save it to ohrm_attendance_record            
            echo $sql_rec;
            $conn->query($sql_rec);
            if($sql_rec){
            	$sqlUpdate = "UPDATE hs_hr_timeattendance_log set uploaded = '1' where uploaded = '0' and employee_id = '$Employee_ID'";
            }
           $conn->query($sqlUpdate);    
      } // end loop for one employee

$conn->close();



?>