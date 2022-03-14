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
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$scheduledate = (!empty($_POST['inputStoneDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputStoneDate']))) : NULL;
		$notes = $_POST['inputNotes'];
		$StoneType = 2;

		$insert_stmt = $mysqli->prepare("INSERT INTO tblStoneSchedule (UserID, JobID, Description, ScheduleDate, Notes, StoneType) VALUES (?, ?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('sisssi', $userid, $jobid, $description, $scheduledate, $notes, $StoneType); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1)
		{
			$data['msg'] = "<div class='alert alert-success' role='alert'>The stone staff entry was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";
		}
		else
		{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The stone staff entry could not be added</div>";
			$data['action'] = "add";
		}
		echo json_encode($data);
	}
	
	//edit a schedule entry
	if ($_POST['action'] == "edit")
	{
		$stoneid = $_POST['stoneid'];

		$stoneresult = "SELECT * FROM tblStoneSchedule WHERE StoneScheduleID = ".$stoneid; 
		$stoneresult_single = $mysqli->query($stoneresult);
		$stone_row = $stoneresult_single->fetch_array();
		
		if ($stone_row['StoneScheduleID'] != '' && $stone_row['StoneScheduleID'] != 0)
		{
			$data['msg'] = "<div class='alert alert-success' role='alert'>The stone staff entry was updated successfully.</div>";
			$data['StoneId'] = $stone_row['StoneScheduleID'];
			$data['UserId'] = $stone_row['UserID'];
			$data['Description'] = $stone_row['Description'];
			$data['StoneDate'] = !empty($stone_row['ScheduleDate']) ? date('d-m-Y',strtotime($stone_row['ScheduleDate'])) : "";
			$data['Notes'] = $stone_row['Notes'];
			$data['last_insert_id'] = $stoneid;
		}
		else
		{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The stone staff entry could not be updated.</div>";
			$data['last_insert_id'] = $stoneid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}


	if ($_POST['action'] == "update")
	{
		$stoneid = $_POST['stoneid'];
		$userid = $_POST['inputUserID'];
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$scheduledate = (!empty($_POST['inputStoneDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputStoneDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$update_stmt = $mysqli->prepare("UPDATE tblStoneSchedule SET UserID = ?, JobID = ?, Description = ?, ScheduleDate = ?, Notes = ? WHERE StoneScheduleID = ?"); 
		$update_stmt->bind_param('sisssi', $userid, $jobid, $description, $scheduledate, $notes, $stoneid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The stone schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $stoneid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The stone schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $stoneid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	
	//move a schedule entry
	/*if ($_POST['action'] == "move"){
		
		$stonescheduleid = $_POST['stonescheduleid'];
		$userid = $_POST['userid'];
		$scheduledate = (!empty($_POST['scheduledate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['scheduledate']))) : NULL;

		$update_stmt = $mysqli->prepare("UPDATE tblStoneSchedule SET UserID = ?, ScheduleDate = ? WHERE StoneScheduleID = ?"); 
		$update_stmt->bind_param('ssi', $userid, $scheduledate, $stonescheduleid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The stone schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $stonescheduleid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The stone schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $stonescheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//sort schedule entries
	if ($_POST['action'] == "sort"){
		if (isset($_POST['sortorder'])){
			$sortorder = $_POST['sortorder'];

			$sort = 1;
			foreach ($sortorder as $stonescheduleid){
				$update_stmt = $mysqli->prepare("UPDATE tblStoneSchedule SET SortOrder = ? WHERE StoneScheduleID = ?"); 
				$update_stmt->bind_param('ii', $sort, $stonescheduleid); 
				$update_stmt->execute();

				$sort++;
			}
		}
	}*/

	//delete a schedule entry
	if ($_POST['action'] == "delete")
	{
		if (isset($_POST['deleteid']))
		{
			$deleteid = $_POST['deleteid'];

			$stmt = $mysqli->prepare("DELETE FROM tblStoneSchedule WHERE StoneScheduleID = ? LIMIT 1");
			$stmt->bind_param("s", $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected stone staff entry was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected stone staff entry could not be deleted</div>";
				
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