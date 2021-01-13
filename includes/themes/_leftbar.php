<?php
	if ( !defined('THEME_LOAD') ) { die ( header('Location: /404') ); }
?>
<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
                <li class="text-muted menu-title">Navigation</li>

                <!--<li class="has_sub">
                    <a href="/index.php" class="waves-effect"><i class="zmdi zmdi-view-dashboard"></i><span> Dashboard </span> </a>
                </li>-->

								<li class="has_sub">
                    <a href="/all-tasks.php" class="waves-effect"><i class="zmdi zmdi-calendar"></i> <span> Tasks </span> </a>
                    <!--<ul class="list-unstyled">
                        <li<?php if (!empty($_GET['day']) && $_GET['day'] == 'Yea') { echo ' class="menu-inactive"'; } ?>><a href="/all-tasks.php">Weekly</a></li>
												<li<?php if (!empty($_GET['day']) && $_GET['day'] == 'Yea') { echo ' class="active"'; } ?>><a href="/all-tasks.php?day=Yea">Yearly</a></li>
                    </ul>-->
                </li>

								<li class="has_sub">
                    <a href="/audit-daily.php" class="waves-effect"><i class="zmdi zmdi-assignment-alert"></i> <span> Accountability </span> </a>
                    <!--<ul class="list-unstyled">
												<li><a href="/audit-networth.php">Networth</a></li>
                        <li><a href="/audit-daily.php">Accountability</a></li>
                        <!--<li><a href="/audit-finance.php">Finance Management</a></li>-->
                        <!--<li><a href="/personal-workout.php">Workout Training</a></li>
                        <li><a href="/components-sweet-alert.php">Meal Plan & Recipies</a></li>
                        <li><a href="/personal-audint.php">Audit</a></li>-->
                    <!--</ul>-->
                </li>



								<?php
									if( is_login_user()['privilege'] == 2657 ) {
								 ?>
								<!--<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-case"></i> <span> Planning </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
											<li><a href="/budgets.php">Budgeting</a></li>
                    </ul>
                </li>-->


                <!--<li class="has_sub">
                    <a href="/personal-affirmations.php" class="waves-effect"><i class="zmdi zmdi-filter-list"></i> <span> Afirmations </span></a>-->
                    <!--<ul class="list-unstyled">

											<li><a href="/personal-affirmations.php">Afirmations</a></li>

												<li><a href="/recipeslist.php">Food Recipes/Lists</a></li>
                        <li><a href="/readinglist.php">Reading List</a></li>
												<li><a href="/devices-insured.php">Devices Insured</a></li>

                    </ul>-->
                </li>

<!--
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-account-box"></i> <span> Fitness </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="/personal-overview.php">Overview</a></li>

												<li><a href="/personal-meal.php">Meal Plan</a></li>
                        <li><a href="/personal-workouts.php">Workout Plan</a></li>
                        <li><a href="/personal-weight.php">Weight Tracking</a></li>
                    </ul>
                </li>-->
<!--
								<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-hourglass-alt"></i> <span> Life Goals </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
												<li<?php if (!empty($_GET['q']) && $_GET['q'] == 'long') { echo ' class="menu-inactive"'; } ?>><a href="/goals.php">Short Term</a></li>
												<li<?php if (!empty($_GET['q']) && $_GET['q'] == 'long') { echo ' class="active"'; } ?>><a href="/goals.php?q=long">Long Term</a></li>
                    </ul>
                </li>-->

                <!--<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-money-box"></i> <span> Liabilities </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="/expenses.php">Expenses</a></li>
                        <li><a href="/recurring-expenses.php">Recurring Expenses</a></li>
												<li><a href="/insurance.php">Insurance</a></li>
												<li><a href="/creditcards.php">Credit Cards</a></li>
												<li><a href="/loans-management.php">Loans Management</a></li>
                    </ul>
                </li>-->
								<?php
									}
								 ?>

                <!--<li class="has_sub">
                    <a href="calendar.php" class="waves-effect"><i class="zmdi zmdi-calendar"></i><span> Calendar </span> </a>
                </li>-->

                <!--<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-widgets"></i> <span> Widgets </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="widgets-tiles.php">Tile Box</a></li>
                        <li><a href="widgets-charts.php">Chart Widgets</a></li>
                    </ul>
                </li>-->

                <!--<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-fire"></i> <span> Icons </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="icons-materialdesign.php">Material Design</a></li>
                        <li><a href="icons-ionicons.php">Ion Icons</a></li>
                        <li><a href="icons-fontawesome.php">Font awesome</a></li>
                        <li><a href="icons-themify.php">Themify Icons</a></li>
                        <li><a href="icons-simple-line.php">Simple line Icons</a></li>
                        <li><a href="icons-weather.php">Weather Icons</a></li>
                        <li><a href="icons-pe7.php">PE7 Icons</a></li>
                        <li><a href="icons-typicons.php">Typicons</a></li>
                    </ul>
                </li>-->

                <!--<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><span class="label label-pill label-warning float-right">8</span><i class="zmdi zmdi-collection-text"></i><span> Forms </span> </a>
                    <ul class="list-unstyled">
                        <li><a href="form-elements.php">General Elements</a></li>
                        <li><a href="form-advanced.php">Advanced Form</a></li>
                        <li><a href="form-validation.php">Form Validation</a></li>
                        <li><a href="form-pickers.php">Form Pickers</a></li>
                        <li><a href="form-wizard.php">Form Wizard</a></li>
                        <li><a href="form-mask.php">Form Masks</a></li>
                        <li><a href="form-uploads.php">Multiple File Upload</a></li>
                        <li><a href="form-xeditable.php">X-editable</a></li>
                    </ul>
                </li>-->

                <!--<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-format-list-bulleted"></i> <span> Tables </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="tables-basic.php">Basic Tables</a></li>
                        <li><a href="tables-datatable.php">Data Table</a></li>
                        <li><a href="tables-responsive.php">Responsive Table</a></li>
                        <li><a href="tables-tablesaw.php">Tablesaw</a></li>
                    </ul>
                </li>-->

                <!--<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-chart"></i><span> Charts </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="chart-flot.php">Flot Chart</a></li>
                        <li><a href="chart-morris.php">Morris Chart</a></li>
                        <li><a href="chart-chartjs.php">Chartjs</a></li>
                        <li><a href="chart-peity.php">Peity Charts</a></li>
                        <li><a href="chart-chartist.php">Chartist Charts</a></li>
                        <li><a href="chart-c3.php">C3 Charts</a></li>
                        <li><a href="chart-sparkline.php">Sparkline charts</a></li>
                        <li><a href="chart-knob.php">Jquery Knob</a></li>
                    </ul>
                </li>-->

                <!--<li class="text-muted menu-title">More</li>

                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><span class="label label-success label-pill float-right">13</span><i class="zmdi zmdi-collection-item"></i><span> Pages </span></a>
                    <ul class="list-unstyled">
                        <li><a href="pages-starter.php">Starter Page</a></li>
                        <li><a href="pages-login.php">Login</a></li>
                        <li><a href="pages-register.php">Register</a></li>
                        <li><a href="pages-recoverpw.php">Recover Password</a></li>
                        <li><a href="pages-lock-screen.php">Lock Screen</a></li>
                        <li><a href="pages-404.php">Error 404</a></li>
                        <li><a href="pages-500.php">Error 500</a></li>
                        <li><a href="pages-timeline.php">Timeline</a></li>
                        <li><a href="pages-invoice.php">Invoice</a></li>
                        <li><a href="pages-pricing.php">Pricing</a></li>
                        <li><a href="pages-gallery.php">Gallery</a></li>
                        <li><a href="pages-maintenance.php">Maintenance</a></li>
                        <li><a href="pages-comingsoon.php">Coming Soon</a></li>
                    </ul>
                </li>-->

                <!--<li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-blur-linear"></i><span>Multi Level </span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><span>Menu Level 1.1</span>  <span class="menu-arrow"></span>    </a>
                            <ul style="">
                                <li><a href="javascript:void(0);"><span>Menu Item</span></a></li>
                                <li><a href="javascript:void(0);"><span>Menu Item</span></a></li>
                                <li><a href="javascript:void(0);"><span>Menu Item</span></a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0);"><span>Menu Level 1.2</span></a>
                        </li>
                    </ul>
                </li>-->

            </ul>
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>

</div>
<!-- Left Sidebar End -->
