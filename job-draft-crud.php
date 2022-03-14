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
		if ($_SESSION['is_draftsman'] <> 0){
			$jobid = $_POST['jobid'];
			//$taskid = $_POST['taskid'];
			$datestarted = date("Y-m-d H:i:s");
			$startedby = $_SESSION['user_id'];

			$query = "SELECT TaskID FROM tblTask WHERE Weight < 2 ORDER BY TaskID";
			$result = $mysqli->query($query);

			while($row = $result->fetch_array()){
				$taskid = $row['TaskID'];

				$insert_stmt = $mysqli->prepare("INSERT INTO tblJobTaskDraft (JobID, TaskID, DateStarted, StartedBy) VALUES (?, ?, ?, ?)");
				$insert_stmt->bind_param('iiss', $jobid, $taskid, $datestarted, $startedby); 
				$insert_stmt->execute();
			}	
					
			if ($insert_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was started successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be started</div>";
			}
				
			echo json_encode($data);
		}
	}

	//drawn
	if ($_POST['action'] == "drawn"){
		if ($_SESSION['is_draftsman'] <> 0){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$datedrawn = date("Y-m-d H:i:s");
			$drawnby = $_SESSION['user_id'];

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateDrawn = ?, DrawnBy = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssii', $datedrawn, $drawnby, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was marked as drawn successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be marked as drawn</div>";
			}
				
			echo json_encode($data);
		}
	}

	//complete
	if ($_POST['action'] == "complete"){
		if ($_SESSION['is_draftsman'] <> 0){
			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];
			$is_off = 1;

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateCompleted = ?, CompletedBy = ?,is_off = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssii', $datecompleted, $completedby, $is_off ,$jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was completed successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be completed</div>";
			}	
				
			echo json_encode($data);
		}
	}

	//sing off
	if ($_POST['action'] == "singOff"){
		if ($_SESSION['is_JobApprove'] == 1){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];
			$is_off = 1;

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateCompleted = ?, CompletedBy = ?, is_off = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssiii', $datecompleted, $completedby, $is_off, $jobid, $taskid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was sing off successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be sing off.</div>";
			}
			echo json_encode($data);
		}
	}

	//sing off All
	if ($_POST['action'] == "singOffAll"){
		if ($_SESSION['is_JobApprove'] == 1){

			$jobid = $_POST['jobid'];
			$datecompleted = date("Y-m-d H:i:s");
			$completedby = $_SESSION['user_id'];
			$is_off = 1;

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET DateCompleted = ?, CompletedBy = ?, is_off = ? WHERE JobID = ?");
			$update_stmt->bind_param('ssii', $datecompleted, $completedby, $is_off, $jobid); 
			$update_stmt->execute();
					
			if ($update_stmt->affected_rows != -1){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was sing off successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be sing off.</div>";
			}
			echo json_encode($data);
		}
	}

	if($_POST['action'] == "singOffAlert")
	{
		$jobid = $_POST['jobid'];
		$taskid = $_POST['taskid'];
		
		if ($stmt = $mysqli->prepare("SELECT JobID, JobAddress, Builder, DateMeasure, MeasureBy FROM tblJob WHERE JobID = ? LIMIT 1")) { 
			$stmt->bind_param('i', $jobid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($formjobid, $formjobaddress, $formbuilder, $formdatemeasure, $formmeasureby);
			$stmt->fetch();
		}
		$query = "SELECT Email FROM tblUser WHERE UserID = $formmeasureby";
		$result = $mysqli->query($query);
		$row = $result->fetch_array();
		$sendmail = $row['Email'];
		
		$task_query = "SELECT TaskName FROM tblTask WHERE TaskID = $taskid";
		$task_result = $mysqli->query($task_query);
		$task_row = $task_result->fetch_array();


		$TaskName = $task_row['TaskName'];

		require('classes/class.phpmailer.php');
		require("classes/class.smtp.php");

		$email_from = "no-reply@challengecabinetsjobs.com.au";
		$headers = 'From: '.$email_from."\r\n". 'Reply-To: '.$email_from."\r\n" . 'X-Mailer: PHP/' . phpversion();
		

		$email_subject = "Job #" . $jobid . " - " . $formjobaddress . " (" . $TaskName . " - Room: " . $_SESSION['full_name'] . ")  is ready to sing off room ";
		$email_message = "Job #" . $jobid . " - " . $formjobaddress . " (" . $TaskName . " - Room: " . $_SESSION['full_name'] . ")  is ready to sing Off job <br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";

		$mail = new PHPMailer(true);
		 $mail->isSMTP();                             // Set mailer to use SMTP
		 $mail->Host       = 'smtp.gmail.com';             // Set the SMTP server to send through
		 $mail->SMTPAuth   = true;                    // Enable SMTP authentication
		 $mail->SMTPDebug = 4; 
		 $mail->Username   = 'viralb.technocomet@gmail.com';     // SMTP false username
		 $mail->Password   = 'Default@123';                      // SMTP false password
		 $mail->SMTPSecure = 'tsl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
		 $mail->Port       = 587; 


		$mail->AddAddress($sendmail);

		$mail->Subject = $email_subject;
		$mail->Body = $email_message;
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
		
		$sendMail_orNot = $mail->Send();

		if ($sendMail_orNot){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The job was sing off alert send successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be sing off alert send.</div>";
		}
		echo json_encode($data);


	}

	if($_POST['action'] == "singOffAlertAll")
	{
		$jobid = $_POST['jobid'];
		

		if ($stmt = $mysqli->prepare("SELECT JobID, JobAddress, Builder, DateMeasure, MeasureBy FROM tblJob WHERE JobID = ? LIMIT 1")) { 
			$stmt->bind_param('i', $jobid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($formjobid, $formjobaddress, $formbuilder, $formdatemeasure, $formmeasureby);
			$stmt->fetch();
		}
		$query = "SELECT Email FROM tblUser WHERE UserID = $formmeasureby";
		$result = $mysqli->query($query);
		$row = $result->fetch_array();
		$sendmail = $row['Email'];
		
		$task_query = "SELECT tblTask.TaskName FROM tblJobTaskDraft JOIN tblTask  ON tblTask.TaskID = tblJobTaskDraft.TaskID WHERE JobID = $jobid AND is_off = 0";

		$task_result = $mysqli->query($task_query);
		//$task_row = $task_result->fetch_array();

		//$TaskName = $task_row['TaskName'];

		require('classes/class.phpmailer.php');
		require("classes/class.smtp.php");

		$email_from = "no-reply@challengecabinetsjobs.com.au";
		$headers = 'From: '.$email_from."\r\n". 'Reply-To: '.$email_from."\r\n" . 'X-Mailer: PHP/' . phpversion();
		
		$numberCount = mysqli_num_rows($task_result);
		if($numberCount > 0)
		{

			while($task_row = $task_result->fetch_array()){

			
				$email_subject = "Job #" . $jobid . " - " . $formjobaddress . " (" . $task_row['TaskName'] . " - Room: " . $_SESSION['full_name'] . ")  is ready to sing Off job ";
				$email_message = "Job #" . $jobid . " - " . $formjobaddress . " (" . $task_row['TaskName'] . " - Room: " . $_SESSION['full_name'] . ")  is ready to sing off room <br><br><a href='http://www.challengecabinetsjobs.com.au'>www.challengecabinetsjobs.com.au</a>";
				

				$mail = new PHPMailer(true);
				 $mail->isSMTP();                             // Set mailer to use SMTP
				 $mail->Host       = 'smtp.gmail.com';             // Set the SMTP server to send through
				 $mail->SMTPAuth   = true;                    // Enable SMTP authentication
				 $mail->SMTPDebug = 4; 
				 $mail->Username   = 'viralb.technocomet@gmail.com';     // SMTP false username
				 $mail->Password   = 'Default@123';                      // SMTP false password
				 $mail->SMTPSecure = 'tsl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
				 $mail->Port       = 587; 


				$mail->AddAddress($sendmail);

				$mail->Subject = $email_subject;
				$mail->Body = $email_message;
				$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
				
				$sendMail_orNot = $mail->Send();
			}
			if ($sendMail_orNot){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was sing off alert send successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be sing off alert send.</div>";
			}
			echo json_encode($data);
		}

		$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be sing off alert send.</div>";
			
		echo json_encode($data);

	}

	//delete room
	if ($_POST['action'] == "deleteRoom"){
		if ($_SESSION['is_JobApprove'] == 1){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			$user_id = $_SESSION['user_id'];
			

			$query = "DELETE FROM tblJobTaskDraft WHERE JobID = $jobid AND TaskID = $taskid";

					
			if ($mysqli->query($query) === TRUE){
				$data['msg'] = "<div class='alert alert-success' role='alert'>The job was deleted successfully.</div>";
			}
			else{
				$data['msg'] = "<div class='alert alert-danger' role='alert'>The job could not be deleted.</div>";
			}
			echo json_encode($data);
		}
	}

	//Remove room
	if ($_POST['action'] == "RemoveRoom"){
		

		
		$taskid = $_POST['room_id'];
		$user_id = $_SESSION['user_id'];
		

		$query = "DELETE FROM tblTask WHERE TaskID = $taskid AND user_id = $user_id";
		$queryDraf = "DELETE FROM tblJobTaskDraft WHERE TaskID = $taskid ";

				
		if ($mysqli->query($query) === TRUE && $mysqli->query($queryDraf) === TRUE){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The room was deleted successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The room could not be deleted.</div>";
		}
		echo json_encode($data);
		
	}



	// add new task room
	if ($_POST['action'] == "taskInsert"){

		$taskName = $_POST['task_name'];
		$Weight = 1;
		$user_id = $_SESSION['user_id'];
		$jobid = $_POST['Roomjobid'];
		$DateStarted = date("Y-m-d H:i:s");

		$insert_stmt = $mysqli->prepare("INSERT INTO tblTask (TaskName,Weight,user_id) VALUES (?,?,?)");
		$insert_stmt->bind_param('sii', $taskName,$Weight,$user_id); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){

			$taskId = $insert_stmt->insert_id;

			$Room_insert_stmt = $mysqli->prepare("INSERT INTO tblJobTaskDraft (JobID,TaskID,DateStarted,StartedBy) VALUES (?,?,?,?)");
			$Room_insert_stmt->bind_param('iisi', $jobid,$taskId,$DateStarted,$user_id); 
			$Room_insert_stmt->execute();

			$data['msg'] = "<div class='alert alert-success' role='alert'>The task room successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The task room not add.</div>";
		}
		echo json_encode($data);
	}

	// add new room
	if ($_POST['action'] == "createRoom"){

		$taskId = $_POST['roomId'];
		$jobid = $_POST['jobid'];
		$DateStarted = date("Y-m-d H:i:s");
		$userId = $_SESSION['user_id'];

		$insert_stmt = $mysqli->prepare("INSERT INTO tblJobTaskDraft (JobID,TaskID,DateStarted,StartedBy) VALUES (?,?,?,?)");
		
		$insert_stmt->bind_param('iisi', $jobid,$taskId,$DateStarted,$userId); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The room add successfully.</div>";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The room not add.</div>";
		}
		echo json_encode($data);
	}


	// save checklist and missing items
	if ($_POST['action'] == "savechecklist"){
		if ($_SESSION['is_draftsman'] <> 0){

			$jobid = $_POST['jobid'];
			$taskid = $_POST['taskid'];
			
			$notes = trim($_POST['inputNotes']);
			$missingitems = '';
			//$missingitems = trim($_POST['inputMissingItems']);
			//$missingitemscomplete = isset($_POST['inputMissingItemsComplete']) ? $_POST['inputMissingItemsComplete'] : 0;
			$missingitemscomplete = 1;

			$update_stmt = $mysqli->prepare("UPDATE tblJobTaskDraft SET Notes = ?, MissingItems = ?, MissingItemsComplete = ? WHERE JobID = ? AND TaskID = ?");
			$update_stmt->bind_param('ssiii', $notes, $missingitems, $missingitemscomplete, $jobid, $taskid); 
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

	// save checklist and missing items
	if ($_POST['action'] == "savematerials"){
		if ($_SESSION['is_draftsman'] <> 0){

			//update existing materials
			if (isset($_POST['inputJobMaterialID'])){
				foreach ($_POST['inputJobMaterialID'] as $k => $v) {
					
					$jobmaterialid = $_POST['inputJobMaterialID'][$k];
					$boarddescription = $_POST['inputBoardDescription'][$k];
					$boardtype = $_POST['inputBoardType'][$k];
					$boardquantity = $_POST['inputBoardQuantity'][$k];

					$delete = $_POST['inputMaterialDelete'][$k];

					if ($delete != "1"){
						$line_update_stmt = $mysqli->prepare("UPDATE tblJobMaterial SET BoardDescription = ?, BoardType = ?, BoardQuantity = ? WHERE JobMaterialID = ?"); 
						$line_update_stmt->bind_param('ssii', $boarddescription, $boardtype, $boardquantity, $jobmaterialid); 
				
						$line_update_stmt->execute();
					}
					else
					{
						//delete removed items
						$stmt = $mysqli->prepare("DELETE FROM tblJobMaterial WHERE JobMaterialID = ? LIMIT 1");
						$stmt->bind_param("i",$jobmaterialid);     
						$stmt->execute();
					}
				}
			}

			//add materials
			if (isset($_POST['inputBoardDescriptionAdd'])){
				foreach ($_POST['inputBoardDescriptionAdd'] as $k => $v) {
					$jobid = $_POST['jobid'];
					$boarddescription = $_POST['inputBoardDescriptionAdd'][$k];
					$boardtype = $_POST['inputBoardTypeAdd'][$k];
					$boardquantity = $_POST['inputBoardQuantityAdd'][$k];
					$cancel = $_POST['inputMaterialCancelAdd'][$k];
					
					if ($cancel != "1"){
						$line_insert_stmt = $mysqli->prepare("INSERT INTO tblJobMaterial (JobID, BoardDescription, BoardType, BoardQuantity) Values (?, ?, ?, ?)"); 
						$line_insert_stmt->bind_param('issi', $jobid, $boarddescription, $boardtype, $boardquantity); 
				
						$line_insert_stmt->execute();
					}
				}
			}
			$data['msg'] = "<div class='alert alert-success' role='alert'>The materials were saved successfully.</div>";
			echo json_encode($data);
		}
	}

	// Code by Kiran on 24th February 2022 for Create Sub Job

	if ($_POST['action'] == "CreateSubJob"){

		$jobid = $_POST['jobid'];
		
		$query = "SELECT * FROM tbljob WHERE JobID = $jobid";
		$result = $mysqli->query($query);
		$row = $result->fetch_array();

		$subJob_query = "SELECT * FROM tbljob WHERE subJobId = $jobid";
		$subJob_result = $mysqli->query($subJob_query);

		if($subJob_result != null)
		{
			$totalCount = mysqli_num_rows($subJob_result);
			$subJobId = $jobid.'.'.($totalCount + 1);
			//$subJob_row = $subJob_result->fetch_array();
		}
		else
		{
			$subJobId = $jobid.'.1';
		}
		

		$data = array();
		if($row){

			$JobID = $row['JobID'];
			$ProjectID = $row['ProjectID'];
			$address = $row['JobAddress'];
			$builder = $row['Builder'];
			$dateentered = $row['DateEntered'];
			$datemeasure = (!empty($row['DateMeasure'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $row['DateMeasure']))) : NULL;
			$measureby = $row['MeasureBy'];
			$ParentJobId = $row['ParentJobId'];
			$status = $row['status'];
			$Deleted = $row['Deleted'];
			
			$insert_stmt = $mysqli->prepare("INSERT INTO tblJob (subJobId,ProjectID, JobAddress, Builder, DateEntered, DateMeasure, MeasureBy, ParentJobId, status, Deleted) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$insert_stmt->bind_param("ssssssssss", $subJobId,$ProjectID, $address, $builder, $dateentered, $datemeasure, $measureby, $JobID, $status, $Deleted); 
			
			$insert_stmt->execute();

			if ($insert_stmt->affected_rows != -1){
				$data['msg'] = "The Sub Job was added successfully";
			} else{
				$data['msg'] = "The Sub Job could not be added";
			}
			
		}

		$data['jobid'] = $jobid;
		echo json_encode($data);
	}	

	// Code by Kiran on 24th February 2022
}
?>