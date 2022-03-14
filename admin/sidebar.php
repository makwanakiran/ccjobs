<div class="span3">
                        <div class="sidebar">
                            <ul class="widget widget-menu unstyled">
                                <li <?php echo ($page == "welcome") ? "class='active'" : ""; ?>><a href="index.php"><i class="menu-icon icon-dashboard"></i>Dashboard
                                </a></li>
                                <li <?php echo ($page == "projects") ? "class='active'" : ""; ?>><a href="projects.php"><i class="menu-icon icon-bullhorn"></i>Project</a>
                                </li>
                                <li <?php echo ($page == "jobs") ? "class='active'" : ""; ?>><a href="jobs.php"><i class="menu-icon icon-bullhorn"></i>Jobs</a>
                                </li>

                            </ul>
                            <ul class="widget widget-menu unstyled">
                                <li <?php echo ($page == "schedule") ? "class='active'" : ""; ?>><a class="collapsed " data-toggle="collapse" href="#togglePages"><i class="menu-icon icon-book">
                                </i><i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right">
                                </i>Schedule </a>
                                    <ul id="togglePages" class="collapse <?php echo ($page == "schedule") ? 'in' : ""; ?> unstyled">
                                        <li><a href="schedule.php">Installers</a></li>
                                        <li><a href="delivery.php">Delivery</a></li>
                                        <li><a href="drawers.php">Draftsman</a></li>
                                        <li><a href="stone.php">Stone</a></li>
                                        <li><a href="assemblers.php">Assemblers</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <!--/.widget-nav-->
                            <ul class="widget widget-menu unstyled">
                                <li <?php echo ($page == "reports") ? "class='active'" : ""; ?> >
                                    <a  class="collapsed " data-toggle="collapse" href="#toggleReport"><i class="menu-icon icon-paste">
                                </i><i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right">
                                </i>Reports</a>
                                    <ul id="toggleReport" class="collapse <?php echo ($page == "reports") ? 'in' : ""; ?> unstyled">
                                        <li><a href="reports-draftsmen.php">Draftsmen</a></li>
                                        <li><a href="reports-cnc.php">CNC</a></li>
                                        <li><a href="reports-edging.php">Edging</a></li>
                                        <li><a href="reports-assemblers.php">Assemblers</a></li>
                                        <li><a href="reports-installers.php">Installers</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="widget widget-menu unstyled">
                                <li <?php echo ($page == "users") ? "class='active'" : ""; ?>><a href="users.php"><i class="menu-icon icon-user"></i>Users</a></li>
                                <li <?php echo ($page == "adminusers") ? "class='active'" : ""; ?>><a href="admin_users.php"><i class="menu-icon icon-book"></i>Admin Users</a></li>
                            </ul>
                            <!--/.widget-nav-->

                            <!--/.widget-nav-->
                            <ul class="widget widget-menu unstyled">
                                <li><a href="logout.php"><i class="menu-icon icon-signout"></i>Logout </a></li>
                            </ul>
                        </div>
                        <!--/.sidebar-->
                    </div>
                    <!--/.span3-->