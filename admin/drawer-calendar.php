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
                <!-- <th style="width: 15%">Draftsman</th> -->
                <th style="width: 17%">MONDAY<br><?php echo $mondaydate; ?></th>
                <th style="width: 17%">TUESDAY<br><?php echo $tuesdaydate; ?></th>
                <th style="width: 17%">WEDNESDAY<br><?php echo $wednesdaydate; ?></th>
                <th style="width: 17%">THURSDAY<br><?php echo $thursdaydate; ?></th>
                <th style="width: 17%">FRIDAY<br><?php echo $fridaydate; ?></th>
            </tr>
        </thead>

        <tbody>
        <?php 
            $query = "SELECT UserID, FullName FROM tblUser WHERE IsDraftsman <> 0 and IsForeman = 0 ORDER BY FullName";
            $result = $mysqli->query($query);
            
            while($row = $result->fetch_array()){
            $name_set=1;
            ?>
                <tr>
                    <!-- <td><strong><?php echo $row['FullName'] ?></strong></td> -->
                    <?php 
                        foreach ($datearray as $day)
                        { 
                            if($name_set==1)
                            {
                                echo "<td class='entry' data-user-id='" . $row['UserID'] . "' data-date='" . date('Y-m-d',strtotime($day)) . "'><div class='alert alert-warning name-bg-custom calendar-entry'><strong>".$row['FullName']."</strong></div>";
                                $name_set++;
                            }
                            else
                            {
                                echo "<td class='entry' data-user-id='" . $row['UserID'] . "' data-date='" . date('Y-m-d',strtotime($day)) . "'>";
                            }    

                            /*echo "<td class='entry' data-user-id='" . $row['UserID'] . "' data-date='" . date('Y-m-d',strtotime($day)) . "'>";*/
                            echo "<button class='btn btn-xs btn-primary pull-right add-entry-btn' value='" . $row['UserID'] . "' data-schedule-date='$day'>+</button>";
                            $schedulequery = "SELECT tblDrawerSchedule.DrawerScheduleID, tblJob.JobAddress, tblJob.JobID, tblDrawerSchedule.Description, tblDrawerSchedule.DrawerType FROM tblJob RIGHT JOIN tblDrawerSchedule ON tblJob.JobID = tblDrawerSchedule.JobID WHERE ScheduleDate = '" . date('Y-m-d',strtotime($day)) . "' AND UserID = '" . $row['UserID'] ."' ORDER BY SortOrder";
                            $scheduleresult = $mysqli->query($schedulequery);
                            
                            while($schedulerow = $scheduleresult->fetch_array())
                            {
                                if($schedulerow['DrawerType'] == 1)
                                {
                                    if (!empty($schedulerow['JobID']))
                                    {
                                        echo "<div class='alert alert-warning calendar-entry' data-action='edit' data-schedule-id='" . $schedulerow['DrawerScheduleID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $schedulerow['DrawerScheduleID'] . "'><span aria-hidden='true'>&times;</span></button><a href='../job.php?jobid=".$schedulerow['JobID']."#draftsman' target='_blank'>".$schedulerow['JobAddress'] . "</a></div>";                                    
                                    }
                                    else
                                    {
                                        echo "<div class='alert alert-warning calendar-entry' data-action='edit' data-schedule-id='" . $schedulerow['DrawerScheduleID'] . "'><button type='button' class='close delete-btn' aria-label='Close' value='" . $schedulerow['DrawerScheduleID'] . "'><span aria-hidden='true'>&times;</span></button>" . $schedulerow['Description'] . "</div>";                                    
                                    }
                                }
                                else
                                {
                                    echo "<div class='alert alert-warning staff-drawers-calendar-edit' data-action='delete' data-drawer-id='" . $schedulerow['DrawerScheduleID'] . "'><button type='button' class='close delete-drawer-staff-btn' aria-label='Close' value='" . $schedulerow['DrawerScheduleID'] . "'><span aria-hidden='true'>&times;</span></button>" . $schedulerow['Description'] . "</div>"; 
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

