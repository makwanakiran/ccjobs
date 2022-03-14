<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id'])){
    header("location:index.php");
    die();
}

include("config.php"); 


if (isset($_REQUEST["page"])) { $page  = $_REQUEST["page"]; } else { $page=1; };  
if (isset($_REQUEST["status"])) { $SortStatus  = $_REQUEST["status"]; } else { $SortStatus=1; };  

//search variables
$where = " WHERE JobID IS NOT NULL AND Deleted = 0 AND MeasureBy = '" . $_SESSION['user_id'] . "'";

$orderBy = "ORDER BY JobID DESC";
if($_REQUEST["type"] != null && $_REQUEST["sort"] != null)
{
    $orderBy = "ORDER BY ". $_REQUEST['type']." ".$_REQUEST['sort'] ;
}   

?>

<div class="table-responsive">
    
    <?php
		$query = $query = "SELECT COUNT(JobID) FROM tblJob Jobs $where AND COALESCE((SELECT SUM(Weight) FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = Jobs.JobID AND tblJobTaskDraft.DateCompleted IS NOT NULL), 0) < 2";
        
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
                
                $query = "SELECT JobID, JobAddress, DateEntered, DateMeasure FROM tblJob Jobs $where AND COALESCE((SELECT SUM(Weight) FROM tblJobTaskDraft INNER JOIN tblTask ON tblJobTaskDraft.TaskID = tblTask.TaskID WHERE tblJobTaskDraft.JobID = Jobs.JobID AND tblJobTaskDraft.DateCompleted IS NOT NULL), 0) < 2 ".$orderBy;
                
                $result = $mysqli->query($query);
                $getProjectName = '';
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

<?php 
if ($total_pages > 1)
{
?>
<nav class="pagination-nav">
   
  <ul class="pagination">
    <?php
      for ($i=1; $i<=$total_pages; $i++) {  
            $active = ($i == $page) ? "class='active'" : "";
            echo "<li><a href='jobs.php?page=".$i."'>".$i."</a></li>";
        };
    ?> 
  </ul>
</nav>

<script>
    $('.pagination').pagination({
        items: <?php echo $total_records;?>,
        itemsOnPage: <?php echo $recordsperpage;?>,
        cssStyle: 'light-theme',
        currentPage : <?php echo $page;?>,
        hrefTextPrefix : 'my-jobs.php?page='
    });

</script>

<?php

}
	 
?>	