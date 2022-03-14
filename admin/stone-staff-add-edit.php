<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

?>
<form id='stone-staff-form' action='#' method='post'>
    <input type="hidden" id="action" name="action" value="<?php if (isset($_POST['action'])) { echo $_POST['action']; } ?>">
    <input type="hidden" id="stoneid" name="stoneid" value="<?php if (isset($_POST['stoneid'])) { echo $_POST['stoneid']; } ?>">
    
	<div class="form-group">
		<label for="inputUserID">Employee</label>
		<select id="inputUserID" name="inputUserID" class="form-control" required>
			<option value="">Please Select</option>
			<?php
				$query = "SELECT UserID, FullName FROM tblUser WHERE IsStone <> 0 AND IsForeman = 0 AND Active <> 0 ORDER BY FullName";
				$result = $mysqli->query($query);			

				while($row = $result->fetch_array())
				{	
					if (isset($formuserid))
						$selected = ($row['UserID'] == $formuserid) ? " SELECTED" : ""; 	
					else
						$selected = "";

					echo "<option value=" . $row['UserID'] . " $selected>" . $row['FullName'] . "</option>";
				}
			?>
		</select>
	</div>


	<div class="form-group">
		<label for="inputDescription">Description (if no linked job)</label>
		<input type="text" class="form-control jobgroup" id="inputDescription" name="inputDescription"  autocomplete="off" placeholder="Description" value="<?php if (isset($formdescription)) { echo htmlspecialchars($formdescription, ENT_QUOTES); } ?>">
	</div>

	<div class="form-group">
		<label for="inputStoneDate">Date</label>
		<input type="text" class="form-control" style="position: relative; z-index: 5;" id="inputStoneDate" name="inputStoneDate" required autocomplete="off" placeholder="Date" date="true" value="<?php if (isset($formscheduledate)) { echo htmlspecialchars(date("d-m-Y", strtotime($formscheduledate)), ENT_QUOTES); } ?>">
	</div>
	
	<div class="form-group">
		<label for="inputNotes">Notes</label>
		<textarea class="form-control" id="inputNotes" name="inputNotes" rows="6"><?php if (isset($formnotes)) { echo htmlspecialchars($formnotes, ENT_QUOTES); } ?></textarea>
	</div>	

    <button type="submit" id="save-btn" class="btn btn-primary">Save</button>
	<button type="button" id="return-btn" class="btn btn-warning"  data-dismiss="modal">Return</button>
	
	<?php if ($_POST['action'] == "edit"){ ?>
	<button type="button" id="stone-delete-btn" class="btn btn-danger pull-right" value="<?php if (isset($_POST['stoneid'])) { echo $_POST['stoneid']; } ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</button> 
	<?php } ?>
</form>