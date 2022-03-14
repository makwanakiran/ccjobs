<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id'])){
	header("location:index.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//job ready for check
	if ($_POST['action'] == "check"){
		
		$jobid = $_POST['jobid'];
		$taskid = $_POST['taskid'];
		$readyforcheck = 1;

		$insert_stmt = $mysqli->prepare("INSERT INTO tblJobTaskInstall (JobID, TaskID, ReadyForCheck) VALUES (?, ?, ?)");
		$insert_stmt->bind_param('iii', $jobid, $taskid, $readyforcheck); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The job was started successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be started</div>";
		}
			
		echo json_encode($data);
	}


	//complete
	if ($_POST['action'] == "complete"){
		if ($_SESSION['is_installer'] <> 0){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskInstall SET DateCompleted = ?, CompletedBy = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssii', $datecompleted, $completedby, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was completed successfully.</div>";

				//send email to foreman
				require('classes/class.phpmailer.php');
				require("classes/class.smtp.php");

				$email_from = "no-reply@challengecabinetsjobs.com.au";

				$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName, tblUser.FullName FROM ((tblJob INNER JOIN tblJobTaskInstall ON tblJob.JobID = tblJobTaskInstall.JobID) INNER JOIN tblTask ON tblJobTaskInstall.TaskID = tblTask.TaskID) INNER JOIN tblUser ON tblJobTaskInstall.CompletedBy = tblUser.UserID WHERE tblJobTaskInstall.JobID = $jobid AND tblJobTaskInstall.TaskID = $taskid";
				$jobresult = $mysqli->query($jobquery);
				$jobrow = $jobresult->fetch_array();

				$email_subject = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . " - Installer: " . $jobrow['FullName'] . ")" . " installation has been completed";
				$email_message = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . " - Installer: " . $jobrow['FullName'] . ")" . " installation has been completed<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";
			
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
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be completed</div>";
			}
				
			echo json_encode($data);
		}
	}

	// save checklist and missing items
	if ($_POST['action'] == "savechecklist"){
		if ($_SESSION['is_installer'] <> 0){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			/*$gapped = isset($_POST['inputGapped']) ? $_POST['inputGapped'] : 0;
			$capped = isset($_POST['inputCapped']) ? $_POST['inputCapped'] : 0;
			$adjusteddoorsdrawers = isset($_POST['inputAdjustedDoorsDrawers']) ? $_POST['inputAdjustedDoorsDrawers'] : 0;
			$benchtopstemplatesinstalled = isset($_POST['inputBenchtopsTemplatesInstalled']) ? $_POST['inputBenchtopsTemplatesInstalled'] : 0;
			$basinsinstalled = isset($_POST['inputBasinsInstalled']) ? $_POST['inputBasinsInstalled'] : 0;
			$alllevelschecked = isset($_POST['inputAllLevelsChecked']) ? $_POST['inputAllLevelsChecked'] : 0;
			$ovencleats = isset($_POST['inputOvenCleats']) ? $_POST['inputOvenCleats'] : 0;
			$cabinetscleaned = isset($_POST['inputCabinetsCleaned']) ? $_POST['inputCabinetsCleaned'] : 0;*/			
		
			$missingitems = trim($_POST['inputMissingItems']);
			$Applicabletems = trim($_POST['inputApplicableItems']);
			$missingitemscomplete = isset($_POST['inputMissingItemsComplete']) ? $_POST['inputMissingItemsComplete'] : 0;

			//check if date checked has already been set
			//$query = "SELECT DateChecked, CheckedBy FROM tblJobTaskInstall WHERE JobID = $jobid AND TaskID = $taskid";
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

			/*$update_stmt = $mysqli->prepare("UPDATE tblJobTaskInstall SET Gapped = ?, Capped = ?, AdjustedDoorsDrawers = ?, BenchtopsTemplatesInstalled = ?, BasinsInstalled = ?, AllLevelsChecked = ?, OvenCleats = ?, CabinetsCleaned = ?, MissingItems = ?, MissingItemsComplete = ?, DateChecked = ?, CheckedBy = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('iiiiiiiisissii', $gapped, $capped, $adjusteddoorsdrawers, $benchtopstemplatesinstalled, $basinsinstalled, $alllevelschecked, $ovencleats, $cabinetscleaned, $missingitems, $missingitemscomplete, $datechecked, $checkedby, $jobid, $taskid); */
			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskInstall SET Applicabletems=?, MissingItems = ?, MissingItemsComplete = ?, DateChecked = ?, CheckedBy = ? WHERE JobID = ? AND TaskID = ?");
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


	// send to maintenance
	if ($_POST['action'] == "sendmaint"){
		if ($_SESSION['is_foreman'] <> 0){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
		
			$senttomaint = 1;
			$missingitems = trim($_POST['missingitems']);
			$missingitemscomplete = 1;
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskInstall SET DateCompleted = ?, CompletedBy = ?, MissingItemsComplete = ?, SentToMaint = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssiiii', $datecompleted, $completedby, $missingitemscomplete, $senttomaint, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was sent to maintenance successfully.</div>";
				
				$datestarted = date("Y-m-d H:i:s");
				$startedby = $_SESSION['user_id'];

				$insert_stmt = $mysqli->prepare("INSERT INTO tblJobTaskMaint (JobID, TaskID, DateStarted, StartedBy, MissingItems) VALUES (?, ?, ?, ?, ?)");
				$insert_stmt->bind_param('iisss', $jobid, $taskid, $datestarted, $startedby, $missingitems); 
				$insert_stmt->execute();

				//send email to maintenance users
				require('classes/class.phpmailer.php');
				require("classes/class.smtp.php");

				$email_from = "no-reply@challengecabinetsjobs.com.au";

				$jobquery = "SELECT tblJob.JobID, tblJob.JobAddress, tblTask.TaskName FROM (tblJob INNER JOIN tblJobTaskMaint ON tblJob.JobID = tblJobTaskMaint.JobID) INNER JOIN tblTask ON tblJobTaskMaint.TaskID = tblTask.TaskID WHERE tblJobTaskMaint.JobID = $jobid AND tblJobTaskMaint.TaskID = $taskid";
				$jobresult = $mysqli->query($jobquery);
				$jobrow = $jobresult->fetch_array();

				$email_subject = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . ")" . " has been assigned to you for maintenance";
				$email_message = "Job #" . $jobid . " - " . $jobrow['JobAddress'] . " (" . $jobrow['TaskName'] . ")" . " has been assigned to you for maintenance<br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";
			
				$headers = 'From: '.$email_from."\r\n". 'Reply-To: '.$email_from."\r\n" . 'X-Mailer: PHP/' . phpversion();
				
				$mail = new PHPMailer(true);
				//$mail->IsSMTP();

				//$mail->Host = "mail.bigpond.com.au";
				
				$mail->SetFrom($email_from);

				$query = "SELECT Email FROM tblUser WHERE IsMaint <> 0 AND Active <> 0";
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
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be sent to maintenance</div>";
			}
				
			echo json_encode($data);
		}
	}

}
?>