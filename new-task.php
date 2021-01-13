<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

	//We get the data from the task if we are going to edit it & We also verify that the task to edit belongs to the user
	if ( !empty($_GET['edit']) && get_task($_GET['edit']) && get_task($_GET['edit'])['id_user'] == $user['id_user'] ) {
		$task = get_task($_GET['edit']);
	}

	// Proccess all form submitions
	if ( isset($_POST['submit']) ) {

		if ( empty($_POST['task_push']) ) {
			$_POST['task_push'] = 'false';
		}

		if ( empty($_POST['task_title']) ) {
			$alert['content'] = 'Please enter a title for the task.';
			$alert['type'] = 'error';
		}

		if ( empty($_POST['task']) ) {
			$alert['content'] = 'Please enter your task.';
			$alert['type'] = 'error';
		}

		//Here We convert the 12 hour to 24 hour so SQL can organize correctly
		$_POST['task_time'] = date("H:i", strtotime($_POST['task_time']));

		//Here we add a cero to the date if it only contains 1 number in the hour. Count will be "00:00"
		if ( empty($_POST['task_yearly_date']) && strlen($_POST['task_time'])<5  ) {
			$_POST['task_time'] = '0'.$_POST['task_time'];
		}

		//Here we collect all the days information and put in in a string
		$days = '';

			if ( !empty($_POST['Sun']) ) {
				$days = $days.'Sun, ';
			}

			if ( !empty($_POST['Mon']) ) {
				$days = $days.'Mon, ';
			}

			if ( !empty($_POST['Tue']) ) {
				$days = $days.'Tue, ';
			}

			if ( !empty($_POST['Wed']) ) {
				$days = $days.'Wed, ';
			}

			if ( !empty($_POST['Thu']) ) {
				$days = $days.'Thu, ';
			}

			if ( !empty($_POST['Fri']) ) {
				$days = $days.'Fri, ';
			}

			if ( !empty($_POST['Sat']) ) {
				$days = $days.'Sat';
			}

			//Trim Spaces
			$days = trim($days);

			//Remove the last "coma (,)" if there is one
			if( substr($days, -1) == ',' ) {
				$days = substr($days, 0, -1);
			}

		//Before proceeding, we verify if the task is DAILY or Yearly
		if ( empty($days) && !empty($_POST['task_yearly_date'])) {
			$_POST['task_time'] = $_POST['task_yearly_date'];

			if ( !empty($_POST['task_yearly_time']) ) {
				$_POST['task_time'] = $_POST['task_time'].' '.$_POST['task_yearly_time'];
			}

			//We remove the year of the date, because this are reccurent yearly task and non dependant on specific YEARS
			if ( strlen($_POST['task_time'])>5 ) {
				$_POST['task_time'] = substr($_POST['task_time'], 0, -5);
			}

			$days = 'Yea';
		}

		//Proccess Updates or Create new task
		if ( !empty($_POST['task_to_edit']) ) {
			update_task ( $_POST['task_to_edit'], 'task_title', trim($_POST['task_title']) );
			update_task ( $_POST['task_to_edit'], 'task', trim($_POST['task']) );
			update_task ( $_POST['task_to_edit'], 'task_desc', trim($_POST['task_desc']) );
			update_task ( $_POST['task_to_edit'], 'task_time', trim($_POST['task_time']) );
			update_task ( $_POST['task_to_edit'], 'task_weekdays', trim($days) );
			update_task ( $_POST['task_to_edit'], 'task_push', trim($_POST['task_push']) );

			$alert['content'] = 'Great! Your updates where made.';
			$alert['type'] = 'success';
		}
		else {
			if ( empty($alert['content']) && new_task ( $_POST['task_title'], $_POST['task'], $_POST['task_desc'], $_POST['task_time'], $days,  $_POST['task_push']) ) {
				$alert['content'] = 'Great! Your task was added.';
				$alert['type'] = 'success';
			}
			else {
				$alert['content'] = 'Oops! An error occurred, try again.';
				$alert['type'] = 'error';
			}
		}
	}

	//Theme Header Configuration
	$array = array (
		'title' => 'New Task',
		'extra' => '',
	);

	include ( dirname(__FILE__).'/includes/themes/_header.php' );

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
								if ( !empty($_GET['edit']) && get_task($_GET['edit']) ) {
							?>

								<h4 class="page-title float-left">Edit Task</h4>

							<?php
								}

								else {
							?>

								<h4 class="page-title float-left">New Task</h4>

							<?php
								}

										/**
										 * We create and easy access to go back to the daily task section that we were in
										 */

										 //We establish the long terms goals url
									 	$get_url = '';
									 	if(!empty($_GET['day']) && is_aproveed_day($_GET['day'])) {
									 		$get_url = '?day='.$_GET['day'];
									 	}
							?>

                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="#">SelfMan</a></li>
                                <li class="breadcrumb-item"><a href="/all-tasks.php<?php echo $get_url; ?>">Daily Task</a></li>

								<?php
									if ( !empty($_GET['edit']) && get_task($_GET['edit']) ) {
								?>
									<li class="breadcrumb-item active">Edit</li>
								<?php
									}

									else {
								?>
									<li class="breadcrumb-item active">New</li>
								<?php
									}
								?>
                            </ol>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-6">

									<?php
										if ( empty($_GET['edit']) or !empty($_GET['edit']) && !get_task($_GET['edit']) ) {
									?>
										<h4 class="header-title m-t-0">Fill The Form To Add a New Task</h4>
										<p class="text-muted font-13 m-b-10">
											The time zone for the task is aligned at GTM-4 (Puerto Rico Time Zone)
										</p>
									<?php
										}
									?>

									<form action="/new-task.php<?php echo $get_url; ?>" data-parsley-validate novalidate method="POST">

                                    <div class="p-20">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="userName">Task Title (or Category)<span class="text-danger">*</span></label>
                                                <input type="text" name="task_title" parsley-trigger="change" required
                                                        class="form-control"<?php if (!empty( $task['task_title'] )) { echo ' value="'.$task['task_title'].'"'; } ?>>
                                            </div>

												<br />

                                            <div class="form-group">
                                                <label for="userName">Task <span class="text-danger">*</span></label>
                                                <input type="text" name="task" parsley-trigger="change" required
                                                        class="form-control" <?php if (!empty( $task['task'] )) { echo ' value="'.$task['task'].'"'; } ?>>
                                            </div>

                                        <div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="remember-1" type="checkbox" name="task_push" value="true"<?php if (!empty($task['task_push']) && $task['task_push'] == 'false') { } else { echo ' checked'; }?>>
                                                            <label for="remember-1"> Send a notification at the time of the task </label>
                                                        </div>
                                                    </div>

                                                        <div class="m-t-20">
                                                            <p class="text-muted m-b-15 font-13">
                                                                Task Description (optional)
                                                            </p>
                                                            <textarea id="textarea" name="task_desc" class="form-control" maxlength="225" rows="3"><?php if (!empty( $task['task_desc'] )) { echo $task['task_desc']; } ?></textarea>
                                                        </div>

											<br />
												<br />

                                            <ul class="nav nav-tabs m-b-10" id="myTab" role="tablist">
												<?php
													//If this is a premade task, we show their proper tab
													if ( !empty($task['task_weekdays']) ) {

														//We only Show the daily tab to the daily task
														if ( substr_count($task['task_weekdays'], 'Yea') == 0 ) {
												?>
															<li class="nav-item">
																<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home"
																	role="tab" aria-controls="home">Daily Task</a>
															</li>

												<?php
														}

														//We only show the yearly tab to the yearly task
														else {
												?>
													<li class="nav-item">
														<a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile"
															role="tab" aria-controls="profile">Yearly Task</a>
													</li>

												<?php
														}


													}
													else {
												?>

													<?php
														if(!empty($_GET['day']) && $_GET['day'] == 'Yea') {
													?>
													<li class="nav-item">
														<a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile"
															role="tab" aria-controls="profile">Yearly Task</a>
													</li>

													<?php
														}
														else {
													 ?>
													 <li class="nav-item">
 														<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home"
 															role="tab" aria-controls="home">Daily Task</a>
 													</li>
														<?php
														}
														 ?>
												<?php
													}
												?>
                                            </ul> <br />

									<div class="tab-content" id="myTabContent">


                                                <div class="tab-pane fade<?php if (!empty($task['task_weekdays']) && substr_count($task['task_weekdays'], 'Yea') > 0 or !empty($_GET['day']) && $_GET['day'] == 'Yea') { echo ' in active show'; } ?>" id="profile" role="tabpanel"
                                                     aria-labelledby="profile-tab">
                                                                <label>Select the date where you are going to perform the task<span class="text-danger">*</span></label>
                                                            <p class="text-muted m-b-15 font-13">
                                                                *For Yearly Task The Notification is sent at 8:00 AM
                                                            </p>
                                                                <div>
                                                                    <div class="input-group">
                                                                        <input type="text" name="task_yearly_date" <?php if (!empty( $task['task_time'] ) && substr_count($task['task_weekdays'], 'Yea') > 0) { echo ' value="'.$task['task_time'].'"'; } ?> class="form-control" placeholder="mm/dd/yyyy" id="datepicker">
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text"><i class="icon-calender"></i></span>
                                                                        </div>
                                                                    </div><!-- input-group -->

                                                                </div><br />

																<!--<label>Select the Time to Perform the Task<span class="text-danger">*</span></label>
																<div class="input-group">
																	<input id="timepicker3" type="text" name="task_yearly_time" <?php if (!empty( $task['task_time']) && substr_count($task['task_weekdays'] , 'Yea') > 0) { echo ' value="'.date("g:i a", strtotime($task['task_time'])).'"'; } ?> class="form-control">
																	<div class="input-group-append">
																		<span class="input-group-text"><i class="zmdi zmdi-time"></i></span>
																	</div>
																</div><br />-->
                                                </div>

                                        <div role="tabpanel" class="tab-pane fade <?php if (empty($task['task_weekdays']) && empty($_GET['day']) or !empty($_GET['day']) && $_GET['day'] != 'Yea' or !empty($task['task_weekdays']) && substr_count($task['task_weekdays'], 'Yea') == 0 ) { echo ' in active show'; } ?>" id="home"
                                                     aria-labelledby="home-tab">


                                            <label>Select the Time to Perform the Task<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input id="timepicker3" type="text" name="task_time" <?php if (!empty( $task['task_time'] )) { echo ' value="'.date("g:i a", strtotime($task['task_time'])).'"'; } ?> class="form-control">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="zmdi zmdi-time"></i></span>
                                                </div>
                                            </div><!-- input-group --><br />

                                        <div class="form-group">
                                            <label>Select the Days that this Task is going to be performed<span class="text-danger">*</span></label>

												<br /><br />

												<!-- Sunday -->
												<div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="sunday" type="checkbox" name="Sun" value="true"<?php if (!empty($task['task_weekdays']) && substr_count($task['task_weekdays'], "Sun") > 0) { echo ' checked'; }?>>
                                                            <label for="sunday"> Sunday </label>
                                                        </div>
                                                </div>

												<!-- Monday -->
												<div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="monday" type="checkbox" name="Mon" value="true"<?php if (!empty($task['task_weekdays']) && substr_count($task['task_weekdays'], "Mon") > 0) { echo ' checked'; }?>>
                                                            <label for="monday"> Monday </label>
                                                        </div>
                                                </div>

												<!-- Tuesday -->
												<div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="tuesday" type="checkbox" name="Tue" value="true"<?php if (!empty($task['task_weekdays']) && substr_count($task['task_weekdays'], "Tue") > 0) { echo ' checked'; }?>>
                                                            <label for="tuesday"> Tuesday </label>
                                                        </div>
                                                </div>

												<!-- Wednesday -->
												<div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="wednesday" type="checkbox" name="Wed" value="true"<?php if (!empty($task['task_weekdays']) && substr_count($task['task_weekdays'], "Wed") > 0) { echo ' checked'; }?>>
                                                            <label for="wednesday"> Wednesday </label>
                                                        </div>
                                                </div>

												<!-- Thursday -->
												<div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="thursday" type="checkbox" name="Thu" value="true"<?php if (!empty($task['task_weekdays']) && substr_count($task['task_weekdays'], "Thu") > 0) { echo ' checked'; }?>>
                                                            <label for="thursday"> Thursday </label>
                                                        </div>
                                                </div>

												<!-- Friday -->
												<div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="friday" type="checkbox" name="Fri" value="true"<?php if (!empty($task['task_weekdays']) && substr_count($task['task_weekdays'], "Fri") > 0) { echo ' checked'; }?>>
                                                            <label for="friday"> Friday </label>
                                                        </div>
                                                </div>

												<!-- Saturday -->
												<div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="saturday" type="checkbox" name="Sat" value="true"<?php if (!empty($task['task_weekdays']) && substr_count($task['task_weekdays'], "Sat") > 0) { echo ' checked'; }?>>
                                                            <label for="saturday"> Saturday </label>
                                                        </div>
                                                </div>
                                        </div>
										</div>
									</div>

                                            <div class="form-group text-right m-b-0">

												<button class="btn btn-primary waves-effect waves-light" name="submit" type="submit">

													<?php
														if ( !empty($_GET['edit']) && get_task($_GET['edit']) ) {
															echo 'Update Task';
														}
														else {
															echo 'Add New Task';
														}
													?>
                                                </button>

												<?php
														if ( !empty($_GET['edit']) && get_task($_GET['edit']) ) {
															echo '<input type="hidden" name="task_to_edit" value="'.$task['id_task'].'">';
														}
												?>
                                            </div>


                                    </div>

                                </div>
							</form>
                            </div>
                            <!-- end row -->

                        </div>
                    </div><!-- end col-->

                </div>
                <!-- end row -->


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
