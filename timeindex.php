<?php
include('db.php');

?>
<!DOCTYPE html>
<html>
<head>
	<title>TIME ATTENDANCE</title>
	<meta http-equiv="refresh" content="30">
	<link href='style.css' rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<style>
	.gray{
	color: gray;
}

</style>
<body>
	<div class="container">
	<div class="row">
		<h3>IOX Time Attendance</h3>
		<h3 class="text-info pull-right" id="time"></h3>
		<div class="col-md-12">
			<table class="table table-responsive table-striped">
				<thead>
					<tr>
						<th class="text-info">Employee Name</th>
						<th>Sched Time-IN</th>
						<th>Actual-TimeIN</th>
						<th>Source</th>
					</tr>
				</thead>
				<tbody>
<?php $sql = "SELECT a.emp_firstname,a.emp_lastname,a.jaz_emp_no,a.emp_number,b.work_shift_id,c.start_time, DATE_SUB(NOW(), INTERVAL 1 HOUR) as min_1hour,DATE_ADD(NOW(), INTERVAL 1 HOUR) as add_1 FROM hs_hr_employee as a LEFT OUTER JOIN ohrm_employee_work_shift as b ON a.emp_number = b.emp_number LEFT JOIN ohrm_work_shift c ON b.work_shift_id = c.id where a.jaz_emp_no !=0  order by b.work_shift_id asc";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
  $fullname = $row['emp_firstname'].' '.$row['emp_lastname'];
  $sched_timein = $row['start_time']; 
  $empNumber = $row['emp_number'];
  $jazNo = $row['jaz_emp_no'];
  $min_1hour = date("H:i:s",strtotime($row['min_1hour']));
  $add_1 = date("H:i:s",strtotime($row['add_1']));
  date_default_timezone_set('Asia/Manila');
  $current_time = date("H:i");

  $sql2 = "SELECT punch_in_user_time,bio,DATE_SUB(punch_in_user_time, INTERVAL 30 MINUTE) as grace_period  from ohrm_attendance_record where employee_id = '$empNumber' and DATE_FORMAT(punch_in_user_time, '%Y-%m-%d') = CURDATE()";
  $result2 = mysqli_query($conn, $sql2);
  if(mysqli_num_rows($result2) > 0){
  	while($row2 = mysqli_fetch_assoc($result2)){
  		$punchin = date("H:i:s",strtotime($row2['punch_in_user_time']));
  		$grace_period = date("H:i:s",strtotime($row2['grace_period']));
  		$row2['bio'] == 1? $source ='BIO.M':$source='OHRM';?>

			<tr>
			<td style="color:<?php echo $grace_period > $sched_timein ? 'orange': 'green'; ?>;" class="lead text-uppercase"><?php echo $fullname; ?></td>
			<td class="lead"><?php echo $sched_timein; ?></td>
			<td class="lead"><?php echo $punchin; ?></td>
			<td class="lead"><?php echo $source; ?></td>
		
  		<?php
  	}

  }else{
  	
  	$punchin = '---';
  	$source = '---';
  	$status = '---'; 

  	$sql3 = "SELECT * from ohrm_leave where date = CURDATE() and emp_number = '$empNumber'";
              $result3 = mysqli_query($conn, $sql3);
             ?>
  	<tr>
			<td class="lead text-uppercase <?php echo mysqli_num_rows($result3) > 0 ? 'gray': ($min_1hour > $sched_timein ? 'text-danger': ($current_time > $sched_timein? 'tab blink':'black')); ?>"><?php echo $fullname; ?></td>
			<td class="lead"><?php echo $sched_timein; ?></td>
			<td class="lead"><?php echo $punchin; ?></td>
			<td class="lead"><?php echo $source; ?></td>
  	<?php
  		
  	}
} 
} ?>

					</tr>
				</tbody>
			</table>
		</div>
	</div>
	</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script type="text/javascript">
	var timestamp = '<?=time();?>';
	function updateTime(){
	  $('#time').html(Date(timestamp));
	  timestamp++;
	}
	$(function(){
	  setInterval(updateTime, 1000);
	});
</script>
</html>