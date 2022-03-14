<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 


if (isset($_REQUEST["page"])) { $page  = $_REQUEST["page"]; } else { $page=1; };  
if (isset($_REQUEST["status"])) { $SortStatus  = $_REQUEST["status"]; } else { $SortStatus=1; };  

//search variables
$where = " WHERE JobID IS NOT NULL  AND Deleted = 0";

if($SortStatus != '')
{
    if($SortStatus == 99){ $where .= " AND status IS NOT NULL "; } else {  $where .= " AND status = " . $SortStatus;} 
}
else
{
    $where .= " AND status = 1";
}

if (!empty($_REQUEST['searchJobNo'])){	
	$where .= " AND JobID LIKE '%" . $mysqli->real_escape_string($_REQUEST['searchJobNo']) . "%'";
    $where .= " OR ParentJobId LIKE '%" . $mysqli->real_escape_string($_REQUEST['searchJobNo']) . "%'";
}

if (!empty($_REQUEST['searchAddress'])){	
	$where .= " AND JobAddress LIKE '%" . $mysqli->real_escape_string($_REQUEST['searchAddress']) . "%'";
}

if(!empty($_REQUEST['projectId']))
{
    $where .= " AND ProjectID = " . $_REQUEST['projectId'];
}

 
 /*$orderBy = "ORDER BY ProjectID ASC, JobID ASC";

 if($_REQUEST["type"] != null && $_REQUEST["sort"] != null)
 {
    $orderBy = "ORDER BY ProjectID ASC, ". $_REQUEST['type']." ".$_REQUEST['sort'] ;
 }   */

 $orderBy = "ORDER BY JobID DESC";

 if($_REQUEST["type"] != null && $_REQUEST["sort"] != null)
 {
    $orderBy = "ORDER BY ". $_REQUEST['type']." ".$_REQUEST['sort'] ;
 }   

?>

<div class="table-responsive">

	<?php
		$query = "SELECT COUNT(JobID) FROM tblJob $where";
        //echo $query;die; 
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
                    <th nowrap>
                        Sub job No. 
                        <span class="sort-icon">
                            <i class="fa fa-sort-asc" id="subJobIdASC" data-name="subJobId" data-sort="ASC" style="color:red;" aria-hidden="true"></i>
                            <i class="fa fa-sort-desc" id="subJobIdDESC" data-name="subJobId" data-sort="DESC" aria-hidden="true"></i>
                        </span>
                    </th>
                    <th>Address
                        <span class="sort-icon"> 
                            <i class="fa fa-sort-asc" id="JobAddressASC" data-name="JobAddress" data-sort="ASC"  aria-hidden="true"></i>
                            <i class="fa fa-sort-desc" id="JobAddressDESC" data-name="JobAddress" data-sort="DESC" aria-hidden="true"></i>
                        </span>
                    </th>
                    <th>Date Entered
                        <span class="sort-icon"> 
                            <i class="fa fa-sort-asc" id="DateEnteredASC" data-name="DateEntered" data-sort="ASC"  aria-hidden="true"></i>
                            <i class="fa fa-sort-desc" id="DateEnteredDESC" data-name="DateEntered" data-sort="DESC" aria-hidden="true"></i>
                        </span>
                    </th>
                    <th>Status
                        <span class="sort-icon"> 
                            <i class="fa fa-sort-asc" id="statusASC" data-name="status" data-sort="ASC"  aria-hidden="true"></i>
                            <i class="fa fa-sort-desc" id="statusDESC" data-name="status" data-sort="DESC" aria-hidden="true"></i>
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
                   
                $query = "SELECT JobID,subJobId, ProjectID, JobAddress, DateEntered, DateMeasure,status FROM tblJob $where $orderBy LIMIT $start_from, $recordsperpage";

                $result = $mysqli->query($query);
                $getProjectName = '';
                while($row = $result->fetch_array()){

                    $ProjectName = '';

                    if($row['ProjectID'] != '')
                    {
                        
                        if ($stmt = $mysqli->prepare("SELECT ProjectName FROM tblproject WHERE ProjectID = ? LIMIT 1")) { 
                            $stmt->bind_param('i', $row['ProjectID']);
                            $stmt->execute();   
                            $stmt->store_result();
                            $stmt->bind_result($ProjectName);
                            $stmt->fetch();
                        }
                    }

                    if($getProjectName != $ProjectName)
                    {
                        $getProjectName = $ProjectName;
                        ?>

                        <tr>
                            <td colspan="6" class="text-center "><h5><?php echo $getProjectName; ?></h5></td>
                        </tr>
                    <?php
                    }
                ?>
                    <tr>
                        <td ><?php echo $row['JobID'] ?></td>
                        <td ><?php echo $row['subJobId'] ?></td>
                        <td ><?php echo $row['JobAddress'] ?></td>                       
                        <td><?php echo date("d-m-Y", strtotime($row['DateEntered'])) ?></td>
                        <td>
                            <?php 
                                if($row['status'] == 1)
                                {
                                    $status = 'open';
                                }
                                elseif($row['status'] == 2)
                                {
                                    $status = 'closed';
                                }
                                echo $status;
                            ?>
                         </td>
                        <td><?php echo (!empty($row['DateMeasure']) ? date("d-m-Y", strtotime($row['DateMeasure']))  : ""); ?></td>

                        <td nowrap>   
                            <button type="button" value="<?php echo $row['JobID']; ?>" class="btn btn-primary btn-xs edit-btn" data-toggle="tooltip" data-placement="top" title="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                           <!--  <button type="button" value="<?php echo $row['JobID']; ?>" class="btn btn-info btn-xs history-btn" data-toggle="tooltip" data-placement="top" title="History"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></button>     -->
                            <a href="../job.php?jobid=<?php echo $row['JobID']; ?>#draftsman" class="btn btn-info btn-xs history-btn" data-toggle="tooltip" data-placement="top" title="History"><span class="glyphicon glyphicon-time job-history-icon-custom" aria-hidden="true"></span></a>                        
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
        hrefTextPrefix : 'jobs.php?page='
    });

</script>

<?php
}
	 
?>	