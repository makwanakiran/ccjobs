<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//add a job
	if ($_POST['action'] == "add"){
		
		$address = $_POST['inputJobAddress'];
		$builder = $_POST['inputBuilder'];
		$dateentered = date("Y-m-d H:i:s");
		$datemeasure = (!empty($_POST['inputDateMeasure'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputDateMeasure']))) : NULL;
		$measureby = $_POST['inputMeasureBy'];
		$ProjectID = $_POST['projectId'];

		$insert_stmt = $mysqli->prepare("INSERT INTO tblJob (ProjectID, JobAddress, Builder, DateEntered, DateMeasure, MeasureBy) VALUES (?, ?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('isssss', $ProjectID, $address, $builder, $dateentered, $datemeasure, $measureby); 
		$insert_stmt->execute();
		

		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The job was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";

			if (!empty($measureby))
				emailDraftsman($measureby, $data['last_insert_id'], $mysqli);
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be added</div>";
			$data['action'] = "add";
		}
		
			
		echo json_encode($data);
	}
	
	//edit an existing job
	if ($_POST['action'] == "edit"){
		
		$jobid = $_POST['jobid'];
		$address = $_POST['inputJobAddress'];
		$builder = $_POST['inputBuilder'];
		$datemeasure = (!empty($_POST['inputDateMeasure'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputDateMeasure']))) : NULL;
		$measureby = $_POST['inputMeasureBy'];
		$ProjectID = $_POST['projectId'];
		$status = $_POST['status'];

		//check if measure by has changed
		$query = "SELECT MeasureBy FROM tblJob WHERE JobID = $jobid";
        $result = $mysqli->query($query);
        $row = $result->fetch_array();

		if ($row['MeasureBy'] <> $measureby)
			$sendemail = true;
		else
			$sendemail = false;

		$update_stmt = $mysqli->prepare("UPDATE tblJob SET ProjectID = ?, JobAddress = ?, Builder = ?, DateMeasure = ?, MeasureBy = ?, status = ? WHERE JobID = ?");

		$update_stmt->bind_param('issssii', $ProjectID, $address, $builder, $datemeasure, $measureby, $status, $jobid); 
		$update_stmt->execute();
		

		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The job was updated successfully.</div>";
			$data['last_insert_id'] = $jobid;

			if (!empty($measureby) && $sendemail == true)
				emailDraftsman($measureby, $data['last_insert_id'], $mysqli);
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be updated.</div>";
			$data['last_insert_id'] = $jobid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}
	
	//delete job
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];
			$deleted = 1;

			$stmt = $mysqli->prepare("UPDATE tblJob SET Deleted = ? WHERE JobID = ?");
			$stmt->bind_param("ii",$deleted, $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be deleted</div>";
				
			$stmt->close();
		}
	}

	//reset job task started
	if ($_POST['action'] == "reset-start"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("DELETE FROM tblJobTask WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job task ready
	if ($_POST['action'] == "reset-ready"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("UPDATE tblJobTask SET ReadyForCheck = 0 WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job task checked
	if ($_POST['action'] == "reset-checked"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("UPDATE tblJobTask SET DateChecked = NULL, CheckedBy = NULL, CopyPlans = 0, QuickClips = 0, WhiteCaps = 0, SinkCutouts = 0, HotplateCutouts = 0, BasesPelmets = 0, AppliancesFitted = 0, CutleryTrays = 0, WorkersName = 0, CabinetLabelled = 0, StickersRemoved = 0, AllAccessories = 0, HandlesMounted = 0, BumpOns = 0, MeasureOverallSizes = 0, OverheadCleats = 0, KitchenInspected = 0, SoftClosers = 0, BlocksKickboards = 0, CheckRails = 0, KickboardsNumbered = 0, DishwasherAngle = 0, Templates = 0, MissingItems = NULL, MissingItemsComplete = 0, DateCompleted = NULL, CompletedBy = NULL WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job task complete
	if ($_POST['action'] == "reset-complete"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("UPDATE tblJobTask SET DateCompleted = NULL, CompletedBy = NULL WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job task checked install
	if ($_POST['action'] == "reset-checkedinstall"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("DELETE FROM tblJobTaskInstall WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job task complete install
	if ($_POST['action'] == "reset-completeinstall"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("UPDATE tblJobTaskInstall SET DateCompleted = NULL, CompletedBy = NULL, SentToMaint = 0 WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}	

	//reset job task started draft
	if ($_POST['action'] == "reset-startdraft"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("DELETE FROM tblJobTaskDraft WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job task drawn
	if ($_POST['action'] == "reset-drawndraft"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateDrawn = NULL, DrawnBy = NULL, Notes = NULL, MissingItems = NULL, MissingItemsComplete = 0, DateCompleted = NULL, CompletedBy = NULL WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job task complete
	if ($_POST['action'] == "reset-completedraft"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateCompleted = NULL, CompletedBy = NULL WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job cnc complete
	if ($_POST['action'] == "reset-completecnc"){
		if (isset($_POST['jobid']) && isset($_POST['jobmaterialid'])){
			$jobid = $_POST['jobid'];
			$jobmaterialid = $_POST['jobmaterialid'];

			$stmt = $mysqli->prepare("UPDATE tblJobMaterial SET CNCDateCompleted = NULL, CNCCompletedBy = NULL WHERE JobID = ? AND JobMaterialID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $jobmaterialid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected item was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected item could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job edging complete
	if ($_POST['action'] == "reset-completeedging"){
		if (isset($_POST['jobid']) && isset($_POST['jobmaterialid'])){
			$jobid = $_POST['jobid'];
			$jobmaterialid = $_POST['jobmaterialid'];

			$stmt = $mysqli->prepare("UPDATE tblJobMaterial SET EdgingDateCompleted = NULL, EdgingCompletedBy = NULL WHERE JobID = ? AND JobMaterialID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $jobmaterialid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected item was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected item could not be reset</div>";
				
			$stmt->close();
		}
	}

	//delete job material
	if ($_POST['action'] == "delete-jobmaterial"){
		if (isset($_POST['jobid']) && isset($_POST['jobmaterialid'])){
			$jobid = $_POST['jobid'];
			$jobmaterialid = $_POST['jobmaterialid'];

			$stmt = $mysqli->prepare("DELETE FROM tblJobMaterial WHERE JobID = ? AND JobMaterialID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $jobmaterialid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected material was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected material could not be deleted</div>";
				
			$stmt->close();
		}
	}

	//reset job task start maintenance
	if ($_POST['action'] == "reset-startmaint"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("DELETE FROM tblJobTaskMaint WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}

	//reset job task complete maintenance
	if ($_POST['action'] == "reset-completemaint"){
		if (isset($_POST['jobid']) && isset($_POST['taskid'])){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];

			$stmt = $mysqli->prepare("UPDATE tblJobTaskMaint SET DateCompleted = NULL, CompletedBy = NULL WHERE JobID = ? AND TaskID = ? LIMIT 1");
			$stmt->bind_param("ii",$jobid, $taskid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected job task was reset successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected job could not be reset</div>";
				
			$stmt->close();
		}
	}
}

function emailDraftsman($userid, $jobid, $mysqli){
	if (!empty($userid) && $userid != 'NULL'){
		require('../classes/class.phpmailer.php');
		require("../classes/class.smtp.php");

		if ($stmt = $mysqli->prepare("SELECT Email FROM tblUser WHERE UserID = ? LIMIT 1")) { 
			$stmt->bind_param('s', $userid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($email_to);
			$stmt->fetch();
		}

		if (!empty($email_to)){
			$email_from = "no-reply@challengecabinetsjobs.com.au";

			$jobquery = "SELECT JobID, JobAddress, DateMeasure FROM tblJob WHERE JobID = $jobid";
			$jobresult = $mysqli->query($jobquery);
			$jobrow = $jobresult->fetch_array();

			$measurestring = "";
			if (!empty($jobrow['DateMeasure']))
				$measurestring = " Measure Date: " . date('d-m-Y', strtotime($jobrow['DateMeasure']));

			$email_subject = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " has been assigned to you." . $measurestring;
			$email_message = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " has been assigned to you." . $measurestring . "<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";

			$headers = 'From: '.$email_from."\r\n". 'Reply-To: '.$email_from."\r\n" . 'X-Mailer: PHP/' . phpversion();
			
			$mail = new PHPMailer(true);
			//$mail->IsSMTP();

			//$mail->Host = "mail.bigpond.com.au";
			
			$mail->SetFrom($email_from);
			$mail->AddAddress($email_to);

			$mail->Subject = $email_subject;
			$mail->Body = $email_message;
			$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
			
			$mail->Send();
		}
		
	}
}

?>