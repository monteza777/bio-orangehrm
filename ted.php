<?php
include('db.php');

$conn2 = new COM("ADODB.Connection");
$conn2->Open('Provider=VFPOLEDB.1;Data Source="c:\xampp\htdocs\uploadtoorange\TERMINAL.DBF";Collating Sequence=machine;'); //c:\xampp\htdocs\uploadtoorange

$rs = $conn2->Execute("SELECT * FROM TERMINAL INTO cursor TERM readwrite where line = '0' and YY !='2017'");
$i = 1;
while (!$rs->EOF) { 
	$fv = $rs->Fields("serno");
	$fv1 = $rs->Fields("no");
	$fv2 = $rs->Fields("yy");
	$fv3 = $rs->Fields("mm");
	$fv4 = $rs->Fields("dd");
	$fv5 = $rs->Fields("hour");
	$fv6 = $rs->Fields("min");
	$fv7 = $rs->Fields("inout");

		$serno =$fv->value;
		$no =$fv1->value;
		$yy =$fv2->value;
		$mm =$fv3->value;
		$dd =$fv4->value;
		$hour =$fv5->value;
		$min =$fv6->value;
		$state = trim($fv7->value,' ');
		$ymd = $yy.'-'.$mm.'-'.$dd.' '.$hour.'-'.$min;
		
		if ($state=="OUT"){
        $d=mktime($hour, $min, 00, $mm, $dd, $yy);
        $date=date_create($yy.'-'.$mm.'-'.$dd.' '.$hour.':'.$min);
        date_sub($date,date_interval_create_from_date_string("12 hours"));
        $yy=date_format($date,"Y");
        $mm=date_format($date,"m");
        $dd=date_format($date,"d");
		}
		$uniqueCode = $yy.$mm.$dd.$no.$state;


	$sqlemps = "SELECT emp_number,jaz_emp_no from hs_hr_employee where jaz_emp_no = '$no'";
			$sqlEmpsResult = $conn->query($sqlemps);
			while($row = mysqli_fetch_assoc($sqlEmpsResult)){
				$sqlEmpId = $row['emp_number'];

	$sql = "INSERT INTO hs_hr_timeattendance_log (hardware_user_id,employee_id,time_log,uploaded,state,ymdis) values ('$no','$sqlEmpId','$ymd','0','$state','$uniqueCode')
			on duplicate key update time_log = '$ymd'";
	$conn->query($sql);	
	
	// echo $state. '<br/>';
			if($conn->query($sql)){
				$query = "UPDATE TERMINAL set LINE = '1' where line = '0'";
				if($conn2->Execute($query)){
					echo 'updated';
				}
			}
		}
	$i++;
		if($i==10000){
			break;
		}else{
			$rs->MoveNext();
		}	
} 
$rs->Close();
