<?php
	include("functions.php");

	sec_session_start();
	if(!isset($_SESSION['user_id'])){
		header("location:index.php");
		die();
	}
	
	include("config.php");

	$jobid = isset($_GET['jobid']) ? (int)$_GET['jobid'] : die('ERROR: Job Number not found.');

	if ($stmt = $mysqli->prepare("SELECT JobID, JobAddress, Builder, DateMeasure, MeasureBy FROM tblJob WHERE JobID = ? LIMIT 1")) { 
		$stmt->bind_param('i', $jobid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($formjobid, $formjobaddress, $formbuilder, $formdatemeasure, $formmeasureby);
		$stmt->fetch();
	}

	if (empty($formjobid)){
		header("location:job-lookup.php");
		die();
	}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">

    <title>Challenge Cabinets Job Management System</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=1.1" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
			<div class="row">
				<div class="col-xs-6 col-sm-4 col-md-3">
					<img src="img/logo_large.png" class="img-responsive" alt="Challenge Cabinets">
					<br>
				</div>
				<div class="col-sm-8 col-md-9">
					<ul class="nav nav-pills pull-right">
						<li role="presentation"><a href="index.php">Home</a></li>
						<li role="presentation"><a href="job-lookup.php">Change Job</a></li>
						<li role="presentation"><a href="job-search.php">Search</a></li>
						<li role="presentation"><a href="my-jobs.php">Job</a></li>
					</ul>
					
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="well">
					<?php 
					$roleArray = array();
						if( $_SESSION['is_foreman'] == 1 )
						{
							$roleArray[] = ucfirst('foreman');
						}
						if( $_SESSION['is_draftsman'] == 1 )
						{
							$roleArray[] = ucfirst('draftsman');
						}
						if( $_SESSION['is_cnc'] == 1 )
						{
							$roleArray[] = ucfirst('cnc');
						}
						if( $_SESSION['is_edging'] == 1 )
						{
							$roleArray[] = ucfirst('edging');
						}
						if( $_SESSION['is_assembler'] == 1 )
						{	
							$roleArray[] = ucfirst('assembler');
						}
						if( $_SESSION['is_installer'] == 1 )
						{
							$roleArray[] = ucfirst('installer');
						}
						if( $_SESSION['is_delivery'] == 1 )
						{
							$roleArray[] = ucfirst('delivery');
						}
						if( $_SESSION['is_maint'] == 1 )
						{
							$roleArray[] = ucfirst('maint');
						}
						if( $_SESSION['is_JobApprove'] == 1 )
						{
							$roleArray[] = ucfirst('JobApprove');
						}
					?>				
							Current User: <strong><?php echo $_SESSION['full_name'] ?></strong> 
							<br>
							User Role: <strong><?php 
								foreach ($roleArray as $key => $value) {
									
									echo $value;
									if($key+1 != count($roleArray))
									{
										echo ', ';
									}

								}
							 ?></strong>
					</div>
					<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($_GET['jobid'])) { echo $_GET['jobid']; } ?>">
					
					<div class="panel panel-default">
						<div class="panel-body">
							<h1><?php echo "Job #" . $formjobid ?> </h1>
							<h3><?php echo  $formjobaddress ?></h3>
							<?php echo (!empty($formbuilder) ? '<h4>'.$formbuilder.'</h4>' : ''); ?>
							<a class="btn btn-sm btn-danger" href='<?php echo "https://www.google.com/maps?q=" . urlencode($formjobaddress); ?>' target="_blank"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span> View Map</a>
							<?php if($_SESSION['is_draftsman'] == 1){ ?><button class="btn btn-primary btn-sm ml-3 create_sub_job">Create Sub Job</button> <?php } ?>
						</div>
					</div>

					<div>
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist" id="myTab">
							<li role="presentation"><a href="#draftsman" aria-controls="draftsman" role="tab" data-toggle="tab">Draftsman</a></li>
							<li role="presentation"><a href="#stone" aria-controls="stone" role="tab" data-toggle="tab">Stone</a></li>
							<!-- <li role="presentation"><a href="#cnc" aria-controls="cnc" role="tab" data-toggle="tab">CNC</a></li>
							<li role="presentation"><a href="#edging" aria-controls="edging" role="tab" data-toggle="tab">Edging</a></li> -->
							<li role="presentation" class="active"><a href="#assembler" aria-controls="assembler" role="tab" data-toggle="tab">Assembler</a></li>
							<li role="presentation"><a href="#installer" aria-controls="installer" role="tab" data-toggle="tab">Installer</a></li>
							<li role="presentation"><a href="#maintenance" aria-controls="maintenance" role="tab" data-toggle="tab">Maintenance</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">

							<div role="tabpanel" class="tab-pane" id="draftsman">
								<?php
									if (!empty($formmeasureby))
										echo "<strong>Assigned to:</strong> " . get_name_from_id($formmeasureby, $mysqli) . "<br>";

									if (!empty($formdatemeasure))
										echo "<strong>Measure Date:</strong> " . date("d-m-Y", strtotime($formdatemeasure)) . "<br>"; 
								?>

								<br>
								<div class="row">
									<div class="col-md-4">
								<?php	

									$query = "SELECT tblJobTaskDraft.TaskID, tblTask.Weight FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = $formjobid";
									$result = $mysqli->query($query);
									$taskarray[] = "";
									$weight = 2;

									while($task = $result->fetch_array()) {
										$taskarray[] = $task['TaskID'];
										$weight -= $task['Weight'];
										
									}


									
									if ($weight > 0){
								?>
										<button class="start-draft-btn btn btn-success btn-lg" type="button" <?php if (empty($_SESSION['is_draftsman'])){echo "disabled='disabled'";}?>>Measured</button>

								<?php } 
								else {
									
								?>
									<button class="materials-draft-btn btn btn-danger btn-lg" type="button" data-toggle="collapse" data-target="#materials" aria-expanded="false" aria-controls="materials">Materials</button>
								<?php 
								}
								?>
								
								<btn class="btn btn-lg btn-info plans-btn">Plans</btn>
					
								<a href="index.php" class="btn btn-warning btn-lg">Home</a>
								
								</div>
								<?php
									//$remove_null = array_filter($taskarray, fn($value) => !is_null($value) && $value !== '');
									$remove_null = array_filter( $taskarray );

									$taskarray_data = implode(',', $remove_null); 

									if($taskarray_data != null)
									{

										$taskData_query = "SELECT tblTask.*, tblUser.FullName  FROM tblTask LEFT JOIN tblUser ON tblTask.user_id = tblUser.UserID WHERE TaskID NOT IN ($taskarray_data) And TaskID != 1";
									}
									else
									{
										$taskData_query = "SELECT tblTask.*, tblUser.FullName  FROM tblTask LEFT JOIN tblUser ON tblTask.user_id = tblUser.UserID WHERE TaskID != 1";
									}
									$task_result = mysqli_query($mysqli, $taskData_query);
									if($task_result != null)
									{
										$task_result_list = mysqli_num_rows($task_result) ;
									}
									else
									{
										$task_result_list = -1;
									}

									if($task_result_list > 0){

								?>
									
									<!-- <div class="col-md-3">
										<div class="form-group err_roomList">
											<label>Room Type</label>
										  <select class="form-control" id="roomList">
										  	 <option value="">select</option>
										    <?php

											    while($row = mysqli_fetch_assoc($task_result)) 
											    {
											    	
											    	echo '<option value="'.$row['TaskID'].'">'.$row['TaskName'].' - '.$row['FullName'].'</option>';
											    }
										    ?>
										  </select>
										</div>
									</div>
									<div class="col-md-1">
										<btn class="btn btn-lg btn-primary create-room">Create New Room Type</btn>
									</div> -->
									<?php } ?>
									<div class="col-md-2">
										<btn class="btn btn-lg btn-success " aria-hidden="true" data-toggle="modal" data-target="#newRoom" >Add Room</btn>
										
									</div>
									<?php

									$query = "SELECT tblJobTaskDraft.*, tblTask.TaskName FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = $jobid ORDER BY tblJobTaskDraft.TaskID";
									$result = $mysqli->query($query);

									$signOff = 0;
									while($get_is_off = $result->fetch_array()) {
										
										if($get_is_off['is_off'] == 0)
										{
											$signOff = 1;
										}
									}


									if ($_SESSION['is_JobApprove'] == 1 && $signOff == 1){ ?>

									<div class="col-md-2">
										<btn class="btn btn-lg btn-info sing-off-all-btn text-right" >Sign Off All</btn>
									</div>
									<div class="col-md-1">
										<btn class="btn btn-lg btn-info sing-off-alert-btn text-right " >Sign Off Alert All</btn>
									</div>
									
								<?php } ?>

								</div>
								<br><br>
								<div class="collapse" id="materials">
									<div class="well">
										<form id='materials-form' class="materials-form form-horizontal" action='#' method='post'>
											<fieldset <?php if ($_SESSION['is_draftsman'] <> 1) { echo "disabled='disabled'";} ?>>
												<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($jobid)) { echo $jobid; } ?>">
												<input type="hidden" id="action" name="action" value="savematerials">

												<?php 
													//check carcass record exists
													$materialquery = "SELECT JobMaterialID FROM tblJobMaterial WHERE JobID = $jobid AND IsCarcass <> 0";
													$materialresult = $mysqli->query($materialquery);

													if ($materialresult->num_rows == 0){
														//insert carcass record
														$boarddescription = "CARCASS";
														$boardtype = "WHITE16MEL";
														$iscarcass = 1;

														$insert_stmt = $mysqli->prepare("INSERT INTO tblJobMaterial (JobID, BoardDescription, BoardType, IsCarcass) VALUES (?, ?, ?, ?)");
														$insert_stmt->bind_param('issi', $jobid, $boarddescription, $boardtype, $iscarcass); 
														$insert_stmt->execute();
													}

													$materialquery = "SELECT JobMaterialID, JobID, BoardDescription, BoardType, BoardQuantity, IsCarcass FROM tblJobMaterial WHERE JobID = $jobid ORDER BY IsCarcass DESC, JobMaterialID ";
													$materialresult = $mysqli->query($materialquery);
													
													$hideheadings = $materialresult->num_rows == 0 ? "style='display:none;'" : "";
												?>

														<div id="tableheadings" class="form-group gutter-10" <?php echo $hideheadings; ?>>
															<div class="col-xs-4">
																<strong>Description / Colour</strong>
															</div>
															<div class="col-xs-4">
																<strong>Board Type</strong>
															</div>
															<div class="col-xs-3">
																<strong>Quantity</strong>
															</div>
															<div class="col-xs-1">
															</div>
														</div>

												<?php 
													$count = 0;
														while($materialrow = $materialresult->fetch_array()){
														?>
															<div class="form-group itemrow gutter-10">
																<div class="col-xs-4">
																	<input type="hidden" id="inputJobMaterialID[]" name="inputJobMaterialID[]" value="<?php echo $materialrow['JobMaterialID']; ?>">
																	<input type="hidden" id="inputMaterialDelete[]" name="inputMaterialDelete[]" value="0">
																	<input type="text" class="form-control" id="inputBoardDescription[<?php echo $count; ?>]" name="inputBoardDescription[<?php echo $count; ?>]" placeholder="Colour" required value="<?php echo $materialrow['BoardDescription']; ?>" <?php if ($materialrow['IsCarcass']<>0){echo "readonly='readonly'";} ?>>
																</div>
																<div class="col-xs-4">
																	<?php if ($materialrow['IsCarcass']<>0){ ?>
																		<input type="text" class="form-control" id="inputBoardType[<?php echo $count; ?>]" name="inputBoardType[<?php echo $count; ?>]" placeholder="Board Type" required value="<?php echo $materialrow['BoardType']; ?>" readonly='readonly'>
																	<?php } else { ?>
																		<select id="inputBoardType[<?php echo $count; ?>]" name="inputBoardType[<?php echo $count; ?>]" class="form-control" required>
																			<option value="">Please Select</option>
																			<?php
																				$boardquery = "SELECT BoardType FROM tblBoardType ORDER BY BoardType";
																				$boardresult = $mysqli->query($boardquery);			

																				while($boardrow = $boardresult->fetch_array())
																				{	
																					if (isset($materialrow['BoardType']))
																						$selected = ($boardrow['BoardType'] == $materialrow['BoardType']) ? " SELECTED" : ""; 	
																					else
																						$selected = "";

																					echo "<option value='" . $boardrow['BoardType'] . "' $selected>" . $boardrow['BoardType'] . "</option>";
																				}
																			?>
																		</select>		
																	<?php } ?>
																															
																</div>
																<div class="col-xs-3">
																	<input type="text" class="form-control" id="inputBoardQuantity[<?php echo $count; ?>]" name="inputBoardQuantity[<?php echo $count; ?>]" placeholder="Quantity" digits="true" required value="<?php echo $materialrow['BoardQuantity']; ?>">
																</div>
																<div class="col-xs-1">
																	<button type="button" class="btn btn-default removeItemButton" <?php if ($materialrow['IsCarcass']<>0){echo "disabled='disabled'";} ?>><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
																</div>
															</div>

														<?php
															$count++;
														}
												
												?>

												<div class="form-group hide gutter-10" id="itemTemplate">
													<div class="col-xs-4">
														<input type="hidden" id="inputMaterialCancelAdd[]" name="inputMaterialCancelAdd[]" value="1">
														<input type="text" class="form-control" id="inputBoardDescriptionAdd[]" name="inputBoardDescriptionAdd[]" placeholder="Colour" required>
													</div>
													<div class="col-xs-4">
														<select id="inputBoardTypeAdd[]" name="inputBoardTypeAdd[]" class="form-control" required>
															<option value="">Please Select</option>
															<?php
																$boardquery = "SELECT BoardType FROM tblBoardType ORDER BY BoardType";
																$boardresult = $mysqli->query($boardquery);			

																while($boardrow = $boardresult->fetch_array())
																{	
																	echo "<option value='" . $boardrow['BoardType'] . "'>" . $boardrow['BoardType'] . "</option>";
																}
															?>
														</select>													
													</div>
													<div class="col-xs-3">
														<input type="text" class="form-control" id="inputBoardQuantityAdd[]" name="inputBoardQuantityAdd[]" placeholder="Quantity" required>
													</div>
													<div class="col-xs-1">
														<button type="button" class="btn btn-default removeItemButton"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
													</div>
												</div>
												
												<div class="form-group gutter-10">
													<div class="col-sm-12">
														<button type="button" class="btn btn-default addItemButton">Add Row</button>
														<button type="submit" id="save-btn" class="btn btn-primary">Save &amp; Transfer</button>
													</div>
												</div>
												
											</fieldset>
										</form>
									</div>
								</div>


								<?php 
									$query = "SELECT tblJobTaskDraft.*, tblTask.TaskName FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = $jobid ORDER BY tblJobTaskDraft.TaskID";
									$result = $mysqli->query($query);

									while($row = $result->fetch_array()){
								?>
									<div class="panel panel-default">
										<div class="panel-body">
											<h2><?php echo $row['TaskName'] ?></h2>
											<div class="row">
												<div class="col-sm-4">
													<div class="alert alert-warning" role="alert">
														<div class="row">
															<div class="col-xs-3"><i class="fa fa-clock-o fa-5x text-warning"></i></div>
															<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Measured</strong><br>" . date('d-m-Y', strtotime($row['DateStarted'])) . "<br>by " . get_name_from_id($row['StartedBy'], $mysqli) ?></div>
														</div>
													</div>
												</div>
												<?php 
													if (!empty($row['DateDrawn'])){
												?>
														<div class='col-sm-4'>
															<div class='alert alert-info' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-list-ol fa-5x text-info"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Drawing Started</strong><br>" . date('d-m-Y', strtotime($row['DateDrawn'])) . "<br>by " . get_name_from_id($row['DrawnBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>

												<?php 
													if (!empty($row['DateCompleted'])){
												?>
														<div class='col-sm-4'>
															<div class='alert alert-success' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-check-square-o fa-5x text-success"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Sent To CNC</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>
											</div>

											<?php 	
												$missingitems = jobtaskdraft_missing_items($row['JobID'], $row['TaskID'], $mysqli);

												if ($missingitems == true)
													echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains missing items!</strong></span><br><br>';
											?>
																							
													<button class="btn btn-primary btn-lg" type="button" data-toggle="collapse" data-target="#checklistdraft<?php echo $row['TaskID']?>" aria-expanded="false" aria-controls="checklist<?php echo $row['TaskID']?>">Notes &amp; Checklist</button>
											
											<?php
													if ($_SESSION['is_draftsman'] <> 0 && empty($row['DateDrawn']) && empty($row['DateCompleted'])){
											?>
														<button class="drawn-draft-btn btn btn-success btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Start Drawing</button>
											<?php } ?>

											<?php
													if ($_SESSION['is_draftsman'] <> 0 && $missingitems == false && !empty($row['DateDrawn']) && empty($row['DateCompleted'])){
											?>
														<button class="complete-draft-btn btn btn-success btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Sent To CNC</button>
											<?php } ?>

											<?php
													if ($_SESSION['is_JobApprove'] == 1 && $row['is_off'] == 0 && empty($row['DateCompleted'])){
											?>
														<button class="sing-off-btn btn btn-warning btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Sign Off</button>
														<button class="sing-off-btn-alert btn btn-warning btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Sign Off Alert</button>
											<?php } ?>	
											<?php
													if ($_SESSION['is_JobApprove'] == 1){
											?>
														<button class="delete-room-btn btn btn-danger btn-lg" type="button" data-id="<?php echo $_SESSION['user_id'] ?>" value='<?php echo $row['TaskID'] ?>'>Delete</button>
											<?php } ?>	
											<br><br>
											<div class="collapse" id="checklistdraft<?php echo $row['TaskID']?>">
												<div class="well">
													<form id='checklist-draft-form' class="checklist-draft-form" action='#' method='post'>
														<fieldset <?php if ($_SESSION['is_draftsman'] <> 1 || !empty($row['DateCompleted'])) { echo "disabled='disabled'";} ?>>
															<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($row['JobID'])) { echo $row['JobID']; } ?>">
															<input type="hidden" id="taskid" name="taskid" value="<?php if (isset($row['TaskID'])) { echo $row['TaskID']; } ?>">
															<input type="hidden" id="action" name="action" value="savechecklist">
															
															<div class="form-group">
																<label for="inputNotes">Measure Notes</label>
																<textarea class="form-control" id="inputNotes" name="inputNotes" rows="6"><?php if (isset($row['Notes'])) { echo htmlspecialchars($row['Notes'], ENT_QUOTES); } ?></textarea>
															</div>																																									
															<!-- <div class="form-group">
																<label for="inputMissingItems">Missing Items</label>
																<textarea class="form-control" id="inputMissingItems" name="inputMissingItems" rows="6"><?php if (isset($row['MissingItems'])) { echo htmlspecialchars($row['MissingItems'], ENT_QUOTES); } ?></textarea>
															</div>
															<div class="form-group">
																<label>
																	<input name="inputMissingItemsComplete" type="checkbox" value="1" <?php if (isset($row['MissingItemsComplete'])) { if ($row['MissingItemsComplete']==1){ echo " CHECKED"; } } ?>> All missing items completed
																</label>
															</div> -->
															
															<button type="submit" id="save-btn" class="btn btn-primary">Save</button>
														</fieldset>
													</form>
												</div>
											</div>

										</div>
									</div>
								<?php
									}
								?>
							</div>
							<div role="tabpanel" class="tab-pane" id="stone">
								<?php
									if (!empty($formmeasureby))
										echo "<strong>Assigned to:</strong> " . get_name_from_id($formmeasureby, $mysqli) . "<br>";

									if (!empty($formdatemeasure))
										echo "<strong>Measure Date:</strong> " . date("d-m-Y", strtotime($formdatemeasure)) . "<br>"; 
								?>

								<br>
								<div class="row">
									<div class="col-md-4">
								<?php	

									$query = "SELECT tblJobTaskDraft.TaskID, tblTask.Weight FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = $formjobid";
									$result = $mysqli->query($query);
									$taskarray[] = "";
									$weight = 2;

									while($task = $result->fetch_array()) {
										$taskarray[] = $task['TaskID'];
										$weight -= $task['Weight'];
										
									}


									
									if ($weight > 0){
								?>
										<button class="start-draft-btn btn btn-success btn-lg" type="button" <?php if (empty($_SESSION['is_draftsman'])){echo "disabled='disabled'";}?>>Measured</button>

								<?php } 
								else {
									
								?>
									<button class="materials-draft-btn btn btn-danger btn-lg" type="button" data-toggle="collapse" data-target="#materials" aria-expanded="false" aria-controls="materials">Materials</button>
								<?php 
								}
								?>
								
								<btn class="btn btn-lg btn-info plans-btn">Plans</btn>
					
								<a href="index.php" class="btn btn-warning btn-lg">Home</a>
								
								</div>
								<?php
									//$remove_null = array_filter($taskarray, fn($value) => !is_null($value) && $value !== '');
									$remove_null = array_filter( $taskarray );

									$taskarray_data = implode(',', $remove_null); 

									if($taskarray_data != null)
									{

										$taskData_query = "SELECT tblTask.*, tblUser.FullName  FROM tblTask LEFT JOIN tblUser ON tblTask.user_id = tblUser.UserID WHERE TaskID NOT IN ($taskarray_data) And TaskID != 1";
									}
									else
									{
										$taskData_query = "SELECT tblTask.*, tblUser.FullName  FROM tblTask LEFT JOIN tblUser ON tblTask.user_id = tblUser.UserID WHERE TaskID != 1";
									}
									$task_result = mysqli_query($mysqli, $taskData_query);
									if($task_result != null)
									{
										$task_result_list = mysqli_num_rows($task_result) ;
									}
									else
									{
										$task_result_list = -1;
									}

									if($task_result_list > 0){

								?>
									
									<!-- <div class="col-md-3">
										<div class="form-group err_roomList">
											<label>Room Type</label>
										  <select class="form-control" id="roomList">
										  	 <option value="">select</option>
										    <?php

											    while($row = mysqli_fetch_assoc($task_result)) 
											    {
											    	
											    	echo '<option value="'.$row['TaskID'].'">'.$row['TaskName'].' - '.$row['FullName'].'</option>';
											    }
										    ?>
										  </select>
										</div>
									</div>
									<div class="col-md-1">
										<btn class="btn btn-lg btn-primary create-room">Create New Room Type</btn>
									</div> -->
									<?php } ?>
									<div class="col-md-2">
										<btn class="btn btn-lg btn-success " aria-hidden="true" data-toggle="modal" data-target="#newRoom" >Add Room</btn>
										
									</div>
									<?php

									$query = "SELECT tblJobTaskDraft.*, tblTask.TaskName FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = $jobid ORDER BY tblJobTaskDraft.TaskID";
									$result = $mysqli->query($query);

									$signOff = 0;
									while($get_is_off = $result->fetch_array()) {
										
										if($get_is_off['is_off'] == 0)
										{
											$signOff = 1;
										}
									}


									if ($_SESSION['is_JobApprove'] == 1 && $signOff == 1){ ?>

									<div class="col-md-2">
										<btn class="btn btn-lg btn-info sing-off-all-btn text-right" >Sign Off All</btn>
									</div>
									<div class="col-md-1">
										<btn class="btn btn-lg btn-info sing-off-alert-btn text-right " >Sign Off Alert All</btn>
									</div>
									
								<?php } ?>

								</div>
								<br><br>
								<div class="collapse" id="materials">
									<div class="well">
										<form id='materials-form' class="materials-form form-horizontal" action='#' method='post'>
											<fieldset <?php if ($_SESSION['is_draftsman'] <> 1) { echo "disabled='disabled'";} ?>>
												<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($jobid)) { echo $jobid; } ?>">
												<input type="hidden" id="action" name="action" value="savematerials">

												<?php 
													//check carcass record exists
													$materialquery = "SELECT JobMaterialID FROM tblJobMaterial WHERE JobID = $jobid AND IsCarcass <> 0";
													$materialresult = $mysqli->query($materialquery);

													if ($materialresult->num_rows == 0){
														//insert carcass record
														$boarddescription = "CARCASS";
														$boardtype = "WHITE16MEL";
														$iscarcass = 1;

														$insert_stmt = $mysqli->prepare("INSERT INTO tblJobMaterial (JobID, BoardDescription, BoardType, IsCarcass) VALUES (?, ?, ?, ?)");
														$insert_stmt->bind_param('issi', $jobid, $boarddescription, $boardtype, $iscarcass); 
														$insert_stmt->execute();
													}

													$materialquery = "SELECT JobMaterialID, JobID, BoardDescription, BoardType, BoardQuantity, IsCarcass FROM tblJobMaterial WHERE JobID = $jobid ORDER BY IsCarcass DESC, JobMaterialID ";
													$materialresult = $mysqli->query($materialquery);
													
													$hideheadings = $materialresult->num_rows == 0 ? "style='display:none;'" : "";
												?>

														<div id="tableheadings" class="form-group gutter-10" <?php echo $hideheadings; ?>>
															<div class="col-xs-4">
																<strong>Description / Colour</strong>
															</div>
															<div class="col-xs-4">
																<strong>Board Type</strong>
															</div>
															<div class="col-xs-3">
																<strong>Quantity</strong>
															</div>
															<div class="col-xs-1">
															</div>
														</div>

												<?php 
													$count = 0;
														while($materialrow = $materialresult->fetch_array()){
														?>
															<div class="form-group itemrow gutter-10">
																<div class="col-xs-4">
																	<input type="hidden" id="inputJobMaterialID[]" name="inputJobMaterialID[]" value="<?php echo $materialrow['JobMaterialID']; ?>">
																	<input type="hidden" id="inputMaterialDelete[]" name="inputMaterialDelete[]" value="0">
																	<input type="text" class="form-control" id="inputBoardDescription[<?php echo $count; ?>]" name="inputBoardDescription[<?php echo $count; ?>]" placeholder="Colour" required value="<?php echo $materialrow['BoardDescription']; ?>" <?php if ($materialrow['IsCarcass']<>0){echo "readonly='readonly'";} ?>>
																</div>
																<div class="col-xs-4">
																	<?php if ($materialrow['IsCarcass']<>0){ ?>
																		<input type="text" class="form-control" id="inputBoardType[<?php echo $count; ?>]" name="inputBoardType[<?php echo $count; ?>]" placeholder="Board Type" required value="<?php echo $materialrow['BoardType']; ?>" readonly='readonly'>
																	<?php } else { ?>
																		<select id="inputBoardType[<?php echo $count; ?>]" name="inputBoardType[<?php echo $count; ?>]" class="form-control" required>
																			<option value="">Please Select</option>
																			<?php
																				$boardquery = "SELECT BoardType FROM tblBoardType ORDER BY BoardType";
																				$boardresult = $mysqli->query($boardquery);			

																				while($boardrow = $boardresult->fetch_array())
																				{	
																					if (isset($materialrow['BoardType']))
																						$selected = ($boardrow['BoardType'] == $materialrow['BoardType']) ? " SELECTED" : ""; 	
																					else
																						$selected = "";

																					echo "<option value='" . $boardrow['BoardType'] . "' $selected>" . $boardrow['BoardType'] . "</option>";
																				}
																			?>
																		</select>		
																	<?php } ?>
																															
																</div>
																<div class="col-xs-3">
																	<input type="text" class="form-control" id="inputBoardQuantity[<?php echo $count; ?>]" name="inputBoardQuantity[<?php echo $count; ?>]" placeholder="Quantity" digits="true" required value="<?php echo $materialrow['BoardQuantity']; ?>">
																</div>
																<div class="col-xs-1">
																	<button type="button" class="btn btn-default removeItemButton" <?php if ($materialrow['IsCarcass']<>0){echo "disabled='disabled'";} ?>><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
																</div>
															</div>

														<?php
															$count++;
														}
												
												?>

												<div class="form-group hide gutter-10" id="itemTemplate">
													<div class="col-xs-4">
														<input type="hidden" id="inputMaterialCancelAdd[]" name="inputMaterialCancelAdd[]" value="1">
														<input type="text" class="form-control" id="inputBoardDescriptionAdd[]" name="inputBoardDescriptionAdd[]" placeholder="Colour" required>
													</div>
													<div class="col-xs-4">
														<select id="inputBoardTypeAdd[]" name="inputBoardTypeAdd[]" class="form-control" required>
															<option value="">Please Select</option>
															<?php
																$boardquery = "SELECT BoardType FROM tblBoardType ORDER BY BoardType";
																$boardresult = $mysqli->query($boardquery);			

																while($boardrow = $boardresult->fetch_array())
																{	
																	echo "<option value='" . $boardrow['BoardType'] . "'>" . $boardrow['BoardType'] . "</option>";
																}
															?>
														</select>													
													</div>
													<div class="col-xs-3">
														<input type="text" class="form-control" id="inputBoardQuantityAdd[]" name="inputBoardQuantityAdd[]" placeholder="Quantity" required>
													</div>
													<div class="col-xs-1">
														<button type="button" class="btn btn-default removeItemButton"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
													</div>
												</div>
												
												<div class="form-group gutter-10">
													<div class="col-sm-12">
														<button type="button" class="btn btn-default addItemButton">Add Row</button>
														<button type="submit" id="save-btn" class="btn btn-primary">Save &amp; Transfer</button>
													</div>
												</div>
												
											</fieldset>
										</form>
									</div>
								</div>


								<?php 
									$query = "SELECT tblJobTaskDraft.*, tblTask.TaskName FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = $jobid ORDER BY tblJobTaskDraft.TaskID";
									$result = $mysqli->query($query);

									while($row = $result->fetch_array()){
								?>
									<div class="panel panel-default">
										<div class="panel-body">
											<h2><?php echo $row['TaskName'] ?></h2>
											<div class="row">
												<div class="col-sm-4">
													<div class="alert alert-warning" role="alert">
														<div class="row">
															<div class="col-xs-3"><i class="fa fa-clock-o fa-5x text-warning"></i></div>
															<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Measured</strong><br>" . date('d-m-Y', strtotime($row['DateStarted'])) . "<br>by " . get_name_from_id($row['StartedBy'], $mysqli) ?></div>
														</div>
													</div>
												</div>
												<?php 
													if (!empty($row['DateDrawn'])){
												?>
														<div class='col-sm-4'>
															<div class='alert alert-info' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-list-ol fa-5x text-info"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Drawing Started</strong><br>" . date('d-m-Y', strtotime($row['DateDrawn'])) . "<br>by " . get_name_from_id($row['DrawnBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>

												<?php 
													if (!empty($row['DateCompleted'])){
												?>
														<div class='col-sm-4'>
															<div class='alert alert-success' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-check-square-o fa-5x text-success"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Sent To CNC</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>
											</div>

											<?php 	
												$missingitems = jobtaskdraft_missing_items($row['JobID'], $row['TaskID'], $mysqli);

												if ($missingitems == true)
													echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains missing items!</strong></span><br><br>';
											?>
																							
													<button class="btn btn-primary btn-lg" type="button" data-toggle="collapse" data-target="#checklistdraft<?php echo $row['TaskID']?>" aria-expanded="false" aria-controls="checklist<?php echo $row['TaskID']?>">Notes &amp; Checklist</button>
											
											<?php
													if ($_SESSION['is_draftsman'] <> 0 && empty($row['DateDrawn']) && empty($row['DateCompleted'])){
											?>
														<button class="drawn-draft-btn btn btn-success btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Start Drawing</button>
											<?php } ?>

											<?php
													if ($_SESSION['is_draftsman'] <> 0 && $missingitems == false && !empty($row['DateDrawn']) && empty($row['DateCompleted'])){
											?>
														<button class="complete-draft-btn btn btn-success btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Sent To CNC</button>
											<?php } ?>

											<?php
													if ($_SESSION['is_JobApprove'] == 1 && $row['is_off'] == 0 && empty($row['DateCompleted'])){
											?>
														<button class="sing-off-btn btn btn-warning btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Sign Off</button>
														<button class="sing-off-btn-alert btn btn-warning btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Sign Off Alert</button>
											<?php } ?>	
											<?php
													if ($_SESSION['is_JobApprove'] == 1){
											?>
														<button class="delete-room-btn btn btn-danger btn-lg" type="button" data-id="<?php echo $_SESSION['user_id'] ?>" value='<?php echo $row['TaskID'] ?>'>Delete</button>
											<?php } ?>	
											<br><br>
											<div class="collapse" id="checklistdraft<?php echo $row['TaskID']?>">
												<div class="well">
													<form id='checklist-draft-form' class="checklist-draft-form" action='#' method='post'>
														<fieldset <?php if ($_SESSION['is_draftsman'] <> 1 || !empty($row['DateCompleted'])) { echo "disabled='disabled'";} ?>>
															<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($row['JobID'])) { echo $row['JobID']; } ?>">
															<input type="hidden" id="taskid" name="taskid" value="<?php if (isset($row['TaskID'])) { echo $row['TaskID']; } ?>">
															<input type="hidden" id="action" name="action" value="savechecklist">
															
															<div class="form-group">
																<label for="inputNotes">Measure Notes</label>
																<textarea class="form-control" id="inputNotes" name="inputNotes" rows="6"><?php if (isset($row['Notes'])) { echo htmlspecialchars($row['Notes'], ENT_QUOTES); } ?></textarea>
															</div>																																									
															<!-- <div class="form-group">
																<label for="inputMissingItems">Missing Items</label>
																<textarea class="form-control" id="inputMissingItems" name="inputMissingItems" rows="6"><?php if (isset($row['MissingItems'])) { echo htmlspecialchars($row['MissingItems'], ENT_QUOTES); } ?></textarea>
															</div>
															<div class="form-group">
																<label>
																	<input name="inputMissingItemsComplete" type="checkbox" value="1" <?php if (isset($row['MissingItemsComplete'])) { if ($row['MissingItemsComplete']==1){ echo " CHECKED"; } } ?>> All missing items completed
																</label>
															</div> -->
															
															<button type="submit" id="save-btn" class="btn btn-primary">Save</button>
														</fieldset>
													</form>
												</div>
											</div>

										</div>
									</div>
								<?php
									}
								?>
							</div>
							
							<div role="tabpanel" class="tab-pane" id="cnc">
								<a href="index.php" class="btn btn-warning btn-lg">Home</a>
								<br><br>
								<?php
									$query = "SELECT JobMaterialID FROM tblJobMaterial WHERE JobID = $jobid AND BoardQuantity <> 0 AND (CNCDateCompleted IS NULL OR CNCDateCompleted = '')";
									$result = $mysqli->query($query);
									if ($result->num_rows <> 0)
										echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains incomplete items!</strong></span><br><br>';

									$query = "SELECT JobMaterialID, JobID, BoardDescription, BoardType, BoardQuantity, IsCarcass, CNCNotes, CNCDateCompleted, CNCCompletedBy FROM tblJobMaterial WHERE JobID = $jobid AND BoardQuantity <> 0 ORDER BY IsCarcass DESC, JobMaterialID ";
									$result = $mysqli->query($query);

									while($row = $result->fetch_array()){
										$labeltext = !empty($row['CNCDateCompleted']) ? "label-success" : "label-danger";
								?>
									<div class="panel panel-default">
										<div class="panel-body">
											<div class="row">
												<div class="col-sm-8"><h3><?php echo $row['BoardDescription'] . " - " . $row['BoardType'] ?></h3></div>
												<div class="col-sm-4 text-right"><h3>Quantity: <span class="label <?php echo $labeltext; ?>"><?php echo $row['BoardQuantity'] ?></span></h3></div>
											</div>											
																					
											<?php 
												if (!empty($row['CNCDateCompleted'])){
											?>
												<div class="row">
													<div class='col-sm-4'>
														<div class='alert alert-success' role='alert'>
															<div class="row">
																<div class="col-xs-3"><i class="fa fa-check-square-o fa-5x text-success"></i></div>
																<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Completed</strong><br>" . date('d-m-Y', strtotime($row['CNCDateCompleted'])) . "<br>by " . get_name_from_id($row['CNCCompletedBy'], $mysqli) ?></div>
															</div>
														</div>
													</div>
												</div>
											<?php } ?>
											
											<div class="form-group">
												<label for="inputCNCNotes">Notes</label>
												<textarea class="form-control" id="inputCNCNotes<?php echo $row['JobMaterialID']; ?>" name="inputCNCNotes<?php echo $row['JobMaterialID']; ?>" rows="2" <?php if ($_SESSION['is_cnc'] <> 1 || !empty($row['CNCDateCompleted'])) { echo "disabled='disabled'";} ?>><?php if (isset($row['CNCNotes'])) { echo htmlspecialchars($row['CNCNotes'], ENT_QUOTES); } ?></textarea>
											</div>
											<button class="save-cnc-btn btn btn-primary btn-lg" type="button" value='<?php echo $row['JobMaterialID'] ?>' <?php if ($_SESSION['is_cnc'] <> 1 || !empty($row['CNCDateCompleted'])) { echo "disabled='disabled'";} ?>>Save</button>

											<?php
												if ($_SESSION['is_cnc'] <> 0 && empty($row['CNCDateCompleted'])){
											?>
													<button class="complete-cnc-btn btn btn-success btn-lg" type="button" value='<?php echo $row['JobMaterialID'] ?>'>Complete</button>
											<?php } ?>

										</div>
									</div>
								<?php
									}
								?>
							</div>

							<div role="tabpanel" class="tab-pane" id="edging">
								<a href="index.php" class="btn btn-warning btn-lg">Home</a>
								<br><br>
								<?php 
									$query = "SELECT JobMaterialID FROM tblJobMaterial WHERE JobID = $jobid AND BoardQuantity <> 0 AND (EdgingDateCompleted IS NULL OR EdgingDateCompleted = '')";
									$result = $mysqli->query($query);
									if ($result->num_rows <> 0)
										echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains incomplete items!</strong></span><br><br>';

									$query = "SELECT JobMaterialID, JobID, BoardDescription, BoardType, BoardQuantity, IsCarcass, EdgingNotes, EdgingDateCompleted, EdgingCompletedBy FROM tblJobMaterial WHERE JobID = $jobid AND BoardQuantity <> 0 ORDER BY IsCarcass DESC, JobMaterialID ";
									$result = $mysqli->query($query);

									while($row = $result->fetch_array()){
										$labeltext = !empty($row['EdgingDateCompleted']) ? "label-success" : "label-danger";
								?>
									<div class="panel panel-default">
										<div class="panel-body">
											<div class="row">
												<div class="col-sm-8"><h3><?php echo $row['BoardDescription'] . " - " . $row['BoardType'] ?></h3></div>
												<div class="col-sm-4 text-right"><h3>Quantity: <span class="label <?php echo $labeltext; ?>"><?php echo $row['BoardQuantity'] ?></span></h3></div>
											</div>											
																					
											<?php 
												if (!empty($row['EdgingDateCompleted'])){
											?>
												<div class="row">
													<div class='col-sm-4'>
														<div class='alert alert-success' role='alert'>
															<div class="row">
																<div class="col-xs-3"><i class="fa fa-check-square-o fa-5x text-success"></i></div>
																<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Completed</strong><br>" . date('d-m-Y', strtotime($row['EdgingDateCompleted'])) . "<br>by " . get_name_from_id($row['EdgingCompletedBy'], $mysqli) ?></div>
															</div>
														</div>
													</div>
												</div>
											<?php } ?>
																						
											<div class="form-group">
												<label for="inputEdgingNotes">Notes</label>
												<textarea class="form-control" id="inputEdgingNotes<?php echo $row['JobMaterialID']; ?>" name="inputEdgingNotes<?php echo $row['JobMaterialID']; ?>" rows="2" <?php if ($_SESSION['is_edging'] <> 1 || !empty($row['EdgingDateCompleted'])) { echo "disabled='disabled'";} ?>><?php if (isset($row['EdgingNotes'])) { echo htmlspecialchars($row['EdgingNotes'], ENT_QUOTES); } ?></textarea>
											</div>
											<button class="save-edging-btn btn btn-primary btn-lg" type="button" value='<?php echo $row['JobMaterialID'] ?>' <?php if ($_SESSION['is_edging'] <> 1 || !empty($row['EdgingDateCompleted'])) { echo "disabled='disabled'";} ?>>Save</button>
										
											<?php
												if ($_SESSION['is_edging'] <> 0 && empty($row['EdgingDateCompleted'])){
											?>
													<button class="complete-edging-btn btn btn-success btn-lg" type="button" value='<?php echo $row['JobMaterialID'] ?>'>Complete</button>
											<?php } ?>

										</div>
									</div>
								<?php
									}
								?>
							</div>

							<div role="tabpanel" class="tab-pane active" id="assembler">

								<?php	

										$query = "SELECT tblJobTask.TaskID, tblTask.Weight FROM tblJobTask INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID WHERE tblJobTask.JobID = $formjobid";
										$result = $mysqli->query($query);
										unset($taskarray);
										$taskarray[] = "";
										$weight = 2;

										while($task = $result->fetch_array()) {
											$taskarray[] = $task['TaskID'];
											$weight -= $task['Weight'];
										}
								
										if ($weight > 0){
								?>
											<br>
											<div class="btn-group">
												<button class="btn btn-primary btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if (empty($_SESSION['is_assembler'])){echo "disabled='disabled'";}?>>
													Start Job <span class="caret"></span>
												</button>
												<ul id="start-list" class="dropdown-menu">
												<?php
													$query = "SELECT TaskID, TaskName, Weight FROM tblTask WHERE RoomStatus = 1 ORDER BY TaskID";
													$result = $mysqli->query($query);

													while($row = $result->fetch_array()){
														if (!in_array($row['TaskID'], $taskarray) && $row['Weight'] <= $weight)
															echo "<li id='" . $row['TaskID'] . "'><a href='javascript:void(0)'>" . $row['TaskName'] . "</a></li>";

													}	
												?>
												</ul>
											</div>

										<?php } ?>
								<a href="index.php" class="btn btn-warning btn-lg">Home</a>

								<br><br>

								<?php 
									$query = "SELECT tblJobTask.*, tblTask.TaskName FROM tblJobTask INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID WHERE tblJobTask.JobID = $jobid ORDER BY tblJobTask.TaskID";
									$result = $mysqli->query($query);

									while($row = $result->fetch_array()){
								?>
									<div class="panel panel-default">
										<div class="panel-body">
											<h2><?php echo $row['TaskName'] ?></h2>
											<div class="row">
												<div class="col-sm-4">
													<div class="alert alert-warning" role="alert">
														<div class="row">
															<div class="col-xs-3"><i class="fa fa-clock-o fa-5x text-warning"></i></div>
															<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Started</strong><br>" . date('d-m-Y', strtotime($row['DateStarted'])) . "<br>by " . get_name_from_id($row['StartedBy'], $mysqli) ?></div>
														</div>
													</div>
												</div>
												<?php 
													if (!empty($row['DateChecked'])){
												?>
														<div class='col-sm-4'>
															<div class='alert alert-info' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-list-ol fa-5x text-info"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Checked</strong><br>" . date('d-m-Y', strtotime($row['DateChecked'])) . "<br>by " . get_name_from_id($row['CheckedBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>

												<?php 
													if (!empty($row['DateCompleted'])){
												?>
														<div class='col-sm-4'>
															<div class='alert alert-success' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-check-square-o fa-5x text-success"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Completed</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>
											</div>

											<?php 
												if ($row['ReadyForCheck']==0){
													$disabledstring = (empty($_SESSION['is_assembler'])) ? "disabled='disabled'" : "";
													echo "<button type='button' class='check-btn btn btn-primary btn-lg' value='" . $row['TaskID'] . "' $disabledstring>Job Check</button><br><br>";
												}
												else{	
													$checklistincomplete = jobtask_checklist_complete($row['JobID'], $row['TaskID'], $mysqli);
													$missingitems = jobtask_missing_items($row['JobID'], $row['TaskID'], $mysqli);

													if ($checklistincomplete == true)
														echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains unchecked items!</strong></span><br><br>';
													if ($missingitems == true)
														echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains missing items!</strong></span><br><br>';
											?>
																							
													<button class="btn btn-primary btn-lg" type="button" data-toggle="collapse" data-target="#checklist<?php echo $row['TaskID']?>" aria-expanded="false" aria-controls="checklist<?php echo $row['TaskID']?>">Checklist</button>
											<?php
													if ($_SESSION['is_foreman'] <> 0 && $checklistincomplete == false && $missingitems == false && empty($row['DateCompleted'])){
											?>
														<button class="complete-btn btn btn-success btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Mark Complete</button>
											<?php } ?>

													<br><br>
													<div class="collapse" id="checklist<?php echo $row['TaskID']?>">
														<div class="well">
															<form id='checklist-form' class="checklist-form" action='#' method='post'>
																<fieldset <?php if ($_SESSION['is_foreman'] <> 1 || !empty($row['DateCompleted'])) { echo "disabled='disabled'";} ?>>
																	<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($row['JobID'])) { echo $row['JobID']; } ?>">
																	<input type="hidden" id="taskid" name="taskid" value="<?php if (isset($row['TaskID'])) { echo $row['TaskID']; } ?>">
																	<input type="hidden" id="action" name="action" value="savechecklist">
																	
																	<!-- <p>Tick boxes if "yes" or "not applicable"</p>
																	<div class="form-group">
																		<label>
																			<input name="inputCopyPlans" type="checkbox" value="1" required <?php if (isset($row['CopyPlans'])) { if ($row['CopyPlans']==1){ echo " CHECKED"; } } ?>> Copy of builder's plans and our plans - second drawer
																		</label>
																	</div> 
																	<div class="form-group">
																		<label>
																			<input name="inputQuickClips" type="checkbox" value="1" required <?php if (isset($row['QuickClips'])) { if ($row['QuickClips']==1){ echo " CHECKED"; } } ?>> Quick Clips
																		</label>
																	</div> 
																	<div class="form-group">
																		<label>
																			<input name="inputWhiteCaps" type="checkbox" value="1" required <?php if (isset($row['WhiteCaps'])) { if ($row['WhiteCaps']==1){ echo " CHECKED"; } } ?>> White caps to all panels and scribes
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputSinkCutouts" type="checkbox" value="1" required <?php if (isset($row['SinkCutouts'])) { if ($row['SinkCutouts']==1){ echo " CHECKED"; } } ?>> Sink cut outs and basin (ends) and edge
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputHotplateCutouts" type="checkbox" value="1" required <?php if (isset($row['HotplateCutouts'])) { if ($row['HotplateCutouts']==1){ echo " CHECKED"; } } ?>> Hot plate cut outs (ends)
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputBasesPelmets" type="checkbox" value="1" required <?php if (isset($row['BasesPelmets'])) { if ($row['BasesPelmets']==1){ echo " CHECKED"; } } ?>> Bases &amp; pelmets and fitted
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputAppliancesFitted" type="checkbox" value="1" required <?php if (isset($row['AppliancesFitted'])) { if ($row['AppliancesFitted']==1){ echo " CHECKED"; } } ?>> All appliances fitted
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputCutleryTrays" type="checkbox" value="1" required <?php if (isset($row['CutleryTrays'])) { if ($row['CutleryTrays']==1){ echo " CHECKED"; } } ?>> Cutlery trays
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputWorkersName" type="checkbox" value="1" required <?php if (isset($row['WorkersName'])) { if ($row['WorkersName']==1){ echo " CHECKED"; } } ?>> Worker's name in oven box
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputCabinetLabelled" type="checkbox" value="1" required <?php if (isset($row['CabinetLabelled'])) { if ($row['CabinetLabelled']==1){ echo " CHECKED"; } } ?>> All cabinet work labelled
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputStickersRemoved" type="checkbox" value="1" required <?php if (isset($row['StickersRemoved'])) { if ($row['StickersRemoved']==1){ echo " CHECKED"; } } ?>> All internal stickers removed
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputAllAccessories" type="checkbox" value="1" required <?php if (isset($row['AllAccessories'])) { if ($row['AllAccessories']==1){ echo " CHECKED"; } } ?>> All accessories eg bins &amp; lazy susan
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputHandlesMounted" type="checkbox" value="1" required <?php if (isset($row['HandlesMounted'])) { if ($row['HandlesMounted']==1){ echo " CHECKED"; } } ?>> All handles mounted
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputBumpOns" type="checkbox" value="1" required <?php if (isset($row['BumpOns'])) { if ($row['BumpOns']==1){ echo " CHECKED"; } } ?>> All bump-ons
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputMeasureOverallSizes" type="checkbox" value="1" required <?php if (isset($row['MeasureOverallSizes'])) { if ($row['MeasureOverallSizes']==1){ echo " CHECKED"; } } ?>> Measure overall sizes
																		</label>
																	</div>	
																	<div class="form-group">
																		<label>
																			<input name="inputOverheadCleats" type="checkbox" value="1" required <?php if (isset($row['OverheadCleats'])) { if ($row['OverheadCleats']==1){ echo " CHECKED"; } } ?>> Overhead cleats (fridge cupboard)
																		</label>
																	</div>	
																	<div class="form-group">
																		<label>
																			<input name="inputKitchenInspected" type="checkbox" value="1" required <?php if (isset($row['KitchenInspected'])) { if ($row['KitchenInspected']==1){ echo " CHECKED"; } } ?>> Kitchen has been inspected
																		</label>
																	</div>	
																	<div class="form-group">
																		<label>
																			<input name="inputSoftClosers" type="checkbox" value="1" required <?php if (isset($row['SoftClosers'])) { if ($row['SoftClosers']==1){ echo " CHECKED"; } } ?>> Soft closers to hinges
																		</label>
																	</div>	
																	<div class="form-group">
																		<label>
																			<input name="inputBlocksKickboards" type="checkbox" value="1" required <?php if (isset($row['BlocksKickboards'])) { if ($row['BlocksKickboards']==1){ echo " CHECKED"; } } ?>> Blocks to kickboards or islands
																		</label>
																	</div>	
																	<div class="form-group">
																		<label>
																			<input name="inputCheckRails" type="checkbox" value="1" required <?php if (isset($row['CheckRails'])) { if ($row['CheckRails']==1){ echo " CHECKED"; } } ?>> Check 2x1 rails
																		</label>
																	</div>	
																	<div class="form-group">
																		<label>
																			<input name="inputKickboardsNumbered" type="checkbox" value="1" required <?php if (isset($row['KickboardsNumbered'])) { if ($row['KickboardsNumbered']==1){ echo " CHECKED"; } } ?>> Kickboards to be numbered
																		</label>
																	</div>	
																	<div class="form-group">
																		<label>
																			<input name="inputDishwasherAngle" type="checkbox" value="1" required <?php if (isset($row['DishwasherAngle'])) { if ($row['DishwasherAngle']==1){ echo " CHECKED"; } } ?>> Island dishwasher to have 20x20 angle on bottom between base and panels
																		</label>
																	</div>	
																	<div class="form-group">
																		<label>
																			<input name="inputTemplates" type="checkbox" value="1" required <?php if (isset($row['Templates'])) { if ($row['Templates']==1){ echo " CHECKED"; } } ?>> Templates (if required)
																		</label>
																	</div> -->																																										
																	<div class="form-group">
																		<label for="inputMissingItems">ToDo Items</label>
																		<textarea class="form-control" id="inputApplicableItems" name="inputApplicableItems" rows="6"><?php if (isset($row['Applicabletems'])) { echo htmlspecialchars($row['Applicabletems'], ENT_QUOTES); } ?></textarea>
																	</div>
																	<div class="form-group">
																		<label for="inputMissingItems">Missing Items</label>
																		<textarea class="form-control" id="inputMissingItems" name="inputMissingItems" rows="6"><?php if (isset($row['MissingItems'])) { echo htmlspecialchars($row['MissingItems'], ENT_QUOTES); } ?></textarea>
																	</div>
																	<!-- <div class="form-group">
																		<label>
																			<input name="inputMissingItemsComplete" type="checkbox" value="1" <?php if (isset($row['MissingItemsComplete'])) { if ($row['MissingItemsComplete']==1){ echo " CHECKED"; } } ?>> All missing items completed
																		</label>
																	</div> -->
																	
																	<button type="submit" id="save-btn" class="btn btn-primary">Save</button>
																</fieldset>
															</form>
														</div>
													</div>
												<?php } ?>
										</div>
									</div>
								<?php
									}
								?>							
							
							</div>

							<div role="tabpanel" class="tab-pane" id="installer">

								<?php	

										$query = "SELECT tblJobTaskInstall.TaskID, tblTask.Weight FROM tblJobTaskInstall INNER JOIN tblTask ON tblJobTaskInstall.TaskID = tblTask.TaskID WHERE tblJobTaskInstall.JobID = $formjobid";
										$result = $mysqli->query($query);
										unset($taskarray);
										$taskarray[] = "";
										$weight = 2;

										while($task = $result->fetch_array()) {
											$taskarray[] = $task['TaskID'];
											$weight -= $task['Weight'];
										}
								
										if ($weight > 0){
								?>
											<br>
											<div class="btn-group">
												<button class="btn btn-success btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if (empty($_SESSION['is_installer'])){echo "disabled='disabled'";}?>>
													Job Check <span class="caret"></span>
												</button>
												<ul id="install-list" class="dropdown-menu">
												<?php
													$query = "SELECT TaskID, TaskName, Weight FROM tblTask WHERE RoomStatus = 1 ORDER BY TaskID";
													$result = $mysqli->query($query);

													while($row = $result->fetch_array()){
														if (!in_array($row['TaskID'], $taskarray) && $row['Weight'] <= $weight)
															echo "<li id='" . $row['TaskID'] . "'><a href='javascript:void(0)'>" . $row['TaskName'] . "</a></li>";

													}	
												?>
												</ul>
											</div>

										<?php } ?>

								<btn class="btn btn-lg btn-info plans-btn">Plans</btn>
								<btn class="btn btn-lg btn-primary photos-btn">Photos</btn>		
								<a href="index.php" class="btn btn-warning btn-lg">Home</a>

								<br><br>

								<?php 
									$query = "SELECT tblJobTaskInstall.*, tblTask.TaskName FROM tblJobTaskInstall INNER JOIN tblTask ON tblJobTaskInstall.TaskID = tblTask.TaskID WHERE tblJobTaskInstall.JobID = $jobid ORDER BY tblJobTaskInstall.TaskID";
									$result = $mysqli->query($query);

									while($row = $result->fetch_array()){
								?>
									<div class="panel panel-default">
										<div class="panel-body">
											<h2><?php echo $row['TaskName'] ?></h2>
											<div class="row">
	
												<?php 
													if (!empty($row['DateChecked'])){
												?>
														<div class='col-sm-4'>
															<div class='alert alert-info' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-list-ol fa-5x text-info"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Checked</strong><br>" . date('d-m-Y', strtotime($row['DateChecked'])) . "<br>by " . get_name_from_id($row['CheckedBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>

												<?php 
													if (!empty($row['DateCompleted'])){
														if ($row['SentToMaint']<>0)
															$completestring = "Sent to Maintenance";
														else
														$completestring = "Complete";
												?>
														<div class='col-sm-4'>
															<div class='alert alert-success' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-check-square-o fa-5x text-success"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>".$completestring."</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>
											</div>

											<?php 
												if ($row['ReadyForCheck']==0){
													$disabledstring = (empty($_SESSION['is_installer'])) ? "disabled='disabled'" : "";
													echo "<button type='button' class='check-btn btn btn-primary btn-lg' value='" . $row['TaskID'] . "' $disabledstring>Job Check</button><br><br>";
												}	
												else{	
													$checklistincomplete = jobtaskinstall_checklist_complete($row['JobID'], $row['TaskID'], $mysqli);
													$missingitems = jobtaskinstall_missing_items($row['JobID'], $row['TaskID'], $mysqli);

													if ($checklistincomplete == true)
														echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains unchecked items!</strong></span><br><br>';
													if ($missingitems == true)
														echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains missing items!</strong></span><br><br>';
											?>
																							
													<button class="btn btn-primary btn-lg" type="button" data-toggle="collapse" data-target="#checklistinstall<?php echo $row['TaskID']?>" aria-expanded="false" aria-controls="checklistinstall<?php echo $row['TaskID']?>">Checklist</button>
											<?php
													if ($_SESSION['is_installer'] <> 0 && $checklistincomplete == false && $missingitems == false && empty($row['DateCompleted'])){
											?>
														<button class="complete-install-btn btn btn-success btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Mark Complete</button>
											<?php } ?>

													<br><br>
													<div class="collapse" id="checklistinstall<?php echo $row['TaskID']?>">
														<div class="well">
															<form id='checklist-install-form' class="checklist-install-form" action='#' method='post'>
																<fieldset <?php if ($_SESSION['is_installer'] <> 1 || !empty($row['DateCompleted'])) { echo "disabled='disabled'";} ?>>
																	<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($row['JobID'])) { echo $row['JobID']; } ?>">
																	<input type="hidden" id="taskid" name="taskid" value="<?php if (isset($row['TaskID'])) { echo $row['TaskID']; } ?>">
																	<input type="hidden" id="action" name="action" value="savechecklist">
																	
																	<!-- <p>Tick boxes if "yes" or "not applicable"</p>
																	<div class="form-group">
																		<label>
																			<input name="inputGapped" type="checkbox" value="1" required <?php if (isset($row['Gapped'])) { if ($row['Gapped']==1){ echo " CHECKED"; } } ?>> Gapped
																		</label>
																	</div> 
																	<div class="form-group">
																		<label>
																			<input name="inputCapped" type="checkbox" value="1" required <?php if (isset($row['Capped'])) { if ($row['Capped']==1){ echo " CHECKED"; } } ?>> Capped
																		</label>
																	</div> 
																	<div class="form-group">
																		<label>
																			<input name="inputAdjustedDoorsDrawers" type="checkbox" value="1" required <?php if (isset($row['AdjustedDoorsDrawers'])) { if ($row['AdjustedDoorsDrawers']==1){ echo " CHECKED"; } } ?>> Adjusted doors and drawers
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputBenchtopsTemplatesInstalled" type="checkbox" value="1" required <?php if (isset($row['BenchtopsTemplatesInstalled'])) { if ($row['BenchtopsTemplatesInstalled']==1){ echo " CHECKED"; } } ?>> Benchtops/templates installed
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputBasinsInstalled" type="checkbox" value="1" required <?php if (isset($row['BasinsInstalled'])) { if ($row['BasinsInstalled']==1){ echo " CHECKED"; } } ?>> Basins, sinks, troughs etc installed/marked
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputAllLevelsChecked" type="checkbox" value="1" required <?php if (isset($row['AllLevelsChecked'])) { if ($row['AllLevelsChecked']==1){ echo " CHECKED"; } } ?>> All levels checked
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputOvenCleats" type="checkbox" value="1" required <?php if (isset($row['OvenCleats'])) { if ($row['OvenCleats']==1){ echo " CHECKED"; } } ?>> 2x1 oven cleats
																		</label>
																	</div>
																	<div class="form-group">
																		<label>
																			<input name="inputCabinetsCleaned" type="checkbox" value="1" required <?php if (isset($row['CabinetsCleaned'])) { if ($row['CabinetsCleaned']==1){ echo " CHECKED"; } } ?>> Cabinets all cleaned
																		</label>
																	</div> -->
																	<div class="form-group">
																		<label for="inputMissingItems">ToDo Items</label>
																		<textarea class="form-control" id="inputApplicableItems" name="inputApplicableItems" rows="6"><?php if (isset($row['Applicabletems'])) { echo htmlspecialchars($row['Applicabletems'], ENT_QUOTES); } ?></textarea>
																	</div>																																																									
																	<div class="form-group">
																		<label for="inputMissingItems">Missing Items</label>
																		<textarea class="form-control" id="inputMissingItems" name="inputMissingItems" rows="6"><?php if (isset($row['MissingItems'])) { echo htmlspecialchars($row['MissingItems'], ENT_QUOTES); } ?></textarea>
																	</div>
																	<!-- <div class="form-group">
																		<label>
																			<input name="inputMissingItemsComplete" type="checkbox" value="1" <?php if (isset($row['MissingItemsComplete'])) { if ($row['MissingItemsComplete']==1){ echo " CHECKED"; } } ?>> All missing items completed
																		</label>
																	</div> -->
																	
																	<button type="submit" id="save-btn" class="btn btn-primary">Save</button>

																	<?php
																		if ($_SESSION['is_foreman'] <> 0 && $checklistincomplete == false && $missingitems == true && empty($row['DateCompleted'])){
																		?>
																			<button class="send-maint-btn btn btn-success" type="button" value='<?php echo $row['TaskID'] ?>'>Send To Maintenance</button>
																	<?php } ?>
																</fieldset>
															</form>
														</div>
													</div>
												<?php } ?>
										</div>
									</div>
								<?php
									}
								?>							
							
							</div>

							<div role="tabpanel" class="tab-pane" id="maintenance">
								<a href="index.php" class="btn btn-warning btn-lg">Home</a>
								<br><br>	

								<?php 
									$query = "SELECT tblJobTaskMaint.*, tblTask.TaskName FROM tblJobTaskMaint INNER JOIN tblTask ON tblJobTaskMaint.TaskID = tblTask.TaskID WHERE tblJobTaskMaint.JobID = $jobid ORDER BY tblJobTaskMaint.TaskID";
									$result = $mysqli->query($query);

									while($row = $result->fetch_array()){
								?>
									<div class="panel panel-default">
										<div class="panel-body">
											<h2><?php echo $row['TaskName'] ?></h2>
											<div class="row">
												<div class="col-sm-4">
													<div class="alert alert-warning" role="alert">
														<div class="row">
															<div class="col-xs-3"><i class="fa fa-clock-o fa-5x text-warning"></i></div>
															<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Sent To Maintenance</strong><br>" . date('d-m-Y', strtotime($row['DateStarted'])) . "<br>by " . get_name_from_id($row['StartedBy'], $mysqli) ?></div>
														</div>
													</div>
												</div>

												<?php 
													if (!empty($row['DateCompleted'])){
												?>
														<div class='col-sm-4'>
															<div class='alert alert-success' role='alert'>
																<div class="row">
																	<div class="col-xs-3"><i class="fa fa-check-square-o fa-5x text-success"></i></div>
																	<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Completed</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
																</div>
															</div>
														</div>
												<?php } ?>
											</div>

											<?php 	
												$missingitems = jobtaskmaint_missing_items($row['JobID'], $row['TaskID'], $mysqli);

												if ($missingitems == true)
													echo '<span class="fa fa-warning fa-4x text-danger" style="vertical-align: middle;"></span><span><strong>Contains missing items!</strong></span><br><br>';
											?>
																							
													<button class="btn btn-primary btn-lg" type="button" data-toggle="collapse" data-target="#checklistmaint<?php echo $row['TaskID']?>" aria-expanded="false" aria-controls="checklist<?php echo $row['TaskID']?>">Notes &amp; Checklist</button>
											

											<?php
													if ($_SESSION['is_maint'] <> 0 && $missingitems == false  && empty($row['DateCompleted'])){
											?>
														<button class="complete-maint-btn btn btn-success btn-lg" type="button" value='<?php echo $row['TaskID'] ?>'>Complete</button>
											<?php } ?>

											<br><br>
											<div class="collapse" id="checklistmaint<?php echo $row['TaskID']?>">
												<div class="well">
													<form id='checklist-maint-form' class="checklist-maint-form" action='#' method='post'>
														<fieldset <?php if ($_SESSION['is_maint'] <> 1 || !empty($row['DateCompleted'])) { echo "disabled='disabled'";} ?>>
															<input type="hidden" id="jobid" name="jobid" value="<?php if (isset($row['JobID'])) { echo $row['JobID']; } ?>">
															<input type="hidden" id="taskid" name="taskid" value="<?php if (isset($row['TaskID'])) { echo $row['TaskID']; } ?>">
															<input type="hidden" id="action" name="action" value="savechecklist">
															
															<div class="form-group">
																<label for="inputNotes">Notes</label>
																<textarea class="form-control" id="inputNotes" name="inputNotes" rows="6"><?php if (isset($row['Notes'])) { echo htmlspecialchars($row['Notes'], ENT_QUOTES); } ?></textarea>
															</div>																																									
															<div class="form-group">
																<label for="inputMissingItems">Missing Items</label>
																<textarea class="form-control" id="inputMissingItems" name="inputMissingItems" rows="6"><?php if (isset($row['MissingItems'])) { echo htmlspecialchars($row['MissingItems'], ENT_QUOTES); } ?></textarea>
															</div>
															<div class="form-group">
																<label>
																	<input name="inputMissingItemsComplete" type="checkbox" value="1" <?php if (isset($row['MissingItemsComplete'])) { if ($row['MissingItemsComplete']==1){ echo " CHECKED"; } } ?>> All missing items completed
																</label>
															</div>
															
															<button type="submit" id="save-btn" class="btn btn-primary">Save</button>
														</fieldset>
													</form>
												</div>
											</div>

										</div>
									</div>
								<?php
									}
								?>
							</div>
						</div>
						<br><br>
					</div>

					
				</div>
			</div>
			
    </div> <!-- /container -->

	<div class="modal fade" tabindex="-1" role="dialog" id="uploads-modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Modal title</h4>
		</div>
		<div class="modal-body">
			<div id="modal-alert"></div>
			<div id='modal-content'></div>
			<div id='modal-loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- Button to Open the Modal -->

<!-- The Modal -->
<div class="modal" id="newRoom">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add New Room Type</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form id='task-form' action='#' method='post'>
        	<input type="hidden" id="action" name="action" value="taskInsert">
        	<input type="hidden" id="Roomjobid" name="Roomjobid" value="<?php if (isset($_GET['jobid'])) { echo $_GET['jobid']; } ?>">
        		<div class="form-group">
						  <label for="usr">Room Type Name</label>
						  <input type="text" class="form-control" name="task_name">
						</div>
						<button type="submit" id="save-btn" class="btn btn-primary">Save</button>
        </form>
        <hr>
        <div style="margin-top: 10px;  padding: 5px;">
        	<?php
				$userid = $_SESSION['user_id'];
				$taskData_query = "SELECT * FROM tblTask WHERE user_id = $userid";
				
				$task_result = mysqli_query($mysqli, $taskData_query);
				if($task_result != null)
				{
					$task_result_list = mysqli_num_rows($task_result) ;
				}
				else
				{
					$task_result_list = -1;
				}
				
				if($task_result_list > 0){
					echo '<table class="table">
								<thead>
									<tr>
										<th>Room Type Name</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>';
							
						

				 while($rows = mysqli_fetch_assoc($task_result)) 
			    {

			    	echo '<tr>';
						echo '<td>'.$rows["TaskName"].'</td>';
						echo '<td><button type="button" data-id="'.$rows["TaskID"].'"" class="btn btn-sm btn-danger RemoveRoom"><i class="fa fa-times-circle" aria-hidden="true"></i></button></td>';
					echo '</tr>';
			    	
			    }

			   echo '</tbody>
			   </table>';
			 }
		    ?>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

    <script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/additional-methods.min.js"></script>
    <script src="js/jquery.confirm.min.js"></script>
	<script src="js/bootstrap-filestyle.min.js"></script>

	<script type='text/javascript'>

		$('form').on('submit', function(e){
			//e.preventDefault();
		});
		
		$(document).ready(function(){

			if (location.hash) {
				$("a[href='" + location.hash + "']").tab("show");
			}
			$(document.body).on("click", "a[data-toggle]", function(event) {
				location.hash = this.getAttribute("href");
			});

			$('.plans-btn').click(function(){
				$('#modal-alert').hide();
				$("#uploads-modal").find('.modal-title').text('Plans');

				$('#uploads-modal').modal('show');
				showPlans();
			});

			function showPlans(){
				var jobid = $('#jobid').val();
				$('#modal-content').hide();
				$('#modal-loader-image').show(); 

				$('#modal-content').load('job-plans.php', { jobid: jobid }, function(){ 
					
					$(":file").filestyle({buttonName: "btn-primary", buttonText: " Browse"});
					$('#modal-loader-image').hide(); 
					$('#modal-content').fadeIn('slow');
					
				});
			}

			$('.photos-btn').click(function(){
				$('#modal-alert').hide();
				$("#uploads-modal").find('.modal-title').text('Photos');

				$('#uploads-modal').modal('show');
				showPhotos();
			});

			function showPhotos(){
				var jobid = $('#jobid').val();
				$('#modal-content').hide();
				$('#modal-loader-image').show(); 

				$('#modal-content').load('job-photos.php', { jobid: jobid }, function(){ 
					
					$(":file").filestyle({buttonName: "btn-primary", buttonText: " Browse"});
					$('#modal-loader-image').hide(); 
					$('#modal-content').fadeIn('slow');
					
				});
			}

			$.validator.setDefaults({
				highlight: function(element) {
					$(element).parent().addClass('has-error');
				},
				unhighlight: function(element) {
					$(element).parent().removeClass('has-error');
				},
				errorElement: 'div',
				errorClass: 'help-block',
				errorPlacement: function(error, element) {
					if(element.parent('.input-group').length) {
						element.parent().parent().append(error);
					} else {
						element.parent().append(error);
					}
				}
			});
				
			$('form').each(function() {
				$(this).validate();
			});
		

			$(document).on('click', '#start-list li', function(){ 
				var jobid = $('#jobid').val();
				var taskid = $(this).attr('id');

				$.post("job-crud.php", { action: 'start', jobid: jobid, taskid: taskid }) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					//$('#alert').html(response.msg);
					//$('#alert').show();
					location.reload();
				});
			});

			$(document).on('click', '.check-btn', function(){ 
				var jobid = $('#jobid').val();
				var taskid = $(this).val();

				$(this).attr("disabled", true);

				$.post("job-crud.php", { action: 'check', jobid: jobid, taskid: taskid }) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					//$('#alert').html(response.msg);
					//$('#alert').show();
					location.reload();
				});
			});

			$(document).on('click', '.complete-btn', function(){ 
				var jobid = $('#jobid').val();
				var taskid = $(this).val();
				$.confirm({
					text: "Are you sure you want to mark this job as complete?",
					confirm: function() {
						$.post("job-crud.php", { action: 'complete', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var response = jQuery.parseJSON(data);
							//$('#alert').html(response.msg);
							//$('#alert').show();
							location.reload();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});

			$(document).on('submit', '.checklist-form', function() {
				
				$.post("job-crud.php", $(this).serialize())
					.done(function(data) {
						var response = jQuery.parseJSON(data);
						//$('#alert').html(response.msg);
						//$('#alert').show();
						location.reload();
					});
						
				return false;
			});
				

			$(document).on('click', '#install-list li', function(){ 
				var jobid = $('#jobid').val();
				var taskid = $(this).attr('id');

				$.post("job-install-crud.php", { action: 'check', jobid: jobid, taskid: taskid }) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					location.reload();
				});
			});

			$(document).on('submit', '.checklist-install-form', function() {
				$.post("job-install-crud.php", $(this).serialize())
					.done(function(data) {
						var response = jQuery.parseJSON(data);
						location.reload();
					});
						
				return false;
			});				
			

			$(document).on('click', '.complete-install-btn', function(){ 
				var jobid = $('#jobid').val();
				var taskid = $(this).val();
				$.confirm({
					text: "Are you sure you want to mark this job as complete?",
					confirm: function() {
						$.post("job-install-crud.php", { action: 'complete', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var response = jQuery.parseJSON(data);
							location.reload();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});

			$(document).on('click', '.send-maint-btn', function(){ 
				var jobid = $('#jobid').val();
				var missingitems = $('#checklist-install-form #inputMissingItems').val();
				var taskid = $(this).val();
				$.confirm({
					text: "Are you sure you want to send this job to maintenance?",
					confirm: function() {
						$.post("job-install-crud.php", { action: 'sendmaint', jobid: jobid, taskid: taskid, missingitems: missingitems }) 
						.done(function(data){
							var response = jQuery.parseJSON(data);
							location.reload();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});

			$(document).on('submit', '.checklist-maint-form', function() {
				$.post("job-maint-crud.php", $(this).serialize())
					.done(function(data) {
						var response = jQuery.parseJSON(data);
						location.reload();
					});
						
				return false;
			});				
			

			$(document).on('click', '.complete-maint-btn', function(){ 
				var jobid = $('#jobid').val();
				var taskid = $(this).val();
				$.confirm({
					text: "Are you sure you want to mark this job as complete?",
					confirm: function() {
						$.post("job-maint-crud.php", { action: 'complete', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var response = jQuery.parseJSON(data);
							location.reload();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});
			$(document).on('click', '.start-draft-btn', function(){ 
				var jobid = $('#jobid').val();
				//var taskid = $(this).attr('id');

				$(this).attr("disabled", true);

				$.post("job-draft-crud.php", { action: 'start', jobid: jobid }) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					location.reload();
				});
			});

			$(document).on('click', '.drawn-draft-btn', function(){ 
				var jobid = $('#jobid').val();
				var taskid = $(this).val();

				$(this).attr("disabled", true);

				$.post("job-draft-crud.php", { action: 'drawn', jobid: jobid, taskid: taskid }) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					//$('#alert').html(response.msg);
					//$('#alert').show();
					location.reload();
				});
			});

			$(document).on('submit', '.checklist-draft-form', function() {
				$.post("job-draft-crud.php", $(this).serialize())
					.done(function(data) {
						var response = jQuery.parseJSON(data);
						location.reload();
					});
						
				return false;
			});	
			

			$(document).on('submit', '.materials-form', function() {
				
				$.post("job-draft-crud.php", $(this).serialize())
					.done(function(data) {
						var response = jQuery.parseJSON(data);
						location.reload();
					});
						
				return false;
			});		
			
			$(document).on('click', '.complete-draft-btn', function(){ 
				var jobid = $('#jobid').val();
				var taskid = $(this).val();
				$.confirm({
					text: "Are you sure you want to mark this job as Sent To CNC?",
					confirm: function() {
						$.post("job-draft-crud.php", { action: 'complete', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var response = jQuery.parseJSON(data);
							location.reload();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});

			$(document).on('click', '.save-cnc-btn', function(){ 
				var jobmaterialid = $(this).val();
				var notes = $("#inputCNCNotes"+jobmaterialid).val();

				$.post("job-cnc-crud.php", { action: 'savenotes', jobmaterialid: jobmaterialid, notes: notes }) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					location.reload();
				});
			});

			$(document).on('click', '.complete-cnc-btn', function(){ 
				var jobmaterialid = $(this).val();
				
				$.confirm({
					text: "Are you sure you want to mark this item as complete?",
					confirm: function() {
						$.post("job-cnc-crud.php", { action: 'complete', jobmaterialid: jobmaterialid }) 
						.done(function(data){
							var response = jQuery.parseJSON(data);
							location.reload();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});

			$(document).on('click', '.save-edging-btn', function(){ 
				var jobmaterialid = $(this).val();
				var notes = $("#inputEdgingNotes"+jobmaterialid).val();

				$.post("job-edging-crud.php", { action: 'savenotes', jobmaterialid: jobmaterialid, notes: notes }) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					location.reload();
				});
			});

			$(document).on('click', '.complete-edging-btn', function(){ 
				var jobmaterialid = $(this).val();
				
				$.confirm({
					text: "Are you sure you want to mark this item as complete?",
					confirm: function() {
						$.post("job-edging-crud.php", { action: 'complete', jobmaterialid: jobmaterialid }) 
						.done(function(data){
							var response = jQuery.parseJSON(data);
							location.reload();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});

			// Add button click handler
			$(document).on('click', '.addItemButton', function() {
				$('#tableheadings').show();
				var $template = $('#itemTemplate'),
					$clone    = $template
									.clone()
									.removeClass('hide')
									.addClass('itemrowadd')
									.removeAttr('id')
									.insertBefore($template);
				
				$clone.find('[name="inputMaterialCancelAdd[]"]').val(0);

				var num = $('.itemrowadd').length-1;

				$clone.find('[name="inputBoardDescriptionAdd[]"]').attr('id', 'inputBoardDescriptionAdd['+num+']');
				$clone.find('[name="inputBoardTypeAdd[]"]').attr('id', 'inputBoardTypeAdd['+num+']');
				$clone.find('[name="inputBoardQuantityAdd[]"]').attr('id', 'inputBoardQuantityAdd['+num+']');

				$clone.find('[name="inputBoardDescriptionAdd[]"]').attr('name', 'inputBoardDescriptionAdd['+num+']');
				$clone.find('[name="inputBoardTypeAdd[]"]').attr('name', 'inputBoardTypeAdd['+num+']');
				$clone.find('[name="inputBoardQuantityAdd[]"]').attr('name', 'inputBoardQuantityAdd['+num+']');

			})

			// Remove button click handler
			$(document).on('click', '.removeItemButton', function() {
				
				var $row  = $(this).parents('.form-group');
				
				$row.find('[name="inputMaterialDelete[]"]').val(1);
				$row.find('[name="inputMaterialCancelAdd[]"]').val(1);

				$row.hide();
				
				//if ($('[name="inputBoardDescription[]"]:visible').length == 0 && $('[name="inputBoardDescriptionAdd[]"]:visible').length == 0)
				//	$('#tableheadings').hide();
			});

			$(document).on('click', '#add-plans-btn', function() {
				
				if ($('#inputFileLocation').val() != ""){
					$('#moadl-content').hide();
					$('#modal-loader-image').show();
					
					var formData = new FormData();
					formData.append("action", "add");
					formData.append("jobid", $('#jobid').val());
					files = $('#inputFileLocation').get(0).files;

					$.each(files, function(i, file) {
						formData.append("inputFileLocation_"  + i, file); 
					});
			
					$.ajax( {
						url: 'job-plans-crud.php',
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						success: function( data ){  
							var response = jQuery.parseJSON(data);
							$('#modal-alert').html(response.msg);
							$('#modal-alert').show();
							showPlans();
						}  
					} );
				}
		
				return false;
			});

			$(document).on('click', '.delete-plan-btn', function(){ 
				var deleteid = $(this).val();
				$.confirm({
					text: "Are you sure you want to delete this plan?",
					confirm: function() {
						$.post("job-plans-crud.php", { action: 'delete', deleteid: deleteid }) 
							.done(function(data){
								showPlans();
								$('#modal-alert').html(data);
								$('#modal-alert').show();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});

			$(document).on('click', '#add-photos-btn', function() {
				
				if ($('#inputFileLocation').val() != ""){
					
					$('#modal-content').hide();
					$('#modal-loader-image').show();
					
					var formData = new FormData();

					formData.append("action", "add");					
					formData.append("jobid", $('#jobid').val());				
					files = $('#inputFileLocation').get(0).files;
					
					$.each(files, function(i, file) {
						formData.append("inputFileLocation_"  + i, file); 
					});
			
					$.ajax( {
						url: 'job-photos-crud.php',
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						success: function( data ){  
							var response = jQuery.parseJSON(data);
							$('#modal-alert').html(response.msg);
							$('#modal-alert').show();
							showPhotos();
						}  
					} );
				}
		
				return false;
			});

			$(document).on('click', '.delete-photo-btn', function(){ 
				var deleteid = $(this).val();
				$.confirm({
					text: "Are you sure you want to delete this photo?",
					confirm: function() {
						$.post("job-photos-crud.php", { action: 'delete', deleteid: deleteid }) 
							.done(function(data){
								showPhotos();
								$('#modal-alert').html(data);
								$('#modal-alert').show();
						});
					},
					cancel: function() {
						// nothing to do
					}
				});
			});
		});

	$(document).on('click', '.create-room', function(){ 
			var jobid = $('#jobid').val();
			var roomId = $("#roomList").val();

			if(roomId != "")
			{
				$.post("job-draft-crud.php", { action: 'createRoom', jobid: jobid, roomId: roomId }) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					location.reload();
				});
			}
			else
			{
				$('.err_roomList').append('<span class="small alerts text-danger">Please Select Room Type</span>');
			}
		});

	$(document).on('click', '.sing-off-btn', function(){ 
			var jobid = $('#jobid').val();
			var taskid = $(this).val();

			$.confirm({
				text: "Are you sure you want to sign off?",
				confirm: function() {
					$.post("job-draft-crud.php", { action: 'singOff', jobid: jobid, taskid: taskid }) 
					.done(function(data){
						var response = jQuery.parseJSON(data);
						location.reload();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		});

	$(document).on('click', '.sing-off-btn-alert', function(){ 
			var jobid = $('#jobid').val();
			var taskid = $(this).val();

			$.confirm({
				text: "Are you sure you want to sign off alert?",
				confirm: function() {
					$.post("job-draft-crud.php", { action: 'singOffAlert', jobid: jobid, taskid: taskid }) 
					.done(function(data){
						var response = jQuery.parseJSON(data);
						//location.reload();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		});

	$(document).on('click', '.delete-room-btn', function(){ 
			var jobid = $('#jobid').val();
			var taskid = $(this).val();
			var user_id = $(this).data('id');

			$.confirm({
				text: "Are you sure you want to delete room?",
				confirm: function() {
					$.post("job-draft-crud.php", { action: 'deleteRoom', jobid: jobid, taskid: taskid, user_id: user_id}) 
					.done(function(data){
						var response = jQuery.parseJSON(data);
						location.reload();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		});

	$(document).on('click', '.RemoveRoom', function(){ 
		
		var room_id = $(this).data('id');

		$.confirm({
			text: "Are you sure you want to delete room?",
			confirm: function() {
				$.post("job-draft-crud.php", { action: 'RemoveRoom', room_id: room_id}) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					location.reload();
				});
			},
			cancel: function() {
				// nothing to do
			}
		});
	});
	
	$(document).on('click', '.sing-off-all-btn', function(){ 
			var jobid = $('#jobid').val();

			$.confirm({
				text: "Are you sure you want to sign off?",
				confirm: function() {
					$.post("job-draft-crud.php", { action: 'singOffAll', jobid: jobid}) 
					.done(function(data){
						var response = jQuery.parseJSON(data);
						location.reload();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		});

	$(document).on('click', '.sing-off-alert-btn', function(){ 
			var jobid = $('#jobid').val();

			$.confirm({
				text: "Are you sure you want to sign off?",
				confirm: function() {
					$.post("job-draft-crud.php", { action: 'singOffAlertAll', jobid: jobid}) 
					.done(function(data){
						var response = jQuery.parseJSON(data);
						location.reload();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		});

	$(document).on('submit', '#task-form', function() {

        $.post("job-draft-crud.php", $(this).serialize())
            .done(function(data) {
                location.reload();
            });
                 
        return false;
    });

	$.validator.setDefaults({
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function(error, element) {
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

// Code by Kiran on 24th February 2022


	$(document).on('click', '.create_sub_job', function(){ 
		var jobid = $('#jobid').val();
		$.confirm({
			text: "Are you sure you want to create new sub job?",
			confirm: function() {
				$.post("job-draft-crud.php", { action: 'CreateSubJob', jobid: jobid}) 
				.done(function(data){
					var response = jQuery.parseJSON(data);
					location.reload();
				});
			},
			cancel: function() {
				// nothing to do
			}
		});
	});
// Code by Kiran on 24th February 2022

	</script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>