<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_POST['action']))
{

	if ($_POST['action'] == "edit"){
		
		$jobid = isset($_POST['jobid']) ? $_POST['jobid'] : die('ERROR: Job ID not found.');

		if ($stmt = $mysqli->prepare("SELECT JobID,ProjectID, JobAddress, Builder, DateMeasure, MeasureBy,status FROM tblJob WHERE JobID = ? LIMIT 1")) { 
			$stmt->bind_param('i', $jobid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($formjobid,$projectId, $formjobaddress, $formbuilder, $formdatemeasure, $formmeasureby,$status);
			$stmt->fetch();
		}
	}
}
?>

<form id='job-form' action='#' method='post'>
    <input type="hidden" id="action" name="action" value="<?php if (isset($_POST['action'])) { echo $_POST['action']; } ?>">
    <input type="hidden" id="jobid" name="jobid" value="<?php if (isset($_POST['jobid'])) { echo $_POST['jobid']; } ?>">

    <div class="form-group">
	  <label for="projectId">Select Project:</label>
	  <select class="form-control" id="projectId" name="projectId">
	    <option value="">None</option>
			<?php
				$query = "SELECT ProjectID, ProjectName FROM tblproject  ORDER BY ProjectName";
				$result = $mysqli->query($query);			

				while($row = $result->fetch_array())
				{	
					if (isset($projectId))
						$selected = ($row['ProjectID'] == $projectId) ? " SELECTED" : ""; 	
					else
						$selected = "";

					echo "<option value=" . $row['ProjectID'] . " $selected>" . $row['ProjectName'] . "</option>";
				}
			?>
	  </select>
	</div>
    
    <div class="form-group">
        <label for="inputJobAddress">Address</label>
        <input type="text" class="form-control" id="inputJobAddress" name="inputJobAddress" placeholder="Address" required value="<?php if (isset($formjobaddress)) { echo htmlspecialchars($formjobaddress, ENT_QUOTES); } ?>">
    </div>
	<div class="form-group">
        <label for="inputBuilder">Builder</label>
        <input type="text" class="form-control" id="inputBuilder" name="inputBuilder" placeholder="Builder" value="<?php if (isset($formbuilder)) { echo htmlspecialchars($formbuilder, ENT_QUOTES); } ?>">
    </div>   

	<div class="form-group">
		<label for="inputDateMeasure">Measure Date</label>
		<input type="text" class="form-control" style="position: relative; z-index: 5;" id="inputDateMeasure" name="inputDateMeasure" autocomplete="off" placeholder="Measure Date" date="true" value="<?php if (isset($formdatemeasure)) { echo htmlspecialchars(date("d-m-Y", strtotime($formdatemeasure)), ENT_QUOTES); } ?>">
	</div>

	<div class="form-group">
		<label for="inputMeasureBy">Draftsman</label>
		<select id="inputMeasureBy" name="inputMeasureBy" class="form-control">
			<option value="">None</option>
			<?php
				$query = "SELECT UserID, FullName FROM tblUser WHERE IsDraftsman <> 0 ORDER BY FullName";
				$result = $mysqli->query($query);			

				while($row = $result->fetch_array())
				{	
					if (isset($formmeasureby))
						$selected = ($row['UserID'] == $formmeasureby) ? " SELECTED" : ""; 	
					else
						$selected = "";

					echo "<option value=" . $row['UserID'] . " $selected>" . $row['FullName'] . "</option>";
				}
			?>
		</select>
	</div>
	<?php 
	if ($_POST['action'] == "edit"){
	?>
	<div class="form-group">
		<label for="status">Job Status</label>
		
		<select id="status" name="status" class="form-control">
			<option value="" disabled>None</option>
			<?php
			$opneChecked = '';
			$closeChecked = '';

				if($status == 1)
				{
					$opneChecked = "selected";
				}
				elseif($status == 2)
				{
					$closeChecked = "selected";
				}

			?>
			<option value="1" <?php echo $opneChecked; ?>>Open</option>
			<option value="2" <?php echo $closeChecked; ?>>Close</option>
		</select>
	</div>
	<?php } ?>
    <button type="submit" id="save-btn" class="btn btn-primary">Save</button>
	<button type="button" id="return-btn" class="btn btn-warning">Return</button>
	
	<?php if ($_POST['action'] == "edit"){ ?>
	<button type="button" id="delete-btn" class="btn btn-danger pull-right" value="<?php if (isset($formjobid)) { echo $formjobid; } ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</button> 
	<?php } ?>

</form>