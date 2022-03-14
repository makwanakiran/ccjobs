<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id'])){
	header("location:index.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//start a job
	if ($_POST['action'] == "start"){
		
		$jobid = $_POST['jobid'];
		$taskid = $_POST['taskid'];
		$datestarted = date("Y-m-d H:i:s");
		$startedby = $_SESSION['user_id'];

		$insert_stmt = $mysqli->prepare("INSERT INTO tblJobTask (JobID, TaskID, DateStarted, StartedBy) VALUES (?, ?, ?, ?)");
		$insert_stmt->bind_param('iiss', $jobid, $taskid, $datestarted, $startedby); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The job was started successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be started</div>";
		}
			
		echo json_encode($data);
	}

	//ready for check
	if ($_POST['action'] == "check"){
		
		$jobid = $_POST['jobid'];
		$taskid = $_POST['taskid'];
		$readyforcheck = 1;

		$update_stmt = $mysqli->prepare("UPDATE tblJobTask SET ReadyForCheck = ? WHERE JobID = ? AND TaskID = ?");
		$update_stmt->bind_param('iii', $readyforcheck, $jobid, $taskid); 
		$update_stmt->execute();
				
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The job was started successfully.</div>";
			
			//send email to foreman
			require('classes/class.phpmailer.php');
			require("classes/class.smtp.php");

			$email_from = "no-reply@challengecabinetsjobs.com.au";

			$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblUser.FullName FROM ((tblJob INNER JOIN tblJobTask ON tblJob.JobID = tblJobTask.JobID) INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTask.StartedBy = tblUser.UserID WHERE tblJobTask.JobID = $jobid AND tblJobTask.TaskID = $taskid";
        	$jobresult = $mysqli->query($jobquery);
        	$jobrow = $jobresult->fetch_array();

			$email_subject = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . " - Assembler: " . $jobrow['FullName'] . ")  is ready to be checked";
			$email_message = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . " - Assembler: " . $jobrow['FullName'] . ")  is ready to be checked<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";
		
			$headers = 'From: '.$email_from."\r\n". 'Reply-To: '.$email_from."\r\n" . 'X-Mailer: PHP/' . phpversion();
			
			$mail = new PHPMailer(true);
			//$mail->IsSMTP();

			//$mail->Host = "mail.bigpond.com.au";
			
			$mail->SetFrom($email_from);

			$query = "SELECT Email FROM tblUser WHERE IsForeman <> 0 AND SendAlerts <> 0 AND Active <> 0";
        	$result = $mysqli->query($query);

			if ($result->num_rows > 0){
				while ($row = $result->fetch_array())
					$mail->AddAddress($row['Email']);

				$mail->Subject = $email_subject;
				$mail->Body = $email_message;
				$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
				
				$mail->Send();
			}
        	
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be started</div>";
		}
			
		echo json_encode($data);
	}

	//complete
	if ($_POST['action'] == "complete"){
		
		$jobid = $_POST['jobid'];
		$taskid = $_POST['taskid'];
		$datecompleted = date("Y-m-d H:i:s");
		$completedby = $_SESSION['user_id'];

		$update_stmt = $mysqli->prepare("UPDATE tblJobTask SET DateCompleted = ?, CompletedBy = ? WHERE JobID = ? AND TaskID = ?");
		$update_stmt->bind_param('ssii', $datecompleted, $completedby, $jobid, $taskid); 
		$update_stmt->execute();
				
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The job was completed successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be completed</div>";
		}
			
		echo json_encode($data);
	}

	// save checklist and missing items
	if ($_POST['action'] == "savechecklist"){
		if ($_SESSION['is_foreman'] <> 0){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			/*$copyplans = isset($_POST['inputCopyPlans']) ? $_POST['inputCopyPlans'] : 0;
			$quickclips = isset($_POST['inputQuickClips']) ? $_POST['inputQuickClips'] : 0;
			$whitecaps = isset($_POST['inputWhiteCaps']) ? $_POST['inputWhiteCaps'] : 0;
			$sinkcutouts = isset($_POST['inputSinkCutouts']) ? $_POST['inputSinkCutouts'] : 0;
			$hotplatecutouts = isset($_POST['inputHotplateCutouts']) ? $_POST['inputHotplateCutouts'] : 0;
			$basespelmets = isset($_POST['inputBasesPelmets']) ? $_POST['inputBasesPelmets'] : 0;
			$appliancesfitted = isset($_POST['inputAppliancesFitted']) ? $_POST['inputAppliancesFitted'] : 0;
			$cutlerytrays = isset($_POST['inputCutleryTrays']) ? $_POST['inputCutleryTrays'] : 0;
			$workersname = isset($_POST['inputWorkersName']) ? $_POST['inputWorkersName'] : 0;
			$cabinetlabelled = isset($_POST['inputCabinetLabelled']) ? $_POST['inputCabinetLabelled'] : 0;
			$stickersremoved = isset($_POST['inputStickersRemoved']) ? $_POST['inputStickersRemoved'] : 0;
			$allaccessories = isset($_POST['inputAllAccessories']) ? $_POST['inputAllAccessories'] : 0;
			$handlesmounted = isset($_POST['inputHandlesMounted']) ? $_POST['inputHandlesMounted'] : 0;
			$bumpons = isset($_POST['inputBumpOns']) ? $_POST['inputBumpOns'] : 0;
			$measureoverallsizes = isset($_POST['inputMeasureOverallSizes']) ? $_POST['inputMeasureOverallSizes'] : 0;
			$overheadcleats = isset($_POST['inputOverheadCleats']) ? $_POST['inputOverheadCleats'] : 0;
			$kitcheninspected = isset($_POST['inputKitchenInspected']) ? $_POST['inputKitchenInspected'] : 0;
			$softclosers = isset($_POST['inputSoftClosers']) ? $_POST['inputSoftClosers'] : 0;
			$blockskickboards = isset($_POST['inputBlocksKickboards']) ? $_POST['inputBlocksKickboards'] : 0;
			$checkrails = isset($_POST['inputCheckRails']) ? $_POST['inputCheckRails'] : 0;
			$kickboardsnumbered = isset($_POST['inputKickboardsNumbered']) ? $_POST['inputKickboardsNumbered'] : 0;
			$dishwasherangle = isset($_POST['inputDishwasherAngle']) ? $_POST['inputDishwasherAngle'] : 0;
			$templates = isset($_POST['inputTemplates']) ? $_POST['inputTemplates'] : 0;*/
		
			$missingitems = trim($_POST['inputMissingItems']);
			$Applicabletems = trim($_POST['inputApplicableItems']);
			//$missingitemscomplete = isset($_POST['inputMissingItemsComplete']) ? $_POST['inputMissingItemsComplete'] : 0;
			$missingitemscomplete =  0;

			//check if date checked has already been set
			//$query = "SELECT DateChecked, CheckedBy FROM tblJobTask WHERE JobID = $jobid AND TaskID = $taskid";
        	//$result = $mysqli->query($query);
        	//$row = $result->fetch_array();

        	//if (!empty($row['DateChecked']) && !empty($row['CheckedBy'])){
			//	$datechecked = $row['DateChecked'];
			//	$checkedby = $row['CheckedBy'];
			//}
			//else{
				$datechecked = date("Y-m-d H:i:s");
				$checkedby = $_SESSION['user_id'];
			//}

			/*$update_stmt = $mysqli->prepare("UPDATE tblJobTask SET CopyPlans = ?, QuickClips = ?, WhiteCaps = ?, SinkCutouts = ?, HotplateCutouts = ?, BasesPelmets = ?, AppliancesFitted = ?, CutleryTrays = ?, WorkersName = ?, CabinetLabelled = ?, StickersRemoved = ?, AllAccessories = ?, HandlesMounted = ?, BumpOns = ?, MeasureOverallSizes = ?, OverheadCleats = ?, KitchenInspected = ?, SoftClosers = ?, BlocksKickboards = ?, CheckRails = ?, KickboardsNumbered = ?, DishwasherAngle = ?, Templates = ?, MissingItems = ?, MissingItemsComplete = ?, DateChecked = ?, CheckedBy = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('iiiiiiiiiiiiiiiiiiiiiiisissii', $copyplans, $quickclips, $whitecaps, $sinkcutouts, $hotplatecutouts, $basespelmets, $appliancesfitted, $cutlerytrays, $workersname, $cabinetlabelled, $stickersremoved, $allaccessories, $handlesmounted, $bumpons, $measureoverallsizes, $overheadcleats, $kitcheninspected, $softclosers, $blockskickboards, $checkrails, $kickboardsnumbered, $dishwasherangle, $templates, $missingitems, $missingitemscomplete, $datechecked, $checkedby, $jobid, $taskid);*/
			$update_stmt = $mysqli->prepare("UPDATE tblJobTask SET Applicabletems = ?,  MissingItems = ?, MissingItemsComplete = ?, DateChecked = ?, CheckedBy = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssissii', $Applicabletems, $missingitems, $missingitemscomplete, $datechecked, $checkedby, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The checklist was saved successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The checklist could not be saved</div>";
			}
				
			echo json_encode($data);
		}
	}

}
?>