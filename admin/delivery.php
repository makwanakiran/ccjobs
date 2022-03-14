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
      <span id="pageTitle">Delivery Schedule</span>
      <button type="button" id="delivery-staff-add" class="btn btn-primary pull-right" style="margin-left: 1%;"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Staff Add</button>
      <button type="button" id="add-btn" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New</button>
    </h1>
</div>

<div id="alert"></div>

<div id='page-content'></div>
<div id='loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>

<br />

<div class="modal fade" tabindex="-1" role="dialog" id="delivery-modal">
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

<div class="modal fade" tabindex="-1" role="dialog" id="delivery-staff-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
	  	<div id="delivery-staff-modal-alert"></div>
	  	<div id='delivery-staff-modal-content'></div>
	  	<div id='delivery-staff-modal-loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>
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
			addEditDelivery("add", 0);
		});

		$('#delivery-staff-add').click(function(){
			DeliveryStaffAddEdit("add", 0);
		});



		function showWeek(weekStart){
			$('#loader-image').show();
			$('#add-btn').show();
			
			$('#page-content').hide();
			$('#alert').hide();
			
			$('#page-content').load('delivery-calendar.php', { weekstart: weekStart }, function(){ 
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');

				$(".entry").sortable({
					connectWith: ".entry",
					containment: "#caltable",
					update: function(event, ui){

						var deliveryid = ui.item.attr('data-delivery-id');
						var deliverydate = $(this).attr('data-date');
						
						if (ui.sender){
							$.post("delivery-crud.php", {action: "move", deliveryid: deliveryid, deliverydate: deliverydate})
								.done(function(data) {
									//$('#delivery-modal').modal('hide');
									//showWeek(currentWeekStart);
								});
						}
						var sortorder = $(this).sortable("toArray", {attribute: "data-delivery-id"});
						
						$.post("delivery-crud.php", {action: "sort", sortorder: sortorder})
							.done(function(data) {
								//$('#delivery-modal').modal('hide');
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
					var deliveryid = $(this).attr('data-delivery-id');
					addEditDelivery("edit", deliveryid);
				});

				$('.add-entry-btn').click(function(){
					var deliverydate = $(this).attr("data-delivery-date");
					addEditDelivery("add", 0, deliverydate);
				});



				$('.delete-delivery-staff-btn').click(function(){
					deleteStaffDelivery($(this).val());
				});

				$('.delivery-week-btn').click(function(){
					event.preventDefault();
					currentWeekStart = $(this).val();
					showWeek($(this).val());
				});

				$('.staff-delivery-calendar-edit').dblclick(function () {
					var deliveryid = $(this).attr('data-delivery-id');
					DeliveryStaffAddEdit("edit", deliveryid);
				});
				/*
				$('.delivery-add-entry-btn').click(function(){
					var deliverydate = $(this).attr("data-delivery-date");
					var userid = $(this).val();
					DeliveryStaffAddEdit("add", 0, deliverydate, userid);
				});*/

			});
		}
		

		function addEditDelivery(action, deliveryid, deliverydate){
			$('#modal-content').load('delivery-add-edit.php', { action: action, deliveryid: deliveryid }, function(){ 
				
				if (action == "add")
					$("#delivery-modal").find('.modal-title').text('Add Delivery Entry')
				else if (action == "edit")
					$("#delivery-modal").find('.modal-title').text('Edit Delivery Entry')

				$('#modal-loader-image').hide(); 
				$('#modal-content').fadeIn('slow');
				
				$("#delivery-form").validate({
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
				$('input[name="inputDeliveryDate"]').daterangepicker({format: 'DD-MM-YYYY' , singleDatePicker: true,showDropdowns: true});
				
				$('#delete-btn').click(function(){
					deleteJob($(this).val());
				});

				$('#viewjob-btn').click(function(){
					//alert($("#inputJobID").val());
					if ($("#inputJobID").val() != "")
						window.open('../job.php?jobid='+$("#inputJobID").val()+'#installer', '_blank');

				});

				//$('#inputDescription').prop('disabled', false);

				$('#delivery-modal').modal('show');

				if ($('#inputJobID').val() != ''){
					$.post("schedule-crud.php", { action: 'getbuilder', jobid: $('#inputJobID').val() }) 
						.done(function(data){
							$('#inputBuilder').val(data);
					});
				}
				
				if (deliverydate)
					$("#inputDeliveryDate").val(deliverydate);
				
			});
		}

		$(document).on('change', '#inputJobID', function(){ 
			if ($(this).val() != ''){
				$.post("delivery-crud.php", { action: 'getbuilder', jobid: $(this).val() }) 
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

		$(document).on('submit', '#delivery-form', function() {
			$('#modal-content').hide();
			$('#modal-loader-image').show();
			 
			$.post("delivery-crud.php", $(this).serialize())
				.done(function(data) {
					$('#delivery-modal').modal('hide');
					showWeek(currentWeekStart);
					
				});
					 
			return false;
		});
		
		
		function deleteJob(deleteid){
			$.confirm({
				text: "Are you sure you want to delete this delivery entry?",
				confirm: function() {			
					
					$.post("delivery-crud.php", { action: 'delete', deleteid: deleteid }) 
						.done(function(data){
							$('#delivery-modal').modal('hide');
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
		function DeliveryStaffAddEdit(action, deliveryid)
		{
			$('#delivery-staff-modal-content').load('delivery-staff-add-edit.php', { action: action, deliveryid: deliveryid }, function()
			{ 
				if (action == "add")
					$("#delivery-staff-modal").find('.modal-title').text('Add Staff Entry')
				else if (action == "edit")
					$("#delivery-staff-modal").find('.modal-title').text('Edit Staff Entry')

				$('#delivery-staff-modal-loader-image').hide(); 
				$('#delivery-staff-modal-content').fadeIn('slow');
				
				$("#delivery-staff-form").validate({
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
				$('input[name="inputDeliveryDate"]').daterangepicker({format: 'DD-MM-YYYY' , singleDatePicker: true,showDropdowns: true});
				
				$('#delivery-delete-btn').click(function(){
					deleteStaffDelivery($(this).val());
				});

				$('#viewjob-btn').click(function(){
					if ($("#inputJobID").val() != "")
						window.open('../job.php?jobid='+$("#inputJobID").val()+'#installer', '_blank');

				});

				if (action == "edit")
				{
					$.post("delivery-staff-crud.php", { action: 'edit', 'deliveryid': deliveryid }) 
						.done(function(data){
						var response = JSON.parse(data);
						$("#inputJobID").val(response.JobID).trigger('change');
						$("#inputDeliveryDate").val(response.DeliveryDate);
						$("#inputDescription").val(response.Description);
						$("#inputNotes").val(response.Notes);
						$("#action").val('update');
					});
				}


				$('#delivery-staff-modal').modal('show');

			});
		}

		$(document).on('submit', '#delivery-staff-form', function() {
			$('#modal-content').hide();
			$('#modal-loader-image').show();
			 
			$.post("delivery-staff-crud.php", $(this).serialize())
				.done(function(data) {
					$('#delivery-staff-modal').modal('hide');
					showWeek(currentWeekStart);
					
				});
					 
			return false;
		});
		
		
		function deleteStaffDelivery(deleteid){
			$.confirm({
				text: "Are you sure you want to delete this delivery staff entry?",
				confirm: function() {			
					$.post("delivery-staff-crud.php", { action: 'delete', deleteid: deleteid }) 
						.done(function(data){
							$('#delivery-staff-modal').modal('hide');
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