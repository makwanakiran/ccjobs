<?php

	include("config.php");
	include("functions.php");
	
	sec_session_start();

	if(isset($_POST['inputUserID'])) { 
	   $userid = $_POST['inputUserID'];

	   if(login($userid, $mysqli) == true) {
		  // Login success
			if ($_SESSION['is_delivery'] <> 0)
				header('Location: delivery-schedule.php');
			else if ($_SESSION['is_maint'] <> 0)
				header('Location: maint-jobs.php');
			else
				header('Location: job-lookup.php');
		  
	   } else {
		  // Login failed
		  $message = "ID Number not found!";
	   }
	} 

	function login($userid, $mysqli) {

	   if ($stmt = $mysqli->prepare("SELECT UserID, FullName, IsForeman, IsDraftsman, IsCNC, IsEdging, IsAssembler, IsInstaller, IsDelivery, IsMaint,JobApprove FROM tblUser WHERE UserID = ? AND Active <> 0 LIMIT 1")) { 
		  $stmt->bind_param('s', $userid);
		  $stmt->execute();
		  $stmt->store_result();
		  $stmt->bind_result($user_id, $full_name, $is_foreman, $is_draftsman, $is_cnc, $is_edging, $is_assembler, $is_installer, $is_delivery, $is_maint, $is_JobApprove);
		  $stmt->fetch();
	 	
		  if($stmt->num_rows == 1) { // If the user exists 
				$_SESSION['user_id'] = $user_id;
				$_SESSION['full_name'] = $full_name;
				$_SESSION['is_foreman'] = $is_foreman;
				$_SESSION['is_draftsman'] = $is_draftsman;
				$_SESSION['is_cnc'] = $is_cnc;
				$_SESSION['is_edging'] = $is_edging;
				$_SESSION['is_assembler'] = $is_assembler;
				$_SESSION['is_installer'] = $is_installer;
				$_SESSION['is_delivery'] = $is_delivery;
				$_SESSION['is_maint'] = $is_maint;
				$_SESSION['is_JobApprove'] = $is_JobApprove;
				return true;    
		  } else {
			 // No user exists. 
			 return false;
		  }
	   }
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
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

			<form class="form-keypad" method="post" action="index.php">
				<img src="img/logo_large.png" class="img-responsive" alt="Challenge Cabinets">
				<br />
				<?php if (isset($message)){?><div class="alert alert-danger" role="alert"><?php echo $message ?></div><?php } ?>
				
					<div class="form-group">
						<label for="inputUsername">ID Number</label>
						<input type="number" id="inputUserID" name="inputUserID" class="form-control" autocomplete="off" required autofocus>
					</div>
						
					<div id="keypad" class="form-group">
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="1">1</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="2">2</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="3">3</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="4">4</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="5">5</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="6">6</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="7">7</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="8">8</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="9">9</button>
						<button class="reset-btn col-xs-4 btn btn-lg btn-danger" type="reset">CLEAR</button>
						<button class="num-btn col-xs-4 btn btn-lg btn-default" type="button" value="0">0</button>
						<button class="col-xs-4 btn btn-lg btn-primary" type="submit">ENTER</button>
					</div>

			</form>



    </div> <!-- /container -->
	<div class="container">
		<div class="form-group text-center"><br /><p><a href="admin/">Switch to Admin login ></a></p></div>
	</div>

    <script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/additional-methods.min.js"></script>

	<script type='text/javascript'>

		$('form').on('submit', function(e){
			//e.preventDefault();
		});
		
		$(document).ready(function(){

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

			var validator = $('.form-keypad').validate();

			$(document).on('click', '.num-btn', function(){ 
				$('#inputUserID').val($('#inputUserID').val() + $(this).val());
			});

			$(document).on('click', '.reset-btn', function(){ 
				$('.alert').hide();
				validator.resetForm();
			});
		});

	</script>


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>