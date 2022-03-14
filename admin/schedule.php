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
      <span id="pageTitle">Schedule</span>
      <button type="button" id="schedule-staff-add" class="btn btn-primary pull-right" style="margin-left: 1%;"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Staff Add</button>
      <button type="button" id="add-btn" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New</button>
    </h1>
    <!-- <ul>
    <li>Show Which Schedule is being viewed e.g. Installer Schedule</li>
    <li>???List View</li>
    <li>??? Mounth View</li>
    <li>??? Mouse Over shows job details</li>
    <li>??? Do we want status colours</li>
    <li>??? Do you want Add New Project on this screen</li>
    <li>??? Do you want list unscheduled jobs</li>
    </ul>-->
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

<div class="modal fade" tabindex="-1" role="dialog" id="staff-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
	  	<div id="staff-modal-alert"></div>
	  	<div id='staff-modal-content'></div>
	  	<div id='staff-modal-loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>
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

		$('#schedule-staff-add').click(function(){
			ScheduleStaffAddEdit("add", 0);
		});

		function showWeek(weekStart){
			$('#loader-image').show();
			$('#add-btn').show();
			
			$('#page-content').hide();
			$('#alert').hide();
			
			$('#page-content').load('schedule-calendar.php', { weekstart: weekStart }, function(){ 
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');

				$(".entry").sortable({
					connectWith: ".entry",
					containment: "#caltable",
					update: function(event, ui){

						var scheduleid = ui.item.attr('data-schedule-id');
						var userid = $(this).attr('data-user-id');
						var scheduledate = $(this).attr('data-date');
						
						if (ui.sender)
						{
							$.post("schedule-crud.php", {action: "move", scheduleid: scheduleid, userid: userid, scheduledate: scheduledate})
								.done(function(data) {
								//$('#schedule-modal').modal('hide');
								//showWeek(currentWeekStart);
							});
						}
						var sortorder = $(this).sortable("toArray", {attribute: "data-schedule-id"});
						
						$.post("schedule-crud.php", {action: "sort", sortorder: sortorder})
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


				$('.delete-schedule-staff-btn').click(function(){
					deleteStaffSchedule($(this).val());
				});

				$('.staff-week-btn').click(function(){
					event.preventDefault();
					currentWeekStart = $(this).val();
					showWeek($(this).val());
				});

				$('.staff-schedule-edit').dblclick(function () {
					var scheduleid = $(this).attr('data-schedule-id');
					ScheduleStaffAddEdit("edit", scheduleid);
				});

			});
		}
		

		function addEditSchedule(action, scheduleid, scheduledate, userid)
		{
			$('#modal-content').load('schedule-add-edit.php', { action: action, scheduleid: scheduleid }, function(){ 

				if (action == "add")
					$("#schedule-modal").find('.modal-title').text('Add Schedule Entry')
				else if (action == "edit")
					$("#schedule-modal").find('.modal-title').text('Edit Schedule Entry')

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
					if ($("#inputJobID").val() != "")
						window.open('../job.php?jobid='+$("#inputJobID").val()+'#installer', '_blank');
				});

				//$('#inputDescription').prop('disabled', false);


				$('#schedule-modal').modal('show');

				if ($('#inputJobID').val() != ''){
					$.post("schedule-crud.php", { action: 'getbuilder', jobid: $('#inputJobID').val() }) 
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
				$.post("schedule-crud.php", { action: 'getbuilder', jobid: $(this).val() }) 
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
			 
			$.post("schedule-crud.php", $(this).serialize())
				.done(function(data) {
					$('#schedule-modal').modal('hide');
					showWeek(currentWeekStart);
					
				});
					 
			return false;
		});
		


		
		function deleteJob(deleteid){
			$.confirm({
				text: "Are you sure you want to delete this schedule entry?",
				confirm: function() {			
					
					$.post("schedule-crud.php", { action: 'delete', deleteid: deleteid }) 
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

		function ScheduleStaffAddEdit(action, scheduleid, scheduledate, userid)
		{
			$('#staff-modal-content').load('schedule-staff-add-edit.php', { action: action, scheduleid: scheduleid }, function(){ 
				if (action == "add")
					$("#staff-modal").find('.modal-title').text('Add Staff Entry')
				else if (action == "edit")
					$("#staff-modal").find('.modal-title').text('Edit Staff Entry')

				$('#staff-modal-loader-image').hide(); 
				$('#staff-modal-content').fadeIn('slow');
				
				$("#staff-form").validate({
					rules: {
						inputJobID: {
							required: "#inputDescription:blank"
						},
						inputDescription: {
							required: "#inputJobID:blank"
						}
					}
				});
				
				$('input[name="inputScheduleDate"]').daterangepicker({format: 'DD-MM-YYYY' , singleDatePicker: true,showDropdowns: true});


				$('#staff-modal').modal('show');

				$('#staff-delete-btn').click(function(){
					deleteStaffSchedule($(this).val());
				});


				if (action == "edit")
				{
					$.post("staff-schedule-crud.php", { action: 'edit', 'scheduleid': scheduleid }) 
						.done(function(data){
						var response = JSON.parse(data);
						$("#inputUserID").val(response.UserID);
						$("#inputScheduleDate").val(response.ScheduleDate);
						$("#inputDescription").val(response.Description);
						$("#inputNotes").val(response.Notes);
						$("#action").val('update');
					});
				}

			});
		}

		$(document).on('submit', '#staff-form', function() {
			$('#staff-modal-content').hide();
			$('#staff-modal-loader-image').show();
			 
			$.post("staff-schedule-crud.php", $(this).serialize())
				.done(function(data) {
				$('#staff-modal').modal('hide');
				showWeek(currentWeekStart);
			});
			return false;
		});

		function deleteStaffSchedule(deleteid){
			$.confirm({
				text: "Are you sure you want to delete this schedule staff entry?",
				confirm: function() {			
					
					$.post("staff-schedule-crud.php", { action: 'delete', deleteid: deleteid }) 
						.done(function(data){
							$('#staff-modal').modal('hide');
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