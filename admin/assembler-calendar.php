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
$saturdaydate = date('d-m-Y',strtotime("saturday this week", strtotime($weekstart)));

$datearray = array($mondaydate, $tuesdaydate, $wednesdaydate, $thursdaydate, $fridaydate, $saturdaydate);

echo "<h3>WEEK " . $mondaydate . " to " . $saturdaydate . "</h3>";

?>

<style type="text/css">

    #caltable {
        font-size: 12px;;
    }
    .calendar-entry{
        padding: 5px;
        cursor: pointer;
        margin-bottom: 10px;
    }

    .entry{
        vertical-align: text-top !important;
        height: 60px;
        position: relative;
        padding-top: 30px !important;
    }

    .entry .add-entry-btn { display:none;}
    .entry:hover .add-entry-btn { display:inline-block;}

    .add-entry-btn{
        position: absolute;
        top: 5px;
        right: 8px;
    }
</style>

<div class="table-responsive">

    <table id="caltable" class="table table-bordered table-striped bg-info shaded-icon">
        <thead>
            <tr>
                <!-- <th style="width: 16%">Installer</th> -->
                <th style="width: 14%">MON <?php echo $mondaydate; ?></th>
                <th style="width: 14%">TUE <?php echo $tuesdaydate; ?></th>
                <th style="width: 14%">WED <?php echo $wednesdaydate; ?></th>
                <th style="width: 14%">THU <?php echo $thursdaydate; ?></th>
                <th style="width: 14%">FRI <?php echo $fridaydate; ?></th>
                <th style="width: 14%">SAT <?php echo $saturdaydate; ?></th>
            </tr>
        </thead>

        <tbody>
        <?php 
            $query = "SELECT UserID, FullName FROM tblUser WHERE IsAssembler <> 0 and IsForeman = 0 ORDER BY FullName";
            $result = $mysqli->query($query);
            
            while($row = $result->fetch_array()){
            $name_set=1;
            ?>
                <tr>
                    <!-- <td><strong><?php echo $row['FullName'] ?></strong></td> -->
                    <?php 
                        foreach ($datearray as $day){ 
                            //echo "<td class='entry' data-user-id='" . $row['UserID'] . "' data-date='" . date('Y-m-d',strtotime($day)) . "'>";

                            if($name_set==1)
                            {
                                echo "<td class='entry' data-user-id='" . $row['UserID'] . "' data-date='" . date('Y-m-d',strtotime($day)) . "'><div class='alert alert-warning name-bg-custom calendar-entry'><strong>".$row['FullName']."</strong></div>";
                                $name_set++;
                            }
                            else
                            {
                                echo "<td class='entry' data-user-id='" . $row['UserID'] . "' data-date='" . date('Y-m-d',strtotime($day)) . "'>";
                            }    

                            echo "<button class='btn btn-xs btn-primary pull-right add-entry-btn' value='" . $row['UserID'] . "' data-schedule-date='$day'>+</button>";
                            $schedulequery = "SELECT tblAssemblerSchedule.AssemblerScheduleID, tblJob.JobAddress, tblJob.JobID, tblAssemblerSchedule.Description, tblAssemblerSchedule.AssemblerType FROM tblJob RIGHT JOIN tblAssemblerSchedule ON tblJob.JobID = tblAssemblerSchedule.JobID WHERE ScheduleDate = '" . date('Y-m-d',strtotime($day)) . "' AND UserID = '" . $row['UserID'] ."' ORDER BY SortOrder";
                            $scheduleresult = $mysqli->query($schedulequery);
                            
                            while($schedulerow = $scheduleresult->fetch_array())
                            {
                                if($schedulerow['AssemblerType']=='1')
                                {

                                    if (!empty($schedulerow['JobID'])){
                                        //check assembly completed
                                        $statusquery = "SELECT SUM(Weight) As SumWeight FROM tblJobTask INNER JOIN tblTask ON tblJobTask.TaskID = tblTask.TaskID WHERE JobID = " . $schedulerow['JobID'] . " AND tblJobTask.DateCompleted IS NOT NULL";
                                        $statusresult = $mysqli->query($statusquery);
                                        $statusrow = $statusresult->fetch_array();

                                        $incompletequery = "SELECT Count(JobID) As JobCount FROM tblJobTask WHERE JobID = " . $schedulerow['JobID']. " AND tblJobTask.DateCompleted IS NULL" ;
                                        $incompleteresult = $mysqli->query($incompletequery);
                                        $incompleterow = $incompleteresult->fetch_array();

                                        $Color_query = "SELECT  MissingItems as MissingItemsColor FROM tblJobTask WHERE JobID = " . $schedulerow['JobID'] ;
                                        $Color_result = $mysqli->query($Color_query);
                                        $Color_row = $Color_result->fetch_array();
                                        
                                        
                                        if ($statusrow['SumWeight'] < 1 || $incompleterow['JobCount'] > 0)
                                            $alertstring = "<span class='fa fa-1x fa-warning text-danger'></span>";
                                        else   
                                            $alertstring = "<span class='fa fa-1x fa-check text-success'></span>";

                                        if($Color_result == null)
                                        {
                                            if($Color_row['MissingItemsColor'] == null)
                                                $colorPart = 'alert-success';
                                            else
                                                $colorPart = 'alert-warning';
                                        }else
                                        {

                                                $colorPart = 'alert-success';
                                        }

                                        echo "<div class='alert ".$colorPart."  calendar-entry' data-action='edit' data-schedule-id='" . $schedulerow['AssemblerScheduleID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $schedulerow['AssemblerScheduleID'] . "'><span aria-hidden='true'>&times;</span></button><a href='../job.php?jobid=".$schedulerow['JobID']."#assembler' target='_blank>" . $alertstring . " " . $schedulerow['JobAddress'] . "</a></div>";
                                            
                                    }
                                    else
                                    {
                                        echo "<div class='alert alert-warning calendar-entry' data-action='edit' data-schedule-id='" . $schedulerow['AssemblerScheduleID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $schedulerow['AssemblerScheduleID'] . "'><span aria-hidden='true'>&times;</span></button>" . $schedulerow['Description'] . "</div>";                                    
                                    }
                                }
                                else
                                {
                                    echo "<div class='alert alert-warning assembler-staff-calendar-entry' data-action='edit' data-assembler-id='" . $schedulerow['AssemblerScheduleID'] . "'><button type='button' class='close assembler-staff-delete-btn' aria-label='Close' value='" . $schedulerow['AssemblerScheduleID'] . "'><span aria-hidden='true'>&times;</span></button>" . $schedulerow['Description'] . "</div>"; 
                                }    
                            
                            }
                            
                            echo "</td>";
                            
                    ?>

                    <?php } ?>
                
                </tr>
                
            <?php } ?>

        </tbody>

    </table>
    
</div>


<button class='btn btn-primary week-btn' value="<?php echo date('d-m-Y',strtotime("monday last week", strtotime($weekstart))) ?>">
    <span aria-hidden="true">&laquo;</span> Previous Week
</button>

<button class='btn btn-primary week-btn' value="<?php echo date('d-m-Y',strtotime("monday next week", strtotime($weekstart))) ?>">
     Next Week <span aria-hidden="true">&raquo;</span>
</button>

