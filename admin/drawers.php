<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 

$page = "schedule";

include("header.php"); 

?>

<div class="page-header">
    <h1>
      <span id="pageTitle">Draftsman Schedule</span>
      <button type="button" id="drawer-staff-add" class="btn btn-primary pull-right" style="margin-left:1%;"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Staff Add</button>
      <button type="button" id="add-btn" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New</button>
    </h1>
</div>

<div id="alert"></div>

<div id='page-content'></div>
<div id='loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>

<br />

<div class="modal fade" tabindex="-1" role="dialog" id="schedule-modal">
  <div class="modal-dialog" role="document">
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

<!-- Staff add/edit modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="drawer-staff-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
	  	<div id="drawer-staff-modal-alert"></div>
	  	<div id='drawer-staff-modal-content'></div>
	  	<div id='drawer-staff-modal-loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Staff add/edit modal -->



<script type='text/javascript'>

	$('form').on('submit', function(e){
		e.preventDefault();
	});
	
	$(document).ready(function(){

		showWeek();
		
		var currentWeekStart = ""; 

		$('#add-btn').click(function(){
			addEditSchedule("add", 0);
		});


		$('#drawer-staff-add').click(function(){
			DrawerStaffAddEdit("add", 0);
		});

		function showWeek(weekStart){
			$('#loader-image').show();
			$('#add-btn').show();
			
			$('#page-content').hide();
			$('#alert').hide();
			
			$('#page-content').load('drawer-calendar.php', { weekstart: weekStart }, function(){ 
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');

				$(".entry").sortable({
					connectWith: ".entry",
					containment: "#caltable",
					update: function(event, ui){

						var scheduleid = ui.item.attr('data-schedule-id');
						if(scheduleid==undefined)
						{
							scheduleid = ui.item.attr('data-drawer-id');
							var sortorder = $(this).sortable("toArray", {attribute: "data-drawer-id"});
						}
						else
						{
							var sortorder = $(this).sortable("toArray", {attribute: "data-schedule-id"});
						}
						var userid = $(this).attr('data-user-id');
						var scheduledate = $(this).attr('data-date');
						
						if (ui.sender){
							$.post("drawer-crud.php", {action: "move", drawerscheduleid: scheduleid, userid: userid, scheduledate: scheduledate})
								.done(function(data) {
									//$('#schedule-modal').modal('hide');
									//showWeek(currentWeekStart);
								});
						}
						
						$.post("drawer-crud.php", {action: "sort", sortorder: sortorder})
							.done(function(data) {
								//$('#schedule-modal').modal('hide');
								//showWeek(currentWeekStart);
							});

					}
				}).disableSelection();

				
				
				$('.delete-btn').click(function(){
					deleteJob($(this).val());
				});

				$('.week-btn').click(function(){
					event.preventDefault();
					currentWeekStart = $(this).val();
					showWeek($(this).val());
				});

				$('.calendar-entry').dblclick(function () {
					var scheduleid = $(this).attr('data-schedule-id');
					addEditSchedule("edit", scheduleid);
				});

				$('.add-entry-btn').click(function(){
					var scheduledate = $(this).attr("data-schedule-date");
					var userid = $(this).val();
					addEditSchedule("add", 0, scheduledate, userid);
				});


				$('.delete-drawer-staff-btn').click(function(){
					deleteDrawerStaff($(this).val());
				});

				$('.drawers-week-btn').click(function(){
					event.preventDefault();
					currentWeekStart = $(this).val();
					showWeek($(this).val());
				});

				$('.staff-drawers-calendar-edit').dblclick(function () {
					var drawerid = $(this).attr('data-drawer-id');
					DrawerStaffAddEdit("edit", drawerid);
				});


			});
		}
		

		function addEditSchedule(action, scheduleid, scheduledate, userid){
			$('#modal-content').load('drawer-add-edit.php', { action: action, drawerscheduleid: scheduleid }, function(){ 
				
				if (action == "add")
					$("#schedule-modal").find('.modal-title').text('Add Draftsman Schedule Entry')
				else if (action == "edit")
					$("#schedule-modal").find('.modal-title').text('Edit Draftsman Schedule Entry')

				$('#modal-loader-image').hide(); 
				$('#modal-content').fadeIn('slow');
				
				$("#schedule-form").validate({
					rules: {
						inputJobID: {
							required: "#inputDescription:blank"
						},
						inputDescription: {
							required: "#inputJobID:blank"
						}
					}
				});

				$('.selectpicker').selectpicker({liveSearch: true});
				$('input[name="inputScheduleDate"]').daterangepicker({format: 'DD-MM-YYYY' , singleDatePicker: true,showDropdowns: true});
				
				$('#delete-btn').click(function(){
					deleteJob($(this).val());
				});

				$('#viewjob-btn').click(function(){
					//alert($("#inputJobID").val());
					if ($("#inputJobID").val() != "")
						window.open('../job.php?jobid='+$("#inputJobID").val()+'#draftsman', '_blank');

				});

				//$('#inputDescription').prop('disabled', false);


				$('#schedule-modal').modal('show');

				if ($('#inputJobID').val() != ''){
					$.post("drawer-crud.php", { action: 'getbuilder', jobid: $('#inputJobID').val() }) 
						.done(function(data){
							$('#inputBuilder').val(data);
					});
				}

				if (userid)
					$("#inputUserID").val(userid);
				if (scheduledate)
					$("#inputScheduleDate").val(scheduledate);
				
			});
		}

		$(document).on('change', '#inputJobID', function(){ 
			if ($(this).val() != ''){
				$.post("drawer-crud.php", { action: 'getbuilder', jobid: $(this).val() }) 
					.done(function(data){
						$('#inputBuilder').val(data);
				});
			}
			else	
				$('#inputBuilder').val('');

		//		$('#inputDescription').val('');
		//		$('#inputDescription').prop('disabled', true);
		//	}
		//	else{
		//		$('#inputDescription').prop('disabled', false);
		//	}	
		});

		$(document).on('submit', '#schedule-form', function() {
			$('#modal-content').hide();
			$('#modal-loader-image').show();
			 
			$.post("drawer-crud.php", $(this).serialize())
				.done(function(data) {
					$('#schedule-modal').modal('hide');
					showWeek(currentWeekStart);
					
				});
					 
			return false;
		});
		
		
		function deleteJob(deleteid){
			$.confirm({
				text: "Are you sure you want to delete this draftsman schedule entry?",
				confirm: function() {			
					
					$.post("drawer-crud.php", { action: 'delete', deleteid: deleteid }) 
						.done(function(data){
							$('#schedule-modal').modal('hide');
							showWeek(currentWeekStart);
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}


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

		$.validator.methods.date = function (value, element) {
			return this.optional(element) || moment(value, 'DD/MM/YYYY').isValid();
		};

    
		function DrawerStaffAddEdit(action, drawerid)
		{
			$('#drawer-staff-modal-content').load('drawer-staff-add-edit.php', { action: action, drawerid: drawerid }, function()
			{ 
				if (action == "add")
					$("#drawer-staff-modal").find('.modal-title').text('Add Staff Entry')
				else if (action == "edit")
					$("#drawer-staff-modal").find('.modal-title').text('Edit Staff Entry')

				$('#drawer-staff-modal-loader-image').hide(); 
				$('#drawer-staff-modal-content').fadeIn('slow');
				
				$("#drawer-form").validate({
					rules: {
						inputJobID: {
							required: "#inputDescription:blank"
						},
						inputDescription: {
							required: "#inputJobID:blank"
						}
					}
				});
				

				$('#drawer-delete-btn').click(function(){
					deleteDrawerStaff($(this).val());
				});

				$('input[name="inputDrawerDate"]').daterangepicker({format: 'DD-MM-YYYY' , singleDatePicker: true,showDropdowns: true});

				if (action == "edit")
				{
					$.post("drawer-staff-crud.php", { action: 'edit', 'drawerid': drawerid }) 
						.done(function(data){
						var response = JSON.parse(data);
						$("#inputUserID").val(response.UserId);
						$("#inputDrawerDate").val(response.DrawerDate);
						$("#drawerid").val(response.DrawerId);
						$("#inputDescription").val(response.Description);
						$("#inputNotes").val(response.Notes);
						$("#action").val('update');
					});
				}
				$('#drawer-staff-modal').modal('show');
			});
		}

		$(document).on('submit', '#drawer-staff-form', function() {
			$('#modal-content').hide();
			$('#modal-loader-image').show();
			 
			$.post("drawer-staff-crud.php", $(this).serialize())
				.done(function(data) {
					$('#drawer-staff-modal').modal('hide');
					showWeek(currentWeekStart);
					
				});
					 
			return false;
		});

		function deleteDrawerStaff(deleteid)
		{
			$.confirm({
				text: "Are you sure you want to delete this draftsman staff entry?",
				confirm: function() {			
					$.post("drawer-staff-crud.php", { action: 'delete', deleteid: deleteid }) 
						.done(function(data){
							$('#drawer-staff-modal').modal('hide');
							showWeek(currentWeekStart);
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}
	});


	
</script>

<?php include("footer.php"); ?>