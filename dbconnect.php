<?php
$conn2 = mysqli_connect("localhost", "root", "","orangehrm_mysql");
if (!$conn2) {
    die("Connection failed: " . mysqli_connect_error());
}

$conn = new COM("ADODB.Connection");
$conn->Open('Provider=VFPOLEDB.1;Data Source="c:\xampp\htdocs\uploadtoorange\TERMINAL.DBF";Collating Sequence=machine;');

$rs = $conn->Execute("SELECT * FROM TERMINAL INTO cursor TERM readwrite where line = '1' and YY !='2017'");
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
		$state =$fv7->value;
		$ymd = $yy.'-'.$mm.'-'.$dd.' '.$hour.'-'.$min;
		$uniqueCode = $yy.$mm.$dd.$no.$state;
	$sqlemps = "SELECT emp_number,jaz_emp_no from hs_hr_employee where jaz_emp_no = '$no'";
			$sqlEmpsResult = $conn2->query($sqlemps);
			while($row = mysqli_fetch_assoc($sqlEmpsResult)){
				$sqlEmpId = $row['emp_number'];

	$sql = "INSERT INTO hs_hr_timeattendance_log (hardware_user_id,employee_id,time_log,uploaded,state,ymdis) values ('$no','$sqlEmpId','$ymd','0','$state','$uniqueCode')
			on duplicate key update time_log = '$ymd'";
	$conn2->query($sql);	
	
	echo "INSERT INTO hs_hr_timeattendance_log (hardware_user_id,time_log,uploaded,state) values('$no','$ymd','0','$state')". "<br>";
			if($conn2->query($sql)){
				$query = "UPDATE TERMINAL set LINE = '' where line = '1'";
				if($conn->Execute($query)){
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

