<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//add a schedule entry
	if ($_POST['action'] == "add"){
	
		$userid = $_POST['inputUserID'];
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$scheduledate = (!empty($_POST['inputScheduleDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputScheduleDate']))) : NULL;
		$notes = $_POST['inputNotes'];
		$AssemblerType = 1;

		$insert_stmt = $mysqli->prepare("INSERT INTO tblAssemblerSchedule (UserID, JobID, Description, ScheduleDate, Notes, AssemblerType) VALUES (?, ?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('sisssi', $userid, $jobid, $description, $scheduledate, $notes, $AssemblerType); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The assembler staff entry was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The assembler staff entry could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit a schedule entry
	if ($_POST['action'] == "edit")
	{
		$assemblerscheduleid = $_POST['assemblerscheduleid'];
		$userid = $_POST['inputUserID'];
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$scheduledate = (!empty($_POST['inputScheduleDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputScheduleDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$update_stmt = $mysqli->prepare("UPDATE tblAssemblerSchedule SET UserID = ?, JobID = ?, Description = ?, ScheduleDate = ?, Notes = ? WHERE AssemblerScheduleID = ?"); 
		$update_stmt->bind_param('sisssi', $userid, $jobid, $description, $scheduledate, $notes, $assemblerscheduleid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The assembler schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $assemblerscheduleid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The assembler schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $assemblerscheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//move a schedule entry
	if ($_POST['action'] == "move"){
		
		$assemblerscheduleid = $_POST['assemblerscheduleid'];
		$userid = $_POST['userid'];
		$scheduledate = (!empty($_POST['scheduledate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['scheduledate']))) : NULL;

		$update_stmt = $mysqli->prepare("UPDATE tblAssemblerSchedule SET UserID = ?, ScheduleDate = ? WHERE AssemblerScheduleID = ?"); 
		$update_stmt->bind_param('ssi', $userid, $scheduledate, $assemblerscheduleid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The assembler schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $assemblerscheduleid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The assembler schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $assemblerscheduleid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//sort schedule entries
	if ($_POST['action'] == "sort"){
		if (isset($_POST['sortorder'])){
			$sortorder = $_POST['sortorder'];

			$sort = 1;
			foreach ($sortorder as $assemblerscheduleid){
				$update_stmt = $mysqli->prepare("UPDATE tblAssemblerSchedule SET SortOrder = ? WHERE AssemblerScheduleID = ?"); 
				$update_stmt->bind_param('ii', $sort, $assemblerscheduleid); 
				$update_stmt->execute();

				$sort++;
			}
		}
	}

	//delete a schedule entry
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];

			$stmt = $mysqli->prepare("DELETE FROM tblAssemblerSchedule WHERE AssemblerScheduleID = ? LIMIT 1");
			$stmt->bind_param("s", $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected assembler schedule entry was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected assembler schedule entry could not be deleted</div>";
				
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