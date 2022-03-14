<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_REQUEST["page"])) { $page  = $_REQUEST["page"]; } else { $page=1; };  

//search variables
$where = " WHERE ProjectID IS NOT NULL AND Deleted = 0";


?>

<div class="table-responsive">

	<?php
		$query = "SELECT COUNT(ProjectID) FROM tblproject $where";

		$result = $mysqli->query($query);
		$row = $result->fetch_array(); 
		$total_records = $row[0];  
		$total_pages = ceil($total_records / $recordsperpage); 

		if ($page > $total_pages)
			$page = $total_pages;
			
		$start_from = ($page-1) * $recordsperpage;
		
		if ($row[0] > 0 ){
	?> 
    
        <table class="table table-striped">
            <thead>
                <tr>
                    <th nowrap>Project No.</th>
                    <th>Project Nmae</th>
                    <th>Date Entered</th>
                    <th style="width:40px;"></th>
                </tr>
            </thead>
            <tbody>
                
                <?php 
                $query = "SELECT ProjectID, ProjectName, Discription, DateEntered FROM tblproject $where ORDER BY ProjectID LIMIT $start_from, $recordsperpage";

			    $result = $mysqli->query($query);
                
                while($row = $result->fetch_array()){
                ?>
                    <tr>
                        <td ><?php echo $row['ProjectID'] ?></td>
                        <!-- <td ><?php echo $row['ProjectName'] ?></td>      -->                  
                        <td><b><a href="jobs.php?project=<?php echo $row['ProjectID'] ?>"><?php echo $row['ProjectName'] ?></a></b></td>
                        <td><?php echo date("d-m-Y", strtotime($row['DateEntered'])) ?></td>
                        <td><?php echo (!empty($row['DateMeasure']) ? date("d-m-Y", strtotime($row['DateMeasure']))  : ""); ?></td>

                        <td nowrap>   
                            <button type="button" value="<?php echo $row['ProjectID']; ?>" class="btn btn-primary btn-xs edit-btn" data-toggle="tooltip" data-placement="top" title="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
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
            echo "<li><a href='projects.php?page=".$i."'>".$i."</a></li>";
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
        hrefTextPrefix : 'projects.php?page='
    });
</script>

<?php
}
	 
?>	