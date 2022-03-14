<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../img/favicon.ico">

    <title>Challenge Cabinets Admin</title>

    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=1.4" rel="stylesheet">
	<link rel="stylesheet" href="css/bootstrap-select.min.css">
	<link rel="stylesheet" href="css/daterangepicker-bs3.css">
    <link href="../font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link type="text/css" rel="stylesheet" href="css/simplePagination.css"/>

    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
        rel='stylesheet'>
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script src="../js/jquery-1.11.3.min.js"></script>
  <script src="js/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <!-- <script src="../js/bootstrap.min.js"></script> -->
  	<script src="js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/jquery.confirm.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/additional-methods.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
	<script src="js/bootstrap-filestyle.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/daterangepicker.js"></script>
  <script src="js/jQuery.print.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  <script src="js/jquery.simplePagination.js"></script>

  <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <!-- <script src="js/scripts/flot/jquery.flot.js" type="text/javascript"></script>
  <script src="js/scripts/flot/jquery.flot.resize.js" type="text/javascript"></script> -->
  <script src="js/scripts/datatables/jquery.dataTables.js" type="text/javascript"></script>
  <!-- <script src="js/scripts/common.js" type="text/javascript"></script> -->
  
  </head>

  <body>
	
    <!-- Fixed navbar -->
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                    <i class="icon-reorder shaded"></i></a><a class="brand" href="index.php"><img src="../img/logo_small.png" alt="Challenge Cabinets" /> </a>
                <div class="nav-collapse collapse navbar-inverse-collapse">
                    <ul class="nav nav-icons">
                        <!-- <li class=""><a href="">Job</a></li> -->
                        <!-- <li><a href="#"><i class="icon-eye-open"></i></a></li>
                        <li><a href="#"><i class="icon-bar-chart"></i></a></li> -->
                    </ul>
                    <!-- <form class="navbar-search pull-left input-append" action="#">
                    <input type="text" class="span3">
                    <button class="btn" type="button">
                        <i class="icon-search"></i>
                    </button>
                    </form> -->
                    <ul class="nav pull-right">
                        


                         <li <?php echo ($page == "welcome") ? "class='active'" : ""; ?>><a href="index.php"><i class="menu-icon icon-dashboard"></i>Dashboard</a></li>
                        <li <?php echo ($page == "projects") ? "class='active'" : ""; ?>><a href="projects.php"><i class="menu-icon icon-bullhorn"></i>Project</a>
                        </li>
                        <li <?php echo ($page == "jobs") ? "class='active'" : ""; ?>><a href="jobs.php"><i class="menu-icon icon-bullhorn"></i>Jobs</a>
                        </li>

                        <li class="dropdown <?php echo ($page == "schedule") ? 'active' : ''; ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Schedule
                            <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="schedule.php">Installers</a></li>
                                <li><a href="delivery.php">Delivery</a></li>
                                <li><a href="drawers.php">Draftsman</a></li>
                                <li><a href="stone.php">Stone</a></li>
                                <li><a href="assemblers.php">Assemblers</a></li>
                            </ul>
                        </li>

                        <li class="dropdown <?php echo ($page == "reports") ? 'active' : ''; ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports
                            <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="reports-draftsmen.php">Draftsmen</a></li>
                                        <li><a href="reports-cnc.php">CNC</a></li>
                                        <li><a href="reports-edging.php">Edging</a></li>
                                        <li><a href="reports-assemblers.php">Assemblers</a></li>
                                        <li><a href="reports-installers.php">Installers</a></li>
                            </ul>
                        </li>

                        <li <?php echo ($page == "users") ? "class='active'" : ""; ?>><a href="users.php"><i class="menu-icon icon-user"></i>Users</a></li>
                        <li <?php echo ($page == "adminusers") ? "class='active'" : ""; ?>><a href="admin_users.php"><i class="menu-icon icon-book"></i>Admin Users</a></li>    

                        <!-- <li><a href="#">Support </a></li> -->
                        <li class="nav-user dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="images/user.png" class="nav-avatar" />
                            <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <!-- <li><a href="#">Your Profile</a></li>
                                <li><a href="#">Edit Profile</a></li>
                                <li><a href="#">Account Settings</a></li> -->
                                <li class="divider"></li>
                                <li><a href="logout.php"><i class="menu-icon icon-signout"></i>Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.nav-collapse -->
            </div>
        </div>
        <!-- /navbar-inner -->
    </div>
    <!-- /navbar -->
        <div class="wrapper">
            <div class="container">
                <div class="row">
                    

    <!-- <div class="container">  -->   
      <div class="span12">
