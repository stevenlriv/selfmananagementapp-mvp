<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );
	include ( dirname(__FILE__).'/includes/user-notifications.php' );

	//Get the URL for the Day if in use
	$urlday = '';
	if (!empty($_GET['day']) && is_aproveed_day($_GET['day'])) {
		$urlday = '&day='.$_GET['day'];
	}

	//Proccess the removal of a task
	if ( !empty($_GET['delete']) && empty($_GET['confirm']) && get_task($_GET['delete']) ) {
		$alert['content'] = 'Are you sure you want to delete it? <a href="?delete='.$_GET['delete'].'&confirm=true'.$urlday.'" class="btn btn-danger">YES, Delete It.</a>';
		$alert['type'] = 'warning';
		$alert['non-hide'] = true;
	}
	elseif ( get_task($_GET['delete']) && !empty($_GET['delete']) && !empty($_GET['confirm']) && $_GET['confirm'] == 'true' ) {
		if (  delete_task($_GET['delete']) ) {
			$alert['content'] = 'Great! Your task was deleted.';
			$alert['type'] = 'success';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	//Here we establish the query to the database
		//Get the Specific Day
		if (!empty($_GET['day']) && is_aproveed_day($_GET['day']) ) {
			$spday = substr($_GET['day'], 0, 3);
		}
		else {
			$spday = $actual_date->format('D');
		}

		$query = 'WHERE id_user = '.$user['id_user'].' AND task_weekdays LIKE "%'.$spday.'%" ORDER BY task_time ASC';

		//We verify if there is a task in that section
		if( get_task('count', $query) == 0 ) {
			$alert['content'] = 'There is no task in this section, you should add one.';
			$alert['type'] = 'info';
		}

	//Theme Header Configuration
	$array = array (
		'title' => 'Daily Tasks',
		'extra' => '',

		'favicon_folder' => 'daily',
		'mask-icon-color' => '#5bbad5',
		'theme-color' => '#ffffff',
		'TileColor' => '#2d89ef',
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
                        <h4 class="page-title float-left">
						Your Tasks for <?php

							if (!empty($_GET['day']) && is_aproveed_day($_GET['day']) ) {

								echo is_aproveed_day($_GET['day']);

							}
							else {

								echo is_aproveed_day($actual_date->format('D'));

							}
						?>
						</h4>

                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">SelfMan</a></li>
                            <li class="breadcrumb-item"><a href="/all-tasks.php">Daily Task</a></li>
                            <li class="breadcrumb-item active">Your Tasks</li>
                        </ol>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

						<?php
						//The Standard Small Description Required per Section
						small_desc('ALL-TASK',
							'Welcome to the SelfManagement App, here you will be able to manage different daily Task and get notifications once the exact time for that task comes. You will need to enable the Pushover app for the notifications, to set it up <a href="/setup.php?q=pushover">click here</a>'
						);

							small_desc('SLEEP-WAKE',
								'Remember to set up the time that you go to sleep and at what time you wake up. Click <a href="/setup.php">here</a> to set them up.'
							);
						?>

			<?php
				//Do not show the weekly menu on the yearly task
				if (empty($_GET['day']) or !empty($_GET['day']) && $_GET['day'] != 'Yea' ) {
			?>
            <div class="row">
			<div class="col-12" style="margin-bottom: 20px !important;">
								 <div class="btn-group dropdown pull-right">
										 <button type="button" class="btn btn-primary waves-effect waves-light">Select a Day</button>
										 <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>

										 <div class="dropdown-menu">
											<a href="?day=Sun" class="dropdown-item<?php if(empty($_GET['day']) && $actual_date->format('D') == 'Sun' or !empty($_GET['day']) && $_GET['day'] == 'Sun') { echo ' active'; } ?>">Sun</a>
				 							<a href="?day=Mon" class="dropdown-item<?php if(empty($_GET['day']) && $actual_date->format('D') == 'Mon' or !empty($_GET['day']) && $_GET['day'] == 'Mon') { echo ' active'; } ?>">Mon</a>
				 							<a href="?day=Tue" class="dropdown-item<?php if(empty($_GET['day']) && $actual_date->format('D') == 'Tue' or !empty($_GET['day']) && $_GET['day'] == 'Tue') { echo ' active'; } ?>">Tue</a>
				 							<a href="?day=Wed" class="dropdown-item<?php if(empty($_GET['day']) && $actual_date->format('D') == 'Wed' or !empty($_GET['day']) && $_GET['day'] == 'Wed') { echo ' active'; } ?>">Wed</a>
				 							<a href="?day=Thu" class="dropdown-item<?php if(empty($_GET['day']) && $actual_date->format('D') == 'Thu' or !empty($_GET['day']) && $_GET['day'] == 'Thu') { echo ' active'; } ?>">Thu</a>
				 							<a href="?day=Fri" class="dropdown-item<?php if(empty($_GET['day']) && $actual_date->format('D') == 'Fri' or !empty($_GET['day']) && $_GET['day'] == 'Fri') { echo ' active'; } ?>">Fri</a>
				 							<a href="?day=Sat" class="dropdown-item<?php if(empty($_GET['day']) && $actual_date->format('D') == 'Sat' or !empty($_GET['day']) && $_GET['day'] == 'Sat') { echo ' active'; } ?>">Sat</a>
										 </div>
								 </div>
		 </div>
	</div>

			<?php
				}
			?>

			<div class="row">

					<div class="col-12">

							<div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important; margin-left: 20px !important;">
									<a class="dropdown-item" href="/new-task.php?<?php echo $urlday; ?>"><h6 class="dropdown-header">New Task</h6></a>
							</div>

	</div>

</div>
            <div class="row">

				<div class="col-12">

					<?php

						/**
						 * We want to display also al the task that are on setup, workouts and meal plan in your daily task plan
						 *
						 * So we will need to create a new array and proccess that one instead
						 *
						 * Task Structure[array #]
						 *		- id_task
						 *		- id_user
						 *		- status
						 *		- task_push
						 *		- task_title
						 *		- task
						 *		- task_desc
						 *		- task_time
						 *		- task_weekdays
						 *
						 *		- task_color (this is an extra car to ad color to the main task tag; value must be "true")
						 *		- task_url_delete (used for the delete functions that are not native from task)
						 *		- task_url_edit (used for the edit functions that are not native from task)
						 */
						$big_task_array = array();

						/********************************/
						/**** Task QUERYS PROCCESS ***/
						/********************************/

						foreach ( get_task('all', $query) as $id => $value ) {
							$big_task_array[] = array(
									'id_task' => $value['id_task'],
									'id_user' => $user['id_user'],
									'status' => $value['status'],
									'task_push' => $value['task_push'],
									'task_title' => $value['task_title'],
									'task' => $value['task'],
									'task_desc' => $value['task_desc'],
									'task_time' => $value['task_time'],
									'task_weekdays' => $value['task_weekdays'],
							);

						}

						/********************************/
						/**** WORKOUT QUERYS PROCCESS ***/
						/********************************/

						$workout_query = 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Workouts" AND pm_body_3 LIKE "%'.$spday.'%" ORDER BY pm_body_5 ASC';

						foreach ( get_pm('all', $workout_query) as $id => $value ) {
							//Task Push
							if (!empty($value['pm_body_4']) && $value['pm_body_4'] != 'false') {
								$task_push = 'true';
							}
							else {
								$task_push = 'false';
							}

							$big_task_array[] = array(
									'id_task' => $value['id_pm'],
									'id_user' => $user['id_user'],
									'status' => '',
									'task_push' => $task_push,
									'task_title' => 'Workouts',
									'task' => $value['pm_body_1'],
									'task_desc' => '',
									'task_time' => $value['pm_body_5'],
									'task_weekdays' => $value['pm_body_3'],
									'task_color' => 'true',
									'task_url_delete' => 'personal-workouts.php?delete='.$value['id_pm'],
									'task_url_edit' => 'new-workout.php?edit='.$value['id_pm'],
							);

						}

						/********************************/
						/**** MEALS QUERYS PROCCESS ***/
						/********************************/

						$meals_query = 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Meal" AND pm_body_3 LIKE "%'.$spday.'%" ORDER BY pm_body_5 ASC';

						foreach ( get_pm('all', $meals_query) as $id => $value ) {
							//Task Push
							if (!empty($value['pm_body_4']) && $value['pm_body_4'] != 'false') {
								$task_push = 'true';
							}
							else {
								$task_push = 'false';
							}

							$big_task_array[] = array(
									'id_task' => $value['id_pm'],
									'id_user' => $user['id_user'],
									'status' => '',
									'task_push' => $task_push,
									'task_title' => 'Meals',
									'task' => $value['pm_body_1'],
									'task_desc' => '',
									'task_time' => $value['pm_body_5'],
									'task_weekdays' => $value['pm_body_3'],
									'task_color' => 'true',
									'task_url_delete' => 'personal-meal.php?delete='.$value['id_pm'],
									'task_url_edit' => 'new-meal.php?edit='.$value['id_pm'],
							);

						}

						/********************************/
						/**** SetUps QUERYS PROCCESS ***/
						/********************************/

						foreach ( $users_notifications as $id => $value ) {
							//We first verify that the setup is in user
							//In this case there is no need for task_push, we just set it up "true"
							if ( $user[$value['database']] == 'OFF' || $user[$value['database']] == 'DISABLE' || !isset($value['database']) ) {
								continue;
							}

							//We get the days and time
							$array_data = explode(';', $user[$value['database']]);
							$st['days'] = $array_data[0];
							$st['time'] = $array_data[1];

							//We verify if this setup is in the "query search"
							if ( substr_count($st['days'], $spday) == 0 ) {
								continue;
							}

							$big_task_array[] = array(
									'id_task' => '',
									'id_user' => $user['id_user'],
									'status' => '',
									'task_push' => 'true',
									'task_title' => 'Setups',
									'task' => $value['name'],
									'task_desc' => '',
									'task_time' => $st['time'],
									'task_weekdays' => $st['days'],
									'task_color' => 'true',
									'task_url_delete' => 'setup.php?q='.$value['q'],
									'task_url_edit' => 'setup.php?q='.$value['q'],
							);
						}

						/********************************/
						/**** WE SORT THE ARRAY ***/
						/********************************/
						array_multisort( array_column($big_task_array, "task_time"), SORT_ASC, $big_task_array );

						//This variable is used to know when to add, some dive classes for desktop app use. Each three iterations there are added
						$i = 0;
						foreach ( $big_task_array as $id => $value ) {
							if ( $i == 0 or $i % 3 == 0 ) {
								echo '<div class="card-deck-wrapper">
											<div class="card-deck">	';
							}

							echo '<!-- CARD #'.$i.' -->';
					?>
								<div class="card m-b-20">
									<div class="card-body">
										<p class="card-text">
											<small class="text-muted" style="font-weight: bold;">
												<?php
													if ( substr_count($value['task_weekdays'], 'Yea') > 0 ) {
														echo date("F d", strtotime($value['task_time']));
													}
													else {
														echo date("g:i a", strtotime($value['task_time']));
													}
												?>
											</small>

											&nbsp;&nbsp;&nbsp;&nbsp;

											<?php
												if ($value['task_push'] == 'true') {
													echo '<span class="label label-success"> Active </span>';
												}
												else {
													echo '<span class="label label-danger"> Inactive </span>';
												}
											?>



											<span class="label <?php if (!empty($value['task_color'])) { echo 'label-warning'; } else { echo 'label-default'; } ?>"> <?php echo $value['task_title']; ?> </span>
										</p>

										<h5 class="card-title"><?php echo $value['task']; ?></h5>
										<p class="text-muted m-b-15 font-13"><?php echo $value['task_desc']; ?></p>
									</div>

									<ul class="list-group list-group-flush">
										<li class="list-group-item">
											<a href="/<?php if ( !empty($value['task_url_edit'])) { echo $value['task_url_edit']; } else { echo 'new-task.php?edit='.$value['id_task'].$urlday; } ?>" class="card-link"><i class="fa fa-edit"></i> </a>
											<a href="<?php if ( !empty($value['task_url_delete'])) { echo '/'.$value['task_url_delete']; } else { echo'?delete='.$value['id_task'].$urlday; } ?>" class="card-link text-danger"><i class="fa fa-trash"></i> </a>
										</li>
									</ul>
								</div>
					<?php
							//$i = 2 is the first itteration of 3 cards, so we start with the footer here
							//We use $i > 3 because if not, quickly after $i == 2 another footer would be print
							//The $i_c makes sure to close the itteration before a new one start
							$i_c = $i + 1;
							if ( $i == 2 or $i > 3 && $i_c % 3 == 0 ) {
								echo '</div>
									</div>';
							}
							$i++;
						}
					?>

                </div>

				<!-- FIX the dropdown in Computer -->
				<div class="fix-dropdown"></div>

            </div>

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
