<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

$jobid = isset($_POST['jobid']) ? $_POST['jobid'] : die('ERROR: Job ID not found.');

if ($stmt = $mysqli->prepare("SELECT JobID, JobAddress FROM tblJob WHERE JobID = ? LIMIT 1")) { 
	$stmt->bind_param('i', $jobid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($formjobid, $formjobaddress);
	$stmt->fetch();
}
	
?>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">

			<div class="panel-body">
				<h1><?php echo "Job #" . $formjobid ?></h1>
				<h3><?php echo  $formjobaddress ?></h3>
			</div>
		</div>

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist" id="myTab">
			<li role="presentation" class="active"><a href="#draftsman" aria-controls="draftsman" role="tab" data-toggle="tab">Draftsman</a></li>
			<li role="presentation"><a href="#materials" aria-controls="materials" role="tab" data-toggle="tab">Materials</a></li>
			<li role="presentation"><a href="#assembler" aria-controls="assembler" role="tab" data-toggle="tab">Assembler</a></li>
			<li role="presentation"><a href="#installer" aria-controls="installer" role="tab" data-toggle="tab">Installer</a></li>
			<li role="presentation"><a href="#maintenance" aria-controls="maintenance" role="tab" data-toggle="tab">Maintenance</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">

			<div role="tabpanel" class="tab-pane active" id="draftsman">
				<?php 
					$query = "SELECT tblJobTaskDraft.*, tblTask.TaskName FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = $jobid ORDER BY tblJobTaskDraft.TaskID";
					$result = $mysqli->query($query);

					if ($result->num_rows == 0)
						echo "No records found";

					while($row = $result->fetch_array()){
				?>
					<div class="panel panel-default">
						<div class="panel-body">
							<h2><?php echo $row['TaskName'] ?></h2>
							<div class="row">
								<div class="col-sm-4">
									<div class="alert alert-warning" role="alert">
										<div class="row">
											<div class="col-xs-3"><i class="fa fa-clock-o fa-4x text-warning"></i></div>
											<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Measured</strong><br>" . date('d-m-Y', strtotime($row['DateStarted'])) . "<br>by " . get_name_from_id($row['StartedBy'], $mysqli) ?></div>
										</div>
										<div class="row">
											<div class="col-xs-12 text-right">
												<button type="button" class="startdraft-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button>
											</div>
										</div>
									</div>
								</div>
								<?php 
									if (!empty($row['DateDrawn'])){
								?>
										<div class='col-sm-4'>
											<div class='alert alert-info' role='alert'>
												<div class="row">
													<div class="col-xs-3"><i class="fa fa-list-ol fa-4x text-info"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Drawing Started</strong><br>" . date('d-m-Y', strtotime($row['DateDrawn'])) . "<br>by " . get_name_from_id($row['DrawnBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="drawndraft-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button></div>
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
													<div class="col-xs-3"><i class="fa fa-check-square-o fa-4x text-success"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Sent To CNC</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="completedraft-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button></div>
												</div>										
											</div>
										</div>
								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>

			<div role="tabpanel" class="tab-pane" id="materials">
				<?php
					$query = "SELECT JobMaterialID, JobID, BoardDescription, BoardType, BoardQuantity, IsCarcass, CNCDateCompleted, CNCCompletedBy, EdgingDateCompleted, EdgingCompletedBy FROM tblJobMaterial WHERE JobID = $jobid ORDER BY IsCarcass DESC, JobMaterialID ";
					$result = $mysqli->query($query);

					if ($result->num_rows == 0)
					echo "No records found";

					while($row = $result->fetch_array()){
					?>
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-8"><h3><?php echo $row['BoardDescription'] . " - " . $row['BoardType'] ?></h3></div>
									<div class="col-sm-4 text-right"><h3>Quantity: <span class="label label-danger"><?php echo $row['BoardQuantity'] ?></span></h3></div>
								</div>											

								<div class="row">										
								<?php 
									if (!empty($row['CNCDateCompleted'])){
								?>
									
										<div class='col-sm-4'>
											<div class='alert alert-success' role='alert'>
												<div class="row">
													<div class="col-xs-3"><i class="fa fa-check-square-o fa-4x text-success"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>CNC Completed</strong><br>" . date('d-m-Y', strtotime($row['CNCDateCompleted'])) . "<br>by " . get_name_from_id($row['CNCCompletedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="completecnc-reset-btn btn btn-danger btn-sm" value="<?php echo $row['JobMaterialID']; ?>">Reset</button></div>
												</div>
											</div>
										</div>
									
								<?php } ?>

								<?php 
									if (!empty($row['EdgingDateCompleted'])){
								?>
									
										<div class='col-sm-4'>
											<div class='alert alert-success' role='alert'>
												<div class="row">
													<div class="col-xs-3"><i class="fa fa-check-square-o fa-4x text-success"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Edging Completed</strong><br>" . date('d-m-Y', strtotime($row['EdgingDateCompleted'])) . "<br>by " . get_name_from_id($row['EdgingCompletedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="completeedging-reset-btn btn btn-danger btn-sm" value="<?php echo $row['JobMaterialID']; ?>">Reset</button></div>
												</div>
											</div>
										</div>
									
								<?php } ?>
								</div>
								<div class="row">
									<div class="col-xs-12"><button type="button" class="material-delete-btn btn btn-danger btn-sm" value="<?php echo $row['JobMaterialID']; ?>">Delete</button></div>
								</div>

							</div>
						</div>
					<?php
					}
				?>
			</div>

			<div role="tabpanel" class="tab-pane" id="assembler">	
				<?php 
					$query = "SELECT tblJobTask.*, tblTask.TaskName FROM tblJobTask INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID WHERE tblJobTask.JobID = $jobid ORDER BY tblJobTask.TaskID";
					$result = $mysqli->query($query);

					if ($result->num_rows == 0)
						echo "No records found";

					while($row = $result->fetch_array()){
				?>
					<div class="panel panel-default">
						<div class="panel-body">
							<h2><?php echo $row['TaskName'] ?></h2>
							<div class="row">
								<div class="col-sm-4">
									<div class="alert alert-warning" role="alert">
										<div class="row">
											<div class="col-xs-3"><i class="fa fa-clock-o fa-4x text-warning"></i></div>
											<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Started</strong><br>" . date('d-m-Y', strtotime($row['DateStarted'])) . "<br>by " . get_name_from_id($row['StartedBy'], $mysqli) ?></div>
										</div>
										<div class="row">
											<div class="col-xs-12 text-right">
												<button type="button" class="start-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button>
												<button type="button" class="ready-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset Ready Check</button>
											</div>
										</div>
									</div>
								</div>
								<?php 
									if (!empty($row['DateChecked'])){
								?>
										<div class='col-sm-4'>
											<div class='alert alert-info' role='alert'>
												<div class="row">
													<div class="col-xs-3"><i class="fa fa-list-ol fa-4x text-info"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Checked</strong><br>" . date('d-m-Y', strtotime($row['DateChecked'])) . "<br>by " . get_name_from_id($row['CheckedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="checked-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button></div>
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
													<div class="col-xs-3"><i class="fa fa-check-square-o fa-4x text-success"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Completed</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="complete-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button></div>
												</div>										
											</div>
										</div>
								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>

			<div role="tabpanel" class="tab-pane" id="installer">
				<?php 
					$query = "SELECT tblJobTaskInstall.*, tblTask.TaskName FROM tblJobTaskInstall INNER JOIN tblTask ON tblJobTaskInstall.TaskID = tblTask.TaskID WHERE tblJobTaskInstall.JobID = $jobid ORDER BY tblJobTaskInstall.TaskID";
					$result = $mysqli->query($query);

					if ($result->num_rows == 0)
						echo "No records found";

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
													<div class="col-xs-3"><i class="fa fa-list-ol fa-4x text-info"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Checked</strong><br>" . date('d-m-Y', strtotime($row['DateChecked'])) . "<br>by " . get_name_from_id($row['CheckedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="checkedinstall-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button></div>
												</div>
											</div>
										</div>
								<?php } ?>

								<?php 
									if (!empty($row['DateCompleted'])){
										if ($row['SentToMaint']<>0)
											$completestring = "Sent to Maintenance";
										else
											$completestring = "Complete"
								?>
										<div class='col-sm-4'>
											<div class='alert alert-success' role='alert'>
												<div class="row">
													<div class="col-xs-3"><i class="fa fa-check-square-o fa-4x text-success"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>".$completestring."</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="completeinstall-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button></div>
												</div>										
											</div>
										</div>
								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>

			<div role="tabpanel" class="tab-pane" id="maintenance">
				<?php 
					$query = "SELECT tblJobTaskMaint.*, tblTask.TaskName FROM tblJobTaskMaint INNER JOIN tblTask ON tblJobTaskMaint.TaskID = tblTask.TaskID WHERE tblJobTaskMaint.JobID = $jobid ORDER BY tblJobTaskMaint.TaskID";
					$result = $mysqli->query($query);

					if ($result->num_rows == 0)
						echo "No records found";

					while($row = $result->fetch_array()){
				?>
					<div class="panel panel-default">
						<div class="panel-body">
							<h2><?php echo $row['TaskName'] ?></h2>
							<div class="row">
								<?php 
									if (!empty($row['DateStarted'])){
								?>
										<div class='col-sm-4'>
											<div class='alert alert-info' role='alert'>
												<div class="row">
													<div class="col-xs-3"><i class="fa fa-list-ol fa-4x text-info"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Sent To Maintenance</strong><br>" . date('d-m-Y', strtotime($row['DateStarted'])) . "<br>by " . get_name_from_id($row['StartedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="startmaint-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button></div>
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
													<div class="col-xs-3"><i class="fa fa-check-square-o fa-4x text-success"></i></div>
													<div class="col-xs-9" style="vertical-align: middle;"><?php echo "<strong>Completed</strong><br>" . date('d-m-Y', strtotime($row['DateCompleted'])) . "<br>by " . get_name_from_id($row['CompletedBy'], $mysqli) ?></div>
												</div>
												<div class="row">
													<div class="col-xs-12 text-right"><button type="button" class="completemaint-reset-btn btn btn-danger btn-sm" value="<?php echo $row['TaskID']; ?>">Reset</button></div>
												</div>										
											</div>
										</div>
								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		<br>
		<button type="button" id="return-btn" class="btn btn-warning">Return</button>
	</div>
</div>