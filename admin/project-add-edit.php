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
		
		$projectid = isset($_POST['projectid']) ? $_POST['projectid'] : die('ERROR: project ID not found.');

		if ($stmt = $mysqli->prepare("SELECT ProjectID, ProjectName, Discription,Prefix FROM tblproject WHERE ProjectID = ? LIMIT 1")) { 
			$stmt->bind_param('i', $projectid);
			$stmt->execute();	
			$stmt->store_result();
			$stmt->bind_result($formprojectid, $formprojectname, $formprojectDiscription,$prefix);
			$stmt->fetch();
		}
	}
}
?>

<form id='project-form' action='#' method='post'>
    <input type="hidden" id="action" name="action" value="<?php if (isset($_POST['action'])) { echo $_POST['action']; } ?>">
    <input type="hidden" id="projectid" name="projectid" value="<?php if (isset($_POST['projectid'])) { echo $_POST['projectid']; } ?>">
    
    <div class="form-group">
        <label for="inputprojectName">Project Name</label>
        <input type="text" class="form-control" id="inputprojectName" name="inputprojectName" placeholder="Name" required value="<?php if (isset($formprojectname)) { echo htmlspecialchars($formprojectname, ENT_QUOTES); } ?>">
    </div>
    <div class="form-group">
        <label for="inputprojectDiscription">Project Discription</label>
        <textarea class="form-control" rows="5" id="inputprojectDiscription" name="inputprojectDiscription" placeholder="Discription"><?php if (isset($formprojectDiscription)) { echo htmlspecialchars($formprojectDiscription, ENT_QUOTES); } ?></textarea>
    </div>

    <div class="form-group">
        <label for="inputprojectName">Job Number</label>
        <input type="number" class="form-control" id="inputprojectJobNumber" name="inputprojectJobNumber" placeholder="job Number" value="">
    </div>
    <div class="form-group">
        <label for="inputprojectName">Prefix</label>
        <input type="text" class="form-control" id="inputprojectPrefix" name="inputprojectPrefix" placeholder="Prefix" value="<?php if (isset($prefix)) { echo htmlspecialchars($prefix, ENT_QUOTES); } ?>">
    </div>
	

    <button type="submit" id="save-btn" class="btn btn-primary">Save</button>
	<button type="button" id="return-btn" class="btn btn-warning">Return</button>
	
	<?php if ($_POST['action'] == "edit"){ ?>
	<button type="button" id="delete-btn" class="btn btn-danger pull-right" value="<?php if (isset($formprojectid)) { echo $formprojectid; } ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</button> 
	<?php } ?>

</form>