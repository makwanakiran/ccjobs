<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

if (isset($_REQUEST["page"])) { $page  = $_REQUEST["page"]; } else { $page=1; };  

?>

<div class="table-responsive">

	<?php
				
		$query = "SELECT COUNT(UserID) FROM tblAdminUser";
		$result = $mysqli->query($query);
		$row = $result->fetch_array();

        $total_records = $row[0];  
        $total_pages = ceil($total_records / $recordsperpage); 

        if ($page > $total_pages)
            $page = $total_pages;
            
        $start_from = ($page-1) * $recordsperpage; 
		
		if ($row[0] > 0 ){
	?> 
    
        <table class="table table-striped bg-info shaded-icon">
            <thead>
                <tr>
                  <th>Name</th>
                  <th width="80px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                
                <?php 
                $query = "SELECT UserID, FullName FROM tblAdminUser ORDER BY FullName LIMIT $start_from, $recordsperpage";
                $result = $mysqli->query($query);
                
                while($row = $result->fetch_array()){
                ?>
                    <tr>
                        <td><?php echo $row['FullName'] ?></td>
                        <td>                            
                            <button type="button" value="<?php echo $row['UserID']; ?>" class="btn btn-warning btn-xs edit-btn" data-toggle="tooltip" data-placement="top" title="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                            <button type="button" value="<?php echo $row['UserID']; ?>" class="btn btn-danger btn-xs delete-btn" data-toggle="tooltip" data-placement="top" title="Delete"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
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
            echo "<li><a href='admin_users.php?page=".$i."'>".$i."</a></li>";
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
        hrefTextPrefix : 'admin_users.php?page='
    });
</script>

<?php
}
     
?>  
