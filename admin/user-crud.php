<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//add a user
	if ($_POST['action'] == "add"){
		
		$name = $_POST['inputFullName'];
		$username = $_POST['inputUsername']; 
		$email = $_POST['inputEmail']; 
		$isforeman = isset($_POST['inputIsForeman']) ? $_POST['inputIsForeman'] : 0;
		$isdraftsman = isset($_POST['inputIsDraftsman']) ? $_POST['inputIsDraftsman'] : 0;
		$iscnc = isset($_POST['inputIsCNC']) ? $_POST['inputIsCNC'] : 0;
		$isedging = isset($_POST['inputIsEdging']) ? $_POST['inputIsEdging'] : 0;
		$isassembler = isset($_POST['inputIsAssembler']) ? $_POST['inputIsAssembler'] : 0;
		$isinstaller = isset($_POST['inputIsInstaller']) ? $_POST['inputIsInstaller'] : 0;
		$isdelivery = isset($_POST['inputIsDelivery']) ? $_POST['inputIsDelivery'] : 0;
		$ismaint = isset($_POST['inputIsMaint']) ? $_POST['inputIsMaint'] : 0;
		$isstone = isset($_POST['inputIsStone']) ? $_POST['inputIsStone'] : 0;
		$sendalerts = isset($_POST['inputSendAlerts']) ? $_POST['inputSendAlerts'] : 0;
		$jobApprove = isset($_POST['inputJobApprove']) ? $_POST['inputJobApprove'] : 0;
		$active = isset($_POST['inputActive']) ? $_POST['inputActive'] : 0;

		$insert_stmt = $mysqli->prepare("INSERT INTO tblUser (FullName, UserID, Email, IsForeman, IsDraftsman, IsCNC, IsEdging, IsAssembler, IsInstaller, IsDelivery, IsMaint, IsStone, SendAlerts,JobApprove, Active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('sssiiiiiiiiiiii', $name, $username, $email, $isforeman, $isdraftsman, $iscnc, $isedging, $isassembler, $isinstaller, $isdelivery, $ismaint, $isstone, $sendalerts,$jobApprove, $active); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The user was added successfully.</div>";
			$data['last_insert_id'] = $username;
			$data['action'] = "edit";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The user could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit an existing user
	if ($_POST['action'] == "edit"){
		
		$userid = $_POST['userid'];
		$name = $_POST['inputFullName'];
		$username = $_POST['inputUsername']; 
		$email = $_POST['inputEmail']; 
		$isforeman = isset($_POST['inputIsForeman']) ? $_POST['inputIsForeman'] : 0;
		$isdraftsman = isset($_POST['inputIsDraftsman']) ? $_POST['inputIsDraftsman'] : 0;
		$iscnc = isset($_POST['inputIsCNC']) ? $_POST['inputIsCNC'] : 0;
		$isedging = isset($_POST['inputIsEdging']) ? $_POST['inputIsEdging'] : 0;
		$isassembler = isset($_POST['inputIsAssembler']) ? $_POST['inputIsAssembler'] : 0;
		$isinstaller = isset($_POST['inputIsInstaller']) ? $_POST['inputIsInstaller'] : 0;	
		$isdelivery = isset($_POST['inputIsDelivery']) ? $_POST['inputIsDelivery'] : 0;	
		$ismaint = isset($_POST['inputIsMaint']) ? $_POST['inputIsMaint'] : 0;	
		$isstone = isset($_POST['inputIsStone']) ? $_POST['inputIsStone'] : 0;	
		$sendalerts = isset($_POST['inputSendAlerts']) ? $_POST['inputSendAlerts'] : 0;
		$jobApprove = isset($_POST['inputJobApprove']) ? $_POST['inputJobApprove'] : 0;
		$active = isset($_POST['inputActive']) ? $_POST['inputActive'] : 0;
		
		$update_stmt = $mysqli->prepare("UPDATE tblUser SET FullName = ?, UserID = ?, Email = ?, IsForeman = ?, IsDraftsman = ?, IsCNC = ?, IsEdging = ?, IsAssembler = ?, IsInstaller = ?, IsDelivery = ?, IsMaint = ?, IsStone = ?, SendAlerts = ?, JobApprove = ?, Active = ? WHERE UserID = ?"); 
		$update_stmt->bind_param('sssiiiiiiiiiiiis', $name, $username, $email, $isforeman, $isdraftsman, $iscnc, $isedging, $isassembler, $isinstaller, $isdelivery, $ismaint, $isstone, $sendalerts,$jobApprove,$active, $userid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The user was updated successfully.</div>";
			$data['last_insert_id'] = $username;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The user could not be updated.</div>";
			$data['last_insert_id'] = $userid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}
	
	//delete user
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];
			$stmt = $mysqli->prepare("DELETE FROM tblUser WHERE UserID = ? LIMIT 1");
			$stmt->bind_param("s",$deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected user was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected user could not be deleted</div>";
				
			$stmt->close();
		}
	}
}
?>