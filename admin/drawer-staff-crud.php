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
		$scheduledate = (!empty($_POST['inputDrawerDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputDrawerDate']))) : NULL;
		$notes = $_POST['inputNotes'];
		$DrawerType = 2;

		$insert_stmt = $mysqli->prepare("INSERT INTO tblDrawerSchedule (UserID, JobID, Description, ScheduleDate, Notes, DrawerType) VALUES (?, ?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('sisssi', $userid, $jobid, $description, $scheduledate, $notes, $DrawerType); 
		$insert_stmt->execute();
				
		print_r($insert_stmt);die;		
		if ($insert_stmt->affected_rows != -1)
		{
			$data['msg'] = "<div class='alert alert-success' role='alert'>The draftsman staff entry was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";
		}
		else
		{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The draftsman staff entry could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit a schedule entry
	if ($_POST['action'] == "edit")
	{

		$drawerid = $_POST['drawerid'];
		$drawerresult = "SELECT * FROM tblDrawerSchedule WHERE DrawerScheduleID = ".$drawerid; 
		$drawerresult_single = $mysqli->query($drawerresult);
		$drawer_row = $drawerresult_single->fetch_array();

		if ($drawer_row['DrawerScheduleID'] != '' && $drawer_row['DrawerScheduleID'] != 0)
		{
			$data['msg'] = "<div class='alert alert-success' role='alert'>The draftsman staff entry was edited successfully.</div>";
			$data['DrawerId'] = $drawer_row['DrawerScheduleID'];
			$data['UserId'] = $drawer_row['UserID'];
			$data['Description'] = $drawer_row['Description'];
			$data['DrawerDate'] = !empty($drawer_row['ScheduleDate']) ? date('d-m-Y',strtotime($drawer_row['ScheduleDate'])) : "";
			$data['Notes'] = $drawer_row['Notes'];
			$data['last_insert_id'] = $drawerid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The draftsman staff entry could not be edited.</div>";
			$data['last_insert_id'] = $drawerid;
		}
		$data['action'] = "edit";
		echo json_encode($data);
	}

	if ($_POST['action'] == "update")
	{
		$drawerid = $_POST['drawerid'];
		$userid = $_POST['inputUserID'];
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$scheduledate = (!empty($_POST['inputDrawerDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputDrawerDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$update_stmt = $mysqli->prepare("UPDATE tblDrawerSchedule SET UserID = ?, JobID = ?, Description = ?, ScheduleDate = ?, Notes = ? WHERE DrawerScheduleID = ?"); 
		$update_stmt->bind_param('sisssi', $userid, $jobid, $description, $scheduledate, $notes, $drawerid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The draftsman staff entry was updated successfully.</div>";
			$data['last_insert_id'] = $drawerid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The draftsman staff entry could not be updated.</div>";
			$data['last_insert_id'] = $drawerid;
		}
		
		$data['action'] = "update";
		echo json_encode($data);
	}


	//move a schedule entry
	/*if ($_POST['action'] == "move"){
		
		$drawerscheduleid = $_POST['drawerscheduleid'];
		$userid = $_POST['userid'];
		$scheduledate = (!empty($_POST['scheduledate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['scheduledate']))) : NULL;

		$update_stmt = $mysqli->prepare("UPDATE tblDrawerSchedule SET UserID = ?, ScheduleDate = ? WHERE DrawerScheduleID = ?"); 
		$update_stmt->bind_param('ssi', $userid, $scheduledate, $drawerscheduleid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The draftsman schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $drawerscheduleid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The draftsman schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $drawerscheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//sort schedule entries
	if ($_POST['action'] == "sort"){
		if (isset($_POST['sortorder'])){
			$sortorder = $_POST['sortorder'];

			$sort = 1;
			foreach ($sortorder as $drawerscheduleid){
				$update_stmt = $mysqli->prepare("UPDATE tblDrawerSchedule SET SortOrder = ? WHERE DrawerScheduleID = ?"); 
				$update_stmt->bind_param('ii', $sort, $drawerscheduleid); 
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

			$stmt = $mysqli->prepare("DELETE FROM tblDrawerSchedule WHERE DrawerScheduleID = ? LIMIT 1");
			$stmt->bind_param("s", $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected draftsman staff entry was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected draftsman staff entry could not be deleted</div>";
				
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