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
		$description = $_POST['inputDescription'];
		$DeliveryType = 2;
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$deliverydate = (!empty($_POST['inputDeliveryDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputDeliveryDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$insert_stmt = $mysqli->prepare("INSERT INTO tblDelivery (JobID, Description, DeliveryDate, Notes, DeliveryType) VALUES (?, ?, ?, ?, ?)");
		$insert_stmt->bind_param('isssi', $jobid, $description, $deliverydate, $notes, $DeliveryType); 
		$insert_stmt->execute();
	
		print_r($insert_stmt);die;

		if ($insert_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The delivery staff entry was added successfully.</div>";
			$data['last_insert_id'] = $insert_stmt->insert_id;
			$data['action'] = "edit";
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The delivery staff entry could not be added</div>";
			$data['action'] = "add";
		}
			
		echo json_encode($data);
	}
	
	//edit a schedule entry
	if ($_POST['action'] == "edit")
	{
		$deliveryid = $_POST['deliveryid'];
		$deliveryresult = "SELECT * FROM tblDelivery WHERE DeliveryID = ".$deliveryid; 
		$deliveryresult_single = $mysqli->query($deliveryresult);
		$delivery_row = $deliveryresult_single->fetch_array();
		if ($delivery_row['DeliveryID'] != '' && $delivery_row['DeliveryID'] != 0)
		{
			$data['msg'] = "<div class='alert alert-success' role='alert'>The delivery staff entry was edited successfully.</div>";
			$data['DeliveryID'] = $delivery_row['DeliveryID'];
			$data['JobID'] = $delivery_row['JobID'];
			$data['Description'] = $delivery_row['Description'];
			$data['DeliveryDate'] = !empty($delivery_row['DeliveryDate']) ? date('d-m-Y',strtotime($delivery_row['DeliveryDate'])) : "";
			$data['Notes'] = $delivery_row['Notes'];
		}
		else
		{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The delivery staff entry could not be edited.</div>";
			$data['last_insert_id'] = $deliveryid;
		}
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//add a schedule entry
	if ($_POST['action'] == "update")
	{
		$deliveryid = $_POST['deliveryid'];
		$jobid = (!empty($_POST['inputJobID'])) ? $_POST['inputJobID'] : NULL;
		$description = $_POST['inputDescription'];
		$deliverydate = (!empty($_POST['inputDeliveryDate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['inputDeliveryDate']))) : NULL;
		$notes = $_POST['inputNotes'];

		$update_stmt = $mysqli->prepare("UPDATE tblDelivery SET JobID = ?, Description = ?, DeliveryDate = ?, Notes = ? WHERE DeliveryID = ?"); 
		$update_stmt->bind_param('isssi',  $jobid, $description, $deliverydate, $notes, $deliveryid); 
		$update_stmt->execute();
		
		if ($update_stmt->affected_rows != -1){
			$data['msg'] = "<div class='alert alert-success' role='alert'>The schedule entry was updated successfully.</div>";
			$data['last_insert_id'] = $deliveryid;
		}
		else{
			$data['msg'] = "<div class='alert alert-danger' role='alert'>The schedule entry could not be updated.</div>";
			$data['last_insert_id'] = $deliveryid;
		}
		
		$data['action'] = "edit";
		echo json_encode($data);
	}

	//move a schedule entry
	/*if ($_POST['action'] == "move"){
		
		$scheduleid = $_POST['scheduleid'];
		$jobid = $_POST['jobid'];
		$scheduledate = (!empty($_POST['scheduledate'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['scheduledate']))) : NULL;

		$update_stmt = $mysqli->prepare("UPDATE tblDelivery SET JobID = ?, DeliveryDate = ? WHERE DeliveryID = ?"); 
		$update_stmt->bind_param('isi', $jobid, $scheduledate, $scheduleid); 
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
	}*/

	//sort schedule entries
	/*if ($_POST['action'] == "sort"){
		if (isset($_POST['sortorder'])){
			$sortorder = $_POST['sortorder'];

			$sort = 1;
			foreach ($sortorder as $scheduleid){
				$update_stmt = $mysqli->prepare("UPDATE tblDelivery SET SortOrder = ? WHERE DeliveryID = ?"); 
				$update_stmt->bind_param('ii', $sort, $scheduleid); 
				$update_stmt->execute();

				$sort++;
			}
		}
	}*/

	//delete a schedule entry
	if ($_POST['action'] == "delete"){
		if (isset($_POST['deleteid'])){
			$deleteid = $_POST['deleteid'];

			$stmt = $mysqli->prepare("DELETE FROM tblDelivery WHERE DeliveryID = ? LIMIT 1");
			$stmt->bind_param("s", $deleteid);     
			$stmt->execute();
			
			if ($stmt->affected_rows != -1)
				echo "<div class='alert alert-success' role='alert'>The selected delivery staff entry was deleted successfully</div>";
			else
				echo "<div class='alert alert-danger' role='alert'>The selected delivery staff entry could not be deleted</div>";
				
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