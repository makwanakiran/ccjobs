<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//add a schedule entry
	if ($_POST['action'] == "add")
	{
		$userid = $_POST['inputUserID'];
		$ScheduleType = 2;
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$scheduledate = (!empty($_POST['inputScheduleDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputScheduleDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$insert_stmt = $mysqli->prepare("INSERT INTO tblSchedule (UserID, JobID, Description, ScheduleDate, Notes, ScheduleType) VALUES (?, ?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('sisssi', $userid, $jobid, $description, $scheduledate, $notes, $ScheduleType); 
		$insert_stmt->execute();
				

		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The schedule entry was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The schedule entry could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit a schedule entry
	if ($_POST['action'] == "edit")
	{
		$scheduleid = $_POST['scheduleid'];
		$scheduleresult = "SELECT * FROM tblSchedule WHERE ScheduleID = ".$scheduleid; 
		$scheduleresult_single = $mysqli->query($scheduleresult);
		$schedule_row = $scheduleresult_single->fetch_array();
		if ($schedule_row['ScheduleID'] != '' && $schedule_row['ScheduleID'] != 0)
		{
			$data['msg'] = "<div class='alert alert-success' role='alert'>The schedule entry was updated successfully.</div>";
			$data['ScheduleID'] = $schedule_row['ScheduleID'];
			$data['UserID'] = $schedule_row['UserID'];
			$data['Description'] = $schedule_row['Description'];
			$data['ScheduleDate'] = !empty($schedule_row['ScheduleDate']) ? date('d-m-Y',strtotime($schedule_row['ScheduleDate'])) : "";
			$data['Notes'] = $schedule_row['Notes'];
		}
		else
		{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $scheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//add a schedule entry
	if ($_POST['action'] == "update")
	{
		$scheduleid = $_POST['scheduleid'];
		$userid = $_POST['inputUserID'];
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$scheduledate = (!empty($_POST['inputScheduleDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputScheduleDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$update_stmt = $mysqli->prepare("UPDATE tblSchedule SET UserID = ?, JobID = ?, Description = ?, ScheduleDate = ?, Notes = ? WHERE ScheduleID = ?"); 
		$update_stmt->bind_param('sisssi', $userid, $jobid, $description, $scheduledate, $notes, $scheduleid); 
		$update_stmt->execute();
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $scheduleid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $scheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//move a schedule entry
	if ($_POST['action'] == "move"){
		
		$scheduleid = $_POST['scheduleid'];
		$userid = $_POST['userid'];
		$scheduledate = (!empty($_POST['scheduledate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['scheduledate']))) : NULL;

		$update_stmt = $mysqli->prepare("UPDATE tblSchedule SET UserID = ?, ScheduleDate = ? WHERE ScheduleID = ?"); 
		$update_stmt->bind_param('ssi', $userid, $scheduledate, $scheduleid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $scheduleid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $scheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//sort schedule entries
	if ($_POST['action'] == "sort"){
		if (isset($_POST['sortorder'])){
			$sortorder = $_POST['sortorder'];

			$sort = 1;
			foreach ($sortorder as $scheduleid){
				$update_stmt = $mysqli->prepare("UPDATE tblSchedule SET SortOrder = ? WHERE ScheduleID = ?"); 
				$update_stmt->bind_param('ii', $sort, $scheduleid); 
				$update_stmt->execute();

				$sort++;
			}
		}
	}

	//delete a schedule entry
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];

			$stmt = $mysqli->prepare("DELETE FROM tblSchedule WHERE ScheduleID = ? LIMIT 1");
			$stmt->bind_param("s", $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected schedule staff entry was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected schedule staff entry could not be deleted</div>";
				
			$stmt->close();
		}
	}

	//get job builder
	if ($_POST['action'] == "getbuilder"){
		if (isset($_POST['jobid'])){
			$jobid = $_POST['jobid'];
			
			if ($stmt = $mysqli->prepare("SELECT Builder FROM tblJob WHERE JobID = ? LIMIT 1")) { 
				$stmt->bind_param('i', $jobid);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($builder);
				$stmt->fetch();
			}

			if (isset($builder))
				echo $builder;

		}
	}
}

?>