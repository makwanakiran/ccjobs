<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 

$page = "welcome";

include("header.php"); 

?>

<div class="page-header">
<h1>Job Management System</h1>
</div>


<!-- <a style="margin-top: 2%;" href="jobs.php" class="btn btn-primary"><span class="glyphicon glyphicon-wrench" style="font-size: 120px; "></span><br>Jobs</a>
<a style="margin-top: 2%;" href="schedule.php" class="btn btn-info"><span class="glyphicon glyphicon-calendar" style="font-size: 120px; "></span><br>Schedule</a>
<a style="margin-top: 2%;" href="delivery.php" class="btn btn-primary"><span class="glyphicon glyphicon-road" style="font-size: 120px; "></span><br>Delivery</a>
<a style="margin-top: 2%;" href="reports-assemblers.php" class="btn btn-info"><span class="glyphicon glyphicon-list-alt" style="font-size: 120px; "></span><br>Reports</a>
<a style="margin-top: 2%;" href="users.php" class="btn btn-primary"><span class="glyphicon glyphicon-user" style="font-size: 120px; "></span><br>Users</a>
<a style="margin-top: 2%;" href="admin_users.php" class="btn btn-info"><span class="glyphicon glyphicon-cog" style="font-size: 120px; "></span><br>Admin Users</a>
<a style="margin-top: 2%;" href="logout.php" class="btn btn-primary"><span class="glyphicon glyphicon-log-out" style="font-size: 120px; "></span><br>Log Out</a> -->



<?php include("footer.php"); ?>