<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

	define  ( 'AUDIT_LOAD', true );
	include ( dirname(__FILE__).'/includes/audit.php' );

		//Get the URL for the Month if in use
		$geturl = '';
		if (!empty($_GET['month']) && is_aproveed_month($_GET['month'])) {
			$geturl = '&month='.$_GET['month'];
		}

		//////////////////////////////

	//Here we get all the database
		//We get the last weight of the day "DESC"'
		$day_month_weight = $actual_date->format("d")." ".$actual_date->format("M");
		$today_weight = get_pm('all', 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Weight" AND pm_query_date LIKE "%'.$day_month_weight.'%" ORDER BY pm_position DESC LIMIT 1');
		$today_weight = $today_weight[0];

		//Get todays meals
		$today_meals = get_pm('all', 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Meal" AND pm_body_3 LIKE "%'.$actual_date->format("D").'%" ORDER BY pm_position ASC');

		//Get todays workouts
		$today_workouts = get_pm('all', 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Workouts" AND pm_body_3 LIKE "%'.$actual_date->format("D").'%" ORDER BY pm_position ASC');
		$today_workouts_count = get_pm('count', 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Workouts" AND pm_body_3 LIKE "%'.$actual_date->format("D").'%" ORDER BY pm_position ASC');

	// Proccess all form submitions
	if ( isset($_POST['submit']) ) {
		$audit_body_1 = '';
		$audit_body_2 = '';
		$audit_body_3 = '';
		$audit_body_4 = '';
		$audit_body_5 = '';
		$audit_body_6 = '';

		// We reverse the order of the POST array because they always put the last one dba_firstkey
		// So we can organize th inner values
		$_POST = array_reverse($_POST);

		foreach ( $_POST as $post_name => $value ) {

			//form name which contains the data; format is "did_TIME_TASKID"
			$post_name = trim($post_name);
			$data = explode("_", $post_name);

				// We get the hour number
				$time = $data[1];

				// We get the task ID
				$taskid = $data[2];

				if ( !empty($audit[$time][$taskid]) ) {
						$value_child[$time] = $value_child[$time].trim($audit[$time][$taskid]).',';
				}
		}

		// We reverse the order of the POST array to get it from 00--24
		ksort($value_child);

		//We put them together here
		foreach( $value_child as $name => $value ) {
			// Remove the last comma
			$value = substr($value, 0, -1);

			$audit_body_1 = $audit_body_1.'task_time_'.$name.'='.$value.';';
		}

		//Here we clean the last character to see if it is a ';'
		$audit_body_1 = substr($audit_body_1, 0, -1);
		$audit_body_3 = substr($audit_body_3, 0, -1);
		$audit_body_4 = substr($audit_body_4, 0, -1);

		if ( new_audit ( 'DailyAudit', '', '', $audit_body_1, $audit_body_2, $audit_body_3, $audit_body_4, $audit_body_5, $audit_body_6 ) ) {
			$alert['content'] = 'Great! Your daily audit was submited.';
			$alert['type'] = 'success';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	//Theme Header Configuration
	$array = array (
		'title' => 'Daily Audit',
		'extra' => '',

		'favicon_folder' => 'audit',
		'mask-icon-color' => '#5bbad5',
		'theme-color' => '#ffffff',
		'TileColor' => '#2d89ef',
	);

	include ( dirname(__FILE__).'/includes/themes/_header.php' );

	$today_date = trim($actual_date->format('d').' '.$actual_date->format('M').' '.$actual_date->format('Y'));

?>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
		<!-- Start content -->
		<div class="content">
				<div class="container-fluid">

						<div class="row">
								<div class="col-xl-12">
										<div class="page-title-box">

												<?php
													if (!empty($_GET['q']) && $_GET['q']=='logs'){
												?>
													<h4 class="page-title float-left">Daily Audits Logs for <?php

														if (!empty($_GET['month']) && is_aproveed_month($_GET['month']) ) {

															echo is_aproveed_month($_GET['month']);

														}
														else {

															echo is_aproveed_month($actual_date->format('M'));

														}
													?></h4>

												<?php
													}
													else {
												 ?>
												<h4 class="page-title float-left">Daily Audits</h4>
											<?php } ?>

												<ol class="breadcrumb float-right">
														<li class="breadcrumb-item"><a href="#">SelfMan</a></li>
														<li class="breadcrumb-item"><a href="#">Audits</a></li>
														<li class="breadcrumb-item active">Daily Audit</li>
												</ol>

												<div class="clearfix"></div>
										</div>
								</div>
						</div>
						<!-- end row -->

						<?php
							//The Standard Small Description Required per Section
							small_desc('DailyAudit',
								'In this section you will track your daily performance, hour by hour.'
							);
						?>

						<?php
							if ( empty($user['notification_daily']) or  $user['notification_daily'] == 'OFF' ) {
						?>
							<div class="alert alert-danger alert-white alert-dismissible fade show" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                        <span aria-hidden="true">&times;</span>
			                    </button>
			                    Your Daily Audit isn't set up yet. To set it up, <a href="/setup.php?q=daily">click here</a>.
			                    </div>
						<?php
							}
						?>

						<?php
							//If there is no request to view Daily audit logs, we ask for todays audit
							if( empty($_GET['q']) or !empty($_GET['q']) && $_GET['q'] != 'logs' ) {
						?>

						<div class="row">
							<div class="col-12">
			                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important;">
			                            <a class="dropdown-item" href="?q=logs"><h6 class="dropdown-header">Check your Accountability Logs</h6></a>
			                        </div>
							</div>
						</div>

						<div class="row">
								<div class="col-12">
										<div class="card-box">

			<?php
				if( get_audit('count', 'WHERE id_user = '.$user['id_user'].' AND audit_query_date LIKE "%'.$today_date.'%"') != 0 ) {
			?>

					<div class="alert alert-warning alert-dismissible fade show" role="alert" style="color: #000;">
						You already performed you audit for the day. The next one will be at the end of tomorrows day.
					</div>

			<?php
				}
				else {
			?>
			<div class="container">
				<form action="/audit-daily.php" data-parsley-validate novalidate method="POST">
				<br />

            <div>

                <div id="step-3" class="">
					<br />
																					<h2>Time Audit</h2>

																						<br />

																					<div class="alert alert-info alert-dismissible fade show" role="alert" style="color: #000;">
																						We are going to audit hour by hour, to verify what you really performed today. Select ONLY the ones you performed.
																					</div>

																						<br />

																					<?php
																						for ($i = 0; $i <= 23; $i++) {

																							//Fix $i for relating it to time management
																							if ( strlen($i)!=2 ) {
																								$i = '0'.$i;
																							}

																							//Get the list of preset task
																							$list_task = '<ol style="list-style-type:none">';

																							if ( !empty($audit[$i]) ) {

																								//We fix the order of the array because is on array_reverse
																								$audit[$i] = array_reverse($audit[$i]);

																								foreach ($audit[$i] as $id => $value) {
																									$list_task = $list_task.' <li>

																									<input type="checkbox" class="form-check-input" name="did_'.$i.'_'.$id.'">

																									'.$value.'</li> ';
																								}
																							}
																							$list_task = $list_task.'</ol>';

																							//We clean the html to see if there isnt a preset tag really
																							$check_list = clean_string(trim($list_task));
																							$check_list = trim($check_list);

																							//If there isnt any present task at the time, we skip int
																							if($check_list == ''){continue;}
																					?>

																							<p>

																								<h5>TIME: <b><?php echo readeable_time($i.':00'); ?></b></h5>

																								<?php if($check_list != ''){ echo $list_task; } else { echo 'N/A'; } ?>

																							<?php
																								if ( $i != 23 ) {
																							?>
																								<br />
																							<div class="progress progress-xs m-b-20">
																								<div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
																							</div>
																								<br />
																							<?php
																								}
																							?>


																					<?php
																						}
																					?>

                                           <div class="form-group text-right m-b-0">

												<button class="btn btn-primary waves-effect waves-light" name="submit" type="submit">Submit Audit</button>

                                            </div>
                </div>
                <!--<div id="step-4" class="">
                  <h2>Step 4 Content</h2>
                  <div class="card">
                      <div class="card-header">My Details</div>
                      <div class="card-block p-0">
                        <table class="table">
                            <tbody>
                                <tr> <th>Name:</th> <td>Tim Smith</td> </tr>
                                <tr> <th>Email:</th> <td>example@example.com</td> </tr>
                            </tbody>
                        </table>
                      </div>
                  </div>
                </div>-->
            </div>
		</form>
	</div>
	<?php
		}
	?>


										</div>
								</div><!-- end col-->

						</div>
						<!-- end row -->

						<?php
							}

							//here we show the logs
							elseif( $_GET['q'] == 'logs' ) {

						?>

						<div class="row">

				<div class="col-12">

						<?php
							if ( !empty($_GET['view']) && get_audit($_GET['view'])['id_user'] == $user['id_user'] && get_audit($_GET['view'])['audit_type'] == 'DailyAudit' ) {

								$audit = get_audit($_GET['view']);
						?>

                        <div class="dropdown-menu dropdown-example pull-right " style="margin-bottom: 20px !important; margin-left: 20px !important;">
                            <a class="dropdown-item" href="/audit-daily.php?q=logs<?php echo $geturl; ?>"><h6 class="dropdown-header">Go Back to Logs</h6></a>
                        </div>

				</div><!-- THIS DIV ONLY WORK WHILE VIEW IS ACTIVE -->

				<div class="col-12"><!-- THIS DIV ONLY WORK WHILE VIEW IS ACTIVE -->

                                <!--<div class="row">-->
                                    <!--<div class="col-md-6">-->
                                        <div class="card m-b-20 text-center">
                                            <div class="card-header">
                                                <ul class="nav nav-tabs card-header-tabs">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" href="#"><?php echo $audit['audit_query_date']; ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">


																								<div class="progress progress-xs m-b-20">
																									<div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
																								</div>
																									<br />

																									<?php
																										$log_time_management = explode(";", $audit['audit_body_1']);

																										foreach( $log_time_management as $key => $value ) {

																											//Extract the real valuable data
																											$rd = explode("=", $value);

																											//We get the Number
																											$i = str_replace('task_time_', '', $rd[0]);

																											$rd = trim($rd[1]);

																											//Now we get each task that are separated by a ","
																											$performed = explode(",", $rd);

																											//Fix $i for relating it to time management
																											if ( strlen($i)!=2 ) {
																												$i = '0'.$i;
																											}

																											//Get the list of preset task
																											$list_task = '<ol>';

																												foreach ($performed as $id => $value) {
																													$list_task = $list_task.' <li> '.trim($value).'</li> ';
																												}

																											$list_task = $list_task.'</ol>';
																									?>

																											<p>

																												<h5>TIME: <b><?php echo readeable_time($i.':00'); ?></b></h5>

																												<?php echo $list_task; ?>


																											</p>

																											<?php
																												if ( $i != 23 ) {
																											?>
																												<br />
																											<div class="progress progress-xs m-b-20">
																												<div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
																											</div>
																												<br />
																											<?php
																												}


																										}
																									 ?>
												</p>
                                            </div>
                                        </div>
                                    <!--</div>-->
								<!--</div>-->

						<?php
							}
							else {

								//Here we establish the query to the database
									//Get the Specific Month
									if (!empty($_GET['month']) && is_aproveed_month($_GET['month']) ) {
										$extraquery = substr($_GET['month'], 0, 3).' '.$actual_date->format('Y');
									}
									else {
										$extraquery = $actual_date->format('M').' '.$actual_date->format('Y');
									}

									//Here we establish the query to the database
										$query = 'WHERE id_user = '.$user['id_user'].' AND audit_type = "DailyAudit" AND audit_query_date LIKE "%'.$extraquery.'%"  ORDER BY audit_position ASC';

										//We verify if there is a as in that section
										$count_query = get_audit('count', $query);

									//We verify if there is a lb in that section
									if( $count_query == 0 ) {
										$alert['content'] = 'There was no logs during '.$extraquery;
										$alert['type'] = 'info';
									}
						?>


                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important; margin-left: 20px !important;">
                            <a class="dropdown-item" href="/audit-daily.php"><h6 class="dropdown-header">Go Back</h6></a>
                        </div>
				</div>

				<div class="col-12" style="margin-bottom: 20px !important;">
									 <div class="btn-group dropdown pull-right">
											 <button type="button" class="btn btn-primary waves-effect waves-light">Select a Month</button>
											 <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>

											 <div class="dropdown-menu">
						 <a href="?q=logs&month=Jan" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Jan' or !empty($_GET['month']) && $_GET['month'] == 'Jan') { echo ' active'; } ?>">January</a>
						 <a href="?q=logs&month=Feb" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Feb' or !empty($_GET['month']) && $_GET['month'] == 'Feb') { echo ' active'; } ?>">February</a>
						 <a href="?q=logs&month=Mar" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Mar' or !empty($_GET['month']) && $_GET['month'] == 'Mar') { echo ' active'; } ?>">March</a>
						 <a href="?q=logs&month=Apr" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Apr' or !empty($_GET['month']) && $_GET['month'] == 'Apr') { echo ' active'; } ?>">April</a>
						 <a href="?q=logs&month=May" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'May' or !empty($_GET['month']) && $_GET['month'] == 'May') { echo ' active'; } ?>">May</a>
						 <a href="?q=logs&month=Jun" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Jun' or !empty($_GET['month']) && $_GET['month'] == 'Jun') { echo ' active'; } ?>">June</a>
						 <a href="?q=logs&month=Jul" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Jul' or !empty($_GET['month']) && $_GET['month'] == 'Jul') { echo ' active'; } ?>">July</a>
						 <a href="?q=logs&month=Aug" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Aug' or !empty($_GET['month']) && $_GET['month'] == 'Aug') { echo ' active'; } ?>">August</a>
						 <a href="?q=logs&month=Sep" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Sep' or !empty($_GET['month']) && $_GET['month'] == 'Sep') { echo ' active'; } ?>">September</a>
						 <a href="?q=logs&month=Oct" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Oct' or !empty($_GET['month']) && $_GET['month'] == 'Oct') { echo ' active'; } ?>">October</a>
						 <a href="?q=logs&month=Nov" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Nov' or !empty($_GET['month']) && $_GET['month'] == 'Nov') { echo ' active'; } ?>">November</a>
						 <a href="?q=logs&month=Dec" class="dropdown-item<?php if(empty($_GET['month']) && $actual_date->format('M') == 'Dec' or !empty($_GET['month']) && $_GET['month'] == 'Dec') { echo ' active'; } ?>">December</a>
											 </div>
									 </div>
			 </div>

				<div class="col-12">

                        <table class="table table-striped">
                            <thead class="thead-light">
                                <tr>
									<th>#</th>
                                    <th></th>

                                </tr>
                            </thead>
							<tbody id="sortable_rows">
					<?php
						//$i is used to get the count of and order
						$i = 1;
						foreach ( get_audit('all', $query) as $id => $value ) {
					?>
                                        <tr plid="<?php echo $value['audit_position']; ?>" pmid="<?php echo $value['id_audit']; ?>" ds="au">
                                            <th scope="row"><?php echo $i; ?></th>
                                            <td><a href="?q=logs<?php echo $geturl; ?>&view=<?php echo $value['id_audit']; ?>"><?php echo $value['audit_query_date']; ?></a></td>
                                        </tr>
					<?php
							$i++;
						}
					?>

                            </tbody>
                        </table>

                        <p class="text-muted m-b-15 font-13">
                            **<b>Click the name to see the description.</b> You can move around the log, by dragging and dropping them. This will change the order in which they are displayed.
                        </p>

												<?php
													}
												?>
						                </div>
						            </div>
						<?php
							} //End logs
						?>

				</div> <!-- container -->

		</div> <!-- content -->



</div>
<!-- End content-page -->


<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->


<?php
	include ( dirname(__FILE__).'/includes/themes/_footer.php' );
?>
