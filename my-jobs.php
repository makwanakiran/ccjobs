<?php
	include("functions.php");

	sec_session_start();
	if(!isset($_SESSION['user_id'])){
		header("location:index.php");
		die();
	}
	
	include("config.php");

	if (isset($_REQUEST["page"])) { $page  = $_REQUEST["page"]; } else { $page=1; }; 

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
			<div class="col-xs-9 col-sm-3">
				<img src="img/logo_large.png" class="img-responsive" alt="Challenge Cabinets">
				<br>
			</div>
			<div class="col-sm-9">
				<ul class="nav nav-pills pull-right">
					<li role="presentation"><a href="index.php">Home</a></li>
					<li role="presentation"><a href="job-lookup.php">Change Job</a></li>
					<li role="presentation"><a href="job-search.php">Search</a></li>
				</ul>
				
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="well">				
						Current User: <strong><?php echo $_SESSION['full_name'] ?></strong>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						My Jobs
					</div>
					<div class="panel-body">
						<?php
							$where = " WHERE JobID IS NOT NULL AND Deleted = 0 AND MeasureBy = '" . $_SESSION['user_id'] . "'";
						?>

						<div id="page-content">

						<div class="table-responsive">
							<?php
								$query = "SELECT COUNT(JobID) FROM tblJob Jobs $where AND COALESCE((SELECT SUM(Weight) FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = Jobs.JobID AND tblJobTaskDraft.DateCompleted IS NOT NULL), 0) < 2";
								$result = $mysqli->query($query);
								$row = $result->fetch_array(); 
								$total_records = $row[0];  

								$total_pages = ceil($total_records / $recordsperpage); 

								if ($page > $total_pages)
									$page = $total_pages;
									
								$start_from = ($page-1) * $recordsperpage;
								
								if ($row[0] > 0 ){
							?>
								<table class="table table-striped bg-info shaded-icon table-hover">
								<thead>
									<tr>
										<th nowrap>
											Job No. 
											<span class="sort-icon">
												<i class="fa fa-sort-asc" id="JobIDASC" data-name="JobID" data-sort="ASC" style="color:red;" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" id="JobIDDESC" data-name="JobID" data-sort="DESC" aria-hidden="true"></i>
											</span>
										</th>
										<th>Address
											<span class="sort-icon"> 
												<i class="fa fa-sort-asc" id="JobAddressASC" data-name="JobAddress" data-sort="ASC"  aria-hidden="true"></i>
												<i class="fa fa-sort-desc" id="JobAddressDESC" data-name="JobAddress" data-sort="DESC" aria-hidden="true"></i>
											</span>
										</th>
										<th>Measure Date
											<span class="sort-icon"> 
												<i class="fa fa-sort-asc" id="DateMeasureASC" data-name="DateMeasure" data-sort="ASC"  aria-hidden="true"></i>
												<i class="fa fa-sort-desc" id="DateMeasureDESC" data-name="DateMeasure" data-sort="DESC" aria-hidden="true"></i>
											</span>
										</th>
										<th style="width:40px;"></th>
									</tr>
								</thead>
								<tbody>								
									<?php 
									//$query = "SELECT JobID, JobAddress, DateEntered, DateMeasure FROM tblJob $where ORDER BY DateMeasure, JobID LIMIT $start_from, $recordsperpage";
									$query = "SELECT JobID, JobAddress, DateEntered, DateMeasure FROM tblJob Jobs $where AND COALESCE((SELECT SUM(Weight) FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = Jobs.JobID AND tblJobTaskDraft.DateCompleted IS NOT NULL), 0) < 2 ORDER BY DateMeasure, JobID";
									
									$result = $mysqli->query($query);
									
									while($row = $result->fetch_array()){
									?>
										<tr>
											<td ><?php echo $row['JobID'] ?></td>
											<td ><?php echo $row['JobAddress'] ?></td>  
											<td ><?php  
													if($row['DateMeasure']){
														echo date("d-m-Y", strtotime($row['DateMeasure']));
													} else {
														echo '';
													}
												?></td>                       											
											<td nowrap>   
												<a href="job.php?jobid=<?php echo $row['JobID']; ?>#draftsman" class="btn btn-primary btn-xs edit-btn"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
											</td>
										</tr>
									<?php } ?>
								</tbody>   
							</table>
						<?php } else { echo "No records were found."; } ?>
					</div>
						</div>
						<?php 
						if ($total_pages > 1)
						{
						?>
						<nav>
							<ul class="pagination">

								<?php
								if ($total_pages > 1 && $page > 1){
								?>
								<li>
									<a class='page-numbers' href="?page=<?php echo $page-1 ?>" aria-label="Previous">
										<span aria-hidden="true">&laquo;</span>
									</a>
								</li>
								<?php
								}
								
								for ($i=1; $i<=$total_pages; $i++) {  
									$active = ($i == $page) ? "class='active'" : "";
									echo "<li $active><a class='page-numbers' href='?page=$i'>$i</a></li>";
								}; 
								
								if ($total_pages > 1 && $page < $total_pages){
								?>
								<li>
									<a class='page-numbers' href="?page=<?php echo $page+1 ?>" aria-label="Next">
										<span aria-hidden="true">&raquo;</span>
									</a>
								</li>
								<?php
								}
								?>
									
						</ul>
						</nav>

						<?php
						}
							
						?>	

					</div>
				</div>

			</div>
		</div>
    </div> <!-- /container -->

	<?php
	

	?>

    <script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/additional-methods.min.js"></script>
    <script src="js/jquery.confirm.min.js"></script>

	<script type='text/javascript'>

		$('form').on('submit', function(e){
			//e.preventDefault();
		});
		
		$(document).ready(function(){

			
		});

		var currentPage = "<?php echo $page;?>";
		$(document).on('click', '.fa-sort-asc', function(){ 
			
			var filtertype = $(this).attr('data-name');
			var filtersort = $(this).attr('data-sort');
			type = filtertype;
			sort = filtersort;
			ID = filtertype+""+filtersort;
			showJobs(currentPage,type,sort,ID);
			
		});

		$(document).on('click', '.fa-sort-desc', function(){ 
			
			var filtertype = $(this).attr('data-name');
			var filtersort = $(this).attr('data-sort');
			type = filtertype;
			sort = filtersort;
			ID = filtertype+""+filtersort;
			showJobs(currentPage,type,sort,ID);
			
		});
		function showJobs(page,type=null,sort=null,ID=null){
			$('#loader-image').show();
			$('#add-btn').show();
			$('#searchPanel').show();
			$('#page-content').hide();
			$('#alert').hide();

			$('#page-content').load('my-jobs-list.php', "page=" + page + "&type=" + type + "&sort=" + sort, function(){ 
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');
				$('[data-toggle="tooltip"]').tooltip();
				$('.fa-sort-asc').css('color','');
				$('.fa-sort-desc').css('color','');
				$('#'+ID).css('color','red');
			});
		}


	</script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>