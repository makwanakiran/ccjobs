<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

?>
<form id='delivery-staff-form' action='#' method='post'>
    <input type="hidden" id="action" name="action" value="<?php if (isset($_POST['action'])) { echo $_POST['action']; } ?>">
    <input type="hidden" id="deliveryid" name="deliveryid" value="<?php if (isset($_POST['deliveryid'])) { echo $_POST['deliveryid']; } ?>">
    
	<div class="form-group">
		<label for="inputJobID">Job</label>
		<div class="input-group">
			<select id="inputJobID" name="inputJobID" class="form-control selectpicker jobgroup">
				<option value="">None</option>
				<?php
					$query = "SELECT JobID, JobAddress FROM tblJob WHERE Deleted = 0 ORDER BY JobAddress";
					$result = $mysqli->query($query);			

					while($row = $result->fetch_array())
					{	
						if (isset($formjobid))
							$selected = ($row['JobID'] == $formjobid) ? " SELECTED" : ""; 	
						else
							$selected = "";

						echo "<option value=" . $row['JobID'] . " $selected>" . $row['JobAddress'] . "</option>";
					}
				?>
			</select>
			<span class="input-group-btn">
				<button id="viewjob-btn" class="btn btn-primary" type="button">View</button>
			</span>
		</div>

	</div>


	<div class="form-group">
		<label for="inputDescription">Description (if no linked job)</label>
		<input type="text" class="form-control jobgroup" id="inputDescription" name="inputDescription"  autocomplete="off" placeholder="Description" value="<?php if (isset($formdescription)) { echo htmlspecialchars($formdescription, ENT_QUOTES); } ?>">
	</div>

	<div class="form-group">
		<label for="inputDeliveryDate">Date</label>
		<input type="text" class="form-control" style="position: relative; z-index: 5;" id="inputDeliveryDate" name="inputDeliveryDate" required autocomplete="off" placeholder="Date" date="true" value="<?php if (isset($formscheduledate)) { echo htmlspecialchars(date("d-m-Y", strtotime($formscheduledate)), ENT_QUOTES); } ?>">
	</div>
	
	<div class="form-group">
		<label for="inputNotes">Notes</label>
		<textarea class="form-control" id="inputNotes" name="inputNotes" rows="6"><?php if (isset($formnotes)) { echo htmlspecialchars($formnotes, ENT_QUOTES); } ?></textarea>
	</div>	

    <button type="submit" id="save-btn" class="btn btn-primary">Save</button>
	<button type="button" id="return-btn" class="btn btn-warning"  data-dismiss="modal">Return</button>
	
	<?php if ($_POST['action'] == "edit"){ ?>
	<button type="button" id="delivery-delete-btn" class="btn btn-danger pull-right" value="<?php if (isset($_POST['deliveryid'])) { echo $_POST['deliveryid']; } ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</button> 
	<?php } ?>
</form>