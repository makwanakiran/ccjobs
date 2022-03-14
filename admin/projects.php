<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 

$page = "projects";

include("header.php"); 

?>

<div class="page-header">
    <h1>
      <span id="pageTitle">Projects</span>
      <button type="button" id="add-btn" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Project</button>
      
    </h1>
</div>


<div id="alert"></div>

<div id='page-content'></div>
<div id='loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>

<br />

<script type='text/javascript'>

    $('form').on('submit', function(e){
        e.preventDefault();
    });
    
    function changePageTitle(title){
        $('#pageTitle').html(title);
    }
    
    $(document).ready(function(){

        showProject(1);
        var currentPage = 1;
        var currentJobID = 0;
        
        $('#add-btn').click(function(){
            addEditProject("add", 0);
            $('#alert').hide();
        });
        
        function showProject(page){
            $('#loader-image').show();
            changePageTitle("Projects");
            $('#add-btn').show();
            $('#page-content').hide();
            $('#alert').hide();
            
            $('#page-content').load('project-list.php', $('#searchForm').serialize() + "&page=" + page, function(){ 
                $('#loader-image').hide(); 
                $('#page-content').fadeIn('slow');
                $('[data-toggle="tooltip"]').tooltip();
            });
        }
        

        function addEditProject(action, projectid){

            if (action == "add"){
                $('#add-btn').hide();
                changePageTitle("Add Project");
            }
            else{
                changePageTitle("Edit Project #"+projectid);
                $('#add-btn').show();
            }
            currentprojectID = projectid;
                    
            $('#page-content').hide();
            $('#loader-image').show();
            
            $('#page-content').load('project-add-edit.php', { action: action, projectid: projectid }, function(){ 
                
                $('#loader-image').hide(); 
                $('#page-content').fadeIn('slow');

                $("#project-form").validate();


                $('#delete-btn').click(function(){
                    deleteProject($(this).val());
                });
                
                $('#return-btn').click(function(){
                    $('#page-content').hide();
                    showProject(currentPage);
                });

            });
        }

        $(document).on('submit', '#project-form', function() {
            $('#page-content').hide();
            $('#loader-image').show();
             
            $.post("project-crud.php", $(this).serialize())
                .done(function(data) {
                    var response = jQuery.parseJSON(data);
                    $('#alert').html(response.msg);
                    $('#alert').show();
                    if (response.action == "add"){
                        $('#loader-image').hide();
                        $('#page-content').show();
                    }
                    else
                        addEditProject(response.action, response.last_insert_id)
                });
                     
            return false;
        });
        

        $(document).on('click', '.page-link', function(){ 
            event.preventDefault();
            var clickedPage = $(this).attr('href');
            var pageind = clickedPage.indexOf('page=');
            clickedPage = clickedPage.substring((pageind+5));
            
            currentPage = clickedPage;
            showProject(clickedPage);
        });
        
        
        $(document).on('click', '.edit-btn', function(){ 
            $('#alert').hide();
            addEditProject("edit", $(this).val());
        });
        
        function deleteProject(deleteid){
            $.confirm({
                text: "Are you sure you want to delete this project?",
                confirm: function() {
                    $('#page-content').hide();
                    $('#loader-image').show();
                    
                    $.post("project-crud.php", { action: 'delete', deleteid: deleteid }) 
                        .done(function(data){
                            showProject(currentPage);
                            $('#alert').html(data);
                            $('#alert').show();
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

    
    });
    
</script>

<?php include("footer.php"); ?>