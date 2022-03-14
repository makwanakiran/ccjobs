<?php
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action'])){	
	
	//add a delivery entry
	if ($_POST['action'] == "add"){
	
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$DeliveryType = 1;
		$description = $_POST['inputDescription'];
		$deliverydate = (!empty($_POST['inputDeliveryDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputDeliveryDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$insert_stmt = $mysqli->prepare("INSERT INTO tblDelivery (JobID, Description, DeliveryDate, Notes, DeliveryType) VALUES (?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('isssi', $jobid, $description, $deliverydate, $notes, $DeliveryType); 
		$insert_stmt->execute();
				
		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The delivery entry was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The delivery entry could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit a delivery entry
	if ($_POST['action'] == "edit"){

		$deliveryid = $_POST['deliveryid'];
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$deliverydate = (!empty($_POST['inputDeliveryDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputDeliveryDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$update_stmt = $mysqli->prepare("UPDATE tblDelivery SET JobID = ?, Description = ?, DeliveryDate = ?, Notes = ? WHERE DeliveryID = ?"); 
		$update_stmt->bind_param('isssi', $jobid, $description, $deliverydate, $notes, $deliveryid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The delivery entry was updated successfully.</div>";
			$data['last_insert_id'] = $deliveryid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The delivery entry could not be updated.</div>";
			$data['last_insert_id'] = $deliveryid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//move a delivery entry
	if ($_POST['action'] == "move"){
		
		$deliveryid = $_POST['deliveryid'];
		$deliverydate = (!empty($_POST['deliverydate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['deliverydate']))) : NULL;

		$update_stmt = $mysqli->prepare("UPDATE tblDelivery SET DeliveryDate = ? WHERE DeliveryID = ?"); 
		$update_stmt->bind_param('si', $deliverydate, $deliveryid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The delivery entry was updated successfully.</div>";
			$data['last_insert_id'] = $deliveryid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The delivery entry could not be updated.</div>";
			$data['last_insert_id'] = $deliveryid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//sort delivery entries
	if ($_POST['action'] == "sort"){
		if (isset($_POST['sortorder'])){
			$sortorder = $_POST['sortorder'];

			$sort = 1;
			foreach ($sortorder as $deliveryid){
				$update_stmt = $mysqli->prepare("UPDATE tblDelivery SET SortOrder = ? WHERE DeliveryID = ?"); 
				$update_stmt->bind_param('ii', $sort, $deliveryid); 
				$update_stmt->execute();

				$sort++;
			}
		}
	}

	//delete a delivery entry
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];

			$stmt = $mysqli->prepare("DELETE FROM tblDelivery WHERE DeliveryID = ? LIMIT 1");
			$stmt->bind_param("s", $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected delivery entry was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected delivery entry could not be deleted</div>";
				
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