<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
}

include("config.php"); 

$today = date('d-m-Y');

$weekstart = (!empty($_REQUEST["weekstart"])) ? $_REQUEST["weekstart"] : date('d-m-Y');

$mondaydate = date('d-m-Y',strtotime("monday this week", strtotime($weekstart)));
$tuesdaydate = date('d-m-Y',strtotime("tuesday this week", strtotime($weekstart)));
$wednesdaydate = date('d-m-Y',strtotime("wednesday this week", strtotime($weekstart)));
$thursdaydate = date('d-m-Y',strtotime("thursday this week", strtotime($weekstart)));
$fridaydate = date('d-m-Y',strtotime("friday this week", strtotime($weekstart)));

$datearray = array($mondaydate, $tuesdaydate, $wednesdaydate, $thursdaydate, $fridaydate);

echo "<h3>WEEK " . $mondaydate . " to " . $fridaydate . "</h3>";

?>

<div class="table-responsive">

    <table id="caltable" class="table table-bordered table-striped bg-info shaded-icon">
        <thead>
            <tr>
                <th style="width: 20%">MONDAY<br><?php echo $mondaydate; ?></th>
                <th style="width: 20%">TUESDAY<br><?php echo $tuesdaydate; ?></th>
                <th style="width: 20%">WEDNESDAY<br><?php echo $wednesdaydate; ?></th>
                <th style="width: 20%">THURSDAY<br><?php echo $thursdaydate; ?></th>
                <th style="width: 20%">FRIDAY<br><?php echo $fridaydate; ?></th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <?php 
                    foreach ($datearray as $day){ 
                        echo "<td class='entry' data-date='" . date('Y-m-d',strtotime($day)) . "'>";
                        echo "<button class='btn btn-xs btn-primary pull-right add-entry-btn' data-delivery-date='$day'>+</button>";
                        $deliveryquery = "SELECT tblDelivery.DeliveryID, tblJob.JobAddress, tblJob.JobID, tblDelivery.Description, tblDelivery.DeliveryType FROM tblJob RIGHT JOIN tblDelivery ON tblJob.JobID = tblDelivery.JobID WHERE DeliveryDate = '" . date('Y-m-d',strtotime($day)) . "' ORDER BY SortOrder";
                        $deliveryresult = $mysqli->query($deliveryquery);
                        
                        while($deliveryrow = $deliveryresult->fetch_array())
                        {
                            if($deliveryrow['DeliveryType']==1)
                            {
                                if (!empty($deliveryrow['JobID']))
                                {
                                    //check assembly completed
                                    $statusquery = "SELECT SUM(Weight) As SumWeight FROM tblJobTask INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID WHERE JobID = " . $deliveryrow['JobID'] . " AND tblJobTask.DateCompleted IS NOT NULL";
                                    $statusresult = $mysqli->query($statusquery);
                                    $statusrow = $statusresult->fetch_array();

                                    $incompletequery = "SELECT Count(JobID) As JobCount FROM tblJobTask WHERE JobID = " . $deliveryrow['JobID'] . " AND tblJobTask.DateCompleted IS NULL";
                                    $incompleteresult = $mysqli->query($incompletequery);
                                    $incompleterow = $incompleteresult->fetch_array();
                                    
                                    if ($statusrow['SumWeight'] < 1 || $incompleterow['JobCount'] > 0)
                                        $alertstring = "<span class='fa fa-1x fa-warning text-danger'></span>";
                                    else   
                                        $alertstring = "<span class='fa fa-1x fa-check text-success'></span>";

                                    echo "<div class='alert alert-warning calendar-entry' data-action='edit' data-delivery-id='" . $deliveryrow['DeliveryID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $deliveryrow['DeliveryID'] . "'><span aria-hidden='true'>&times;</span></button><a href='../job.php?jobid=".$deliveryrow['JobID']."#installer' target='_blank>" . $alertstring . " " . $deliveryrow['JobAddress'] . "</a></div>";
                                }
                                else
                                {
                                    echo "<div class='alert alert-warning calendar-entry' data-action='edit' data-delivery-id='" . $deliveryrow['DeliveryID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $deliveryrow['DeliveryID'] . "'><span aria-hidden='true'>&times;</span></button>" . $deliveryrow['Description'] . "</div>";                                    
                                }  
                            }
                            else
                            {
                                echo "<div class='alert alert-warning staff-delivery-calendar-edit' data-action='edit' data-delivery-id='" . $deliveryrow['DeliveryID'] . "'><button type='button' class='close delete-delivery-staff-btn' aria-label='Close' value='" . $deliveryrow['DeliveryID'] . "'><span aria-hidden='true'>&times;</span></button>" . $deliveryrow['Description'] . "</div>";      
                            }                         
                        }
                        
                        echo "</td>";              
                ?>

                <?php } ?>
            
            </tr>

        </tbody>

    </table>
    
</div>


<button class='btn btn-primary week-btn' value="<?php echo date('d-m-Y',strtotime("monday last week", strtotime($weekstart))) ?>">
    <span aria-hidden="true">&laquo;</span> Previous Week
</button>

<button class='btn btn-primary week-btn' value="<?php echo date('d-m-Y',strtotime("monday next week", strtotime($weekstart))) ?>">
     Next Week <span aria-hidden="true">&raquo;</span>
</button>

