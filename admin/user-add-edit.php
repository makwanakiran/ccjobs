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
		
		$userid = isset($_POST['userid']) ? $_POST['userid'] : die('ERROR: User ID not found.');

		if ($stmt = $mysqli->prepare("SELECT UserID, FullName, Email, IsForeman, IsAssembler, IsInstaller, IsDraftsman, IsCNC, IsEdging, IsDelivery, IsMaint, IsStone, SendAlerts,JobApprove, Active FROM tblUser WHERE UserID = ? LIMIT 1")) { 
			$stmt->bind_param('s', $userid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($formuserid, $formfullname, $formemail, $formisforeman, $formisassembler, $formisinstaller, $formisdraftsman, $formiscnc, $formisedging, $formisdelivery, $formismaint, $formisstone, $formsendalerts, $formjobApprove, $formactive);
			$stmt->fetch();
		}
	}
}
?>

<form id='user-form' action='#' method='post'>
    <input type="hidden" id="action" name="action" value="<?php if (isset($_POST['action'])) { echo $_POST['action']; } ?>">
    <input type="hidden" id="userid" name="userid" value="<?php if (isset($_POST['userid'])) { echo $_POST['userid']; } ?>">
    
    <div class="form-group">
        <label for="inputFullName">Name</label>
        <input type="text" class="form-control" id="inputFullName" name="inputFullName" placeholder="Full Name" required value="<?php if (isset($formfullname)) { echo htmlspecialchars($formfullname, ENT_QUOTES); } ?>">
    </div>
    <div class="form-group">
        <label for="inputUsername">User ID</label>
        <input type="text" class="form-control" id="inputUsername" name="inputUsername" placeholder="Username" digits="true" minlength="4" required value="<?php if (isset($formuserid)) { echo htmlspecialchars($formuserid, ENT_QUOTES); } ?>">
    </div>
    <div class="form-group">
        <label for="inputEmail">Email address</label>
        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email" value="<?php if (isset($formemail)) { echo htmlspecialchars($formemail, ENT_QUOTES); } ?>">
    </div>
    <div class="form-group">
        <label for="inputIsForeman">Foreman</label>
        <input name="inputIsForeman" type="checkbox" value="1" <?php if (isset($formisforeman)) { if ($formisforeman==1){ echo " CHECKED"; } } ?>>
    </div>
    <div class="form-group">
        <label for="inputIsDraftsman">Draftsman</label>
        <input name="inputIsDraftsman" type="checkbox" value="1" <?php if (isset($formisdraftsman)) { if ($formisdraftsman==1){ echo " CHECKED"; } } ?>>
    </div>  
    <div class="form-group">
        <label for="inputIsCNC">CNC</label>
        <input name="inputIsCNC" type="checkbox" value="1" <?php if (isset($formiscnc)) { if ($formiscnc==1){ echo " CHECKED"; } } ?>>
    </div> 
    <div class="form-group">
        <label for="inputIsEdging">Edging</label>
        <input name="inputIsEdging" type="checkbox" value="1" <?php if (isset($formisedging)) { if ($formisedging==1){ echo " CHECKED"; } } ?>>
    </div>     
    <div class="form-group">
        <label for="inputIsAssembler">Assembler</label>
        <input name="inputIsAssembler" type="checkbox" value="1" <?php if (isset($formisassembler)) { if ($formisassembler==1){ echo " CHECKED"; } } ?>>
    </div>  
    <div class="form-group">
        <label for="inputIsInstaller">Installer</label>
        <input name="inputIsInstaller" type="checkbox" value="1" <?php if (isset($formisinstaller)) { if ($formisinstaller==1){ echo " CHECKED"; } } ?>>
    </div>        
    <div class="form-group">
        <label for="inputIsDelivery">Delivery</label>
        <input name="inputIsDelivery" type="checkbox" value="1" <?php if (isset($formisdelivery)) { if ($formisdelivery==1){ echo " CHECKED"; } } ?>>
    </div> 
    <div class="form-group">
        <label for="inputIsMaint">Maintenance</label>
        <input name="inputIsMaint" type="checkbox" value="1" <?php if (isset($formismaint)) { if ($formismaint==1){ echo " CHECKED"; } } ?>>
    </div> 
    <div class="form-group">
        <label for="inputIsStone">Stone Installer</label>
        <input name="inputIsStone" type="checkbox" value="1" <?php if (isset($formisstone)) { if ($formisstone==1){ echo " CHECKED"; } } ?>>
    </div>  
    <div class="form-group">
        <label for="inputSendAlerts">Send Email Alerts</label>
        <input name="inputSendAlerts" type="checkbox" value="1" <?php if (isset($formsendalerts)) { if ($formsendalerts==1){ echo " CHECKED"; } } ?>>
    </div>
    <div class="form-group">
        <label for="inputJobApprove">Job Approve</label>
        <input name="inputJobApprove" type="checkbox" value="1" <?php if (isset($formjobApprove)) { if ($formjobApprove==1){ echo " CHECKED"; } } ?>>
    </div>      
    <div class="form-group">
        <label for="inputActive">Active</label>
        <input name="inputActive" type="checkbox" value="1" <?php if ($_POST['action']=="add"){ echo " CHECKED"; } if (isset($formactive)) { if ($formactive==1){ echo " CHECKED"; } } ?>>
    </div>        
    <hr />
    
    <button type="submit" id="save-btn" class="btn btn-primary">Save</button>
	<button type="button" id="return-btn" class="btn btn-warning">Return</button>
</form>