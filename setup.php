<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );
	include ( dirname(__FILE__).'/includes/user-notifications.php' );

	//We check that is a valid set up, if not we just show the options
	if ( is_aproveed_setup($users_notifications, $_GET['q']) ) {
			$st = is_aproveed_setup($users_notifications, $_GET['q']);
	}

	// Proccess all form submitions
	if ( isset($_POST['pushover']) && !empty($_POST['push_user_token'])) {

			//Proccess Updates or Create New Ones
				if ( update_user ( $user['id_user'], 'push_user_token', trim($_POST['push_user_token']) ) ) {
					header( "refresh:2" );
					$alert['content'] = 'Great! Your user key was updated.';
					$alert['type'] = 'success';
				}
				else {
					$alert['content'] = 'Oops! An error occurred, try again.';
					$alert['type'] = 'error';
				}
		}

// Proccess all form submitions
if ( isset($_POST['submit']) && !empty($_POST['table_to_edit'])) {
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

		//Here We convert the 12 hour to 24 hour so SQL can organize correctly
		$_POST['st_time'] = date("H:i", strtotime($_POST['st_time']));

		//Here we add a cero to the date if it only contains 1 number in the hour. Count will be "00:00"
		if ( strlen($_POST['st_time'])<5  ) {
			$_POST['st_time'] = '0'.$_POST['st_time'];
		}

		//We join the from data and trim it
		$form = trim($days).';'.trim($_POST['st_time']);

		//We verify if is check to be disabled
		if ( !empty($_POST['DISABLE']) ) {
			$form = 'DISABLE';
		}

		//Proccess Updates or Create New Ones
			if ( update_user ( $user['id_user'], $_POST['table_to_edit'], trim($form) ) ) {
				$alert['content'] = 'Great! Your updates where made.';
				$alert['type'] = 'success';
			}
			else {
				$alert['content'] = 'Oops! An error occurred, try again.';
				$alert['type'] = 'error';
			}
	}

	//Theme Header Configuration
	$array = array (
		'title' => 'Setups',
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
                            <h4 class="page-title float-left">Setups</h4>

                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="#">SelfMan</a></li>
                                <li class="breadcrumb-item"><a href="/setup.php">Users</a></li>
                                <li class="breadcrumb-item active">Setup</li>
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

                                    <div class="p-20">
																			<?php
																				if (empty($st)) {
																			?>

																                <div class="card m-b-20 bg-info text-white">
																                    <div class="card-body">
																						<blockquote class="card-bodyquote">
																                            <p>
																								Here you can manage some apps notifications, and setting the days and time where you would like to receive them (some are Affirmation, Expenses Reminders). You can also disable the notifications.
																							</p>
																                        </blockquote>
																                    </div>
																                </div>

																			<div class="btn-group dropdown">
									                        <button type="button" class="btn btn-primary waves-effect waves-light">Select the Setup to edit</button>
									                        <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>

									                        <div class="dropdown-menu more-height">
																						<?php
																								foreach( $users_notifications as $id => $value ) {
																								 	echo '<div><a href="?q='.$value['q'].'" class="dropdown-item">'.$value['name'].'</a></div>';
																						 		}
																						 ?>
									                        </div>

									                    </div>

															<!-- FIX the dropdown in Computer -->
															<div class="fix-dropdown"></div>


																			<?php
																				}
																				elseif(!empty($st) && $st['q'] == 'pushover') {

																					/**
																					 * Pushover Setups
																					 */

																			?>

																			<h4 class="header-title m-t-0">Pushover Setup</h4>

																			<br />


																			<p style="font-style: italic;">What is <a href="https://pushover.net/faq#overview-what" target="_blank">Pushover</a> and how do I use it?</p>

																			<p>In short, Pushover is a service to receive instant push notifications on your phone or tablet from a variety of sources. For individuals, after a <b>7-day free trial</b>, it costs <b>$4.99 USD once</b>, per-platform (iOS, Android, Desktop), to use on as many of your own devices on that platform as you want. There are no monthly or yearly fees and you can receive as many notifications as you want.</p>

																			<br />
																				<br />

																			<h4 class="header-title m-t-0" style="font-style: italic;">Using Pushover App</h4>
																			<br />

																			<ol>
																				<li>Go to the <b>App Store</b> or <b>Google Store</b> and search for "Pushover" (logo is a big blue P). Once you find it, download it.</li>
																				<li>Open the app and <b>Create an Account</b> or Login</li>
																				<li>After that, is going to ask you to name your device, just use any name you want</li>
																				<li>Copy and Paste your <b>User Key</b> below, and press <b>"Add Token"</b></li>
																				<li>DONE! <b>(After 7 days you will need to purchase the app for $4.99)</b></li>
																			</ol>

																			<br />

																			<h4 class="header-title m-t-0" style="font-style: italic;">Add the Pushover User Token</h4>
																			<br />

																			<form action="/setup.php?q=pushover" data-parsley-validate novalidate method="POST">


																				<label>Paste the User Key from the Pushover app to here<span class="text-danger">*</span></label>
																				<div class="input-group">
																						<input type="text" name="push_user_token" <?php if (!empty( $user['push_user_token'] )) { echo ' value="'.$user['push_user_token'].'"'; } ?> class="form-control" required>
																						<div class="input-group-append">
																								<span class="input-group-text"><i class="zmdi zmdi-time"></i></span>
																						</div>
																				</div><!-- input-group --><br />

																				<div class="form-group text-right m-b-0">
																						<button class="btn btn-primary waves-effect waves-light" name="pushover" type="submit">
																								Add Token
																						</button>
																				</div>
																			</form>

																			<?php
																				}
																				else {

																					//Get the previus content from the array on the user database
																					if(!empty($user['notification_'.$st['q']]) && $user['notification_'.$st['q']] != 'OFF') {
																						$array_data = explode(';', $user['notification_'.$st['q']]);
																						$st['days'] = $array_data[0];
																						$st['time'] = $array_data[1];
																					}

																					//The Standard Small Description Required per Section
							small_desc('SETUP-'.$st['q'],
								'Here you can manage the '.$st['name'].' notification, and setting the days and time where you would like to receive it. You can also disable it, just check the box at the bottom. If you want to delete it, just check the disable box at the bottom of the page.'
							);
																			?>
																				<h4 class="header-title m-t-0">Fill The Form To Setup a Notification for <?php echo $st['name']; ?></h4>
																				<br />

                                        <form action="/setup.php" data-parsley-validate novalidate method="POST">

																					<div class="form-group">
																							<label>Select the Days to receive the notifications<span class="text-danger">*</span></label>

													<br /><br />

													<!-- Sunday -->
													<div class="form-group">
																													<div class="checkbox">
																															<input id="sunday" type="checkbox" name="Sun" value="true"<?php if (!empty($st['days']) && substr_count($st['days'], "Sun") > 0) { echo ' checked'; }?>>
																															<label for="sunday"> Sunday </label>
																													</div>
																									</div>

													<!-- Monday -->
													<div class="form-group">
																													<div class="checkbox">
																															<input id="monday" type="checkbox" name="Mon" value="true"<?php if (!empty($st['days']) && substr_count($st['days'], "Mon") > 0) { echo ' checked'; }?>>
																															<label for="monday"> Monday </label>
																													</div>
																									</div>

													<!-- Tuesday -->
													<div class="form-group">
																													<div class="checkbox">
																															<input id="tuesday" type="checkbox" name="Tue" value="true"<?php if (!empty($st['days']) && substr_count($st['days'], "Tue") > 0) { echo ' checked'; }?>>
																															<label for="tuesday"> Tuesday </label>
																													</div>
																									</div>

													<!-- Wednesday -->
													<div class="form-group">
																													<div class="checkbox">
																															<input id="wednesday" type="checkbox" name="Wed" value="true"<?php if (!empty($st['days']) && substr_count($st['days'], "Wed") > 0) { echo ' checked'; }?>>
																															<label for="wednesday"> Wednesday </label>
																													</div>
																									</div>

													<!-- Thursday -->
													<div class="form-group">
																													<div class="checkbox">
																															<input id="thursday" type="checkbox" name="Thu" value="true"<?php if (!empty($st['days']) && substr_count($st['days'], "Thu") > 0) { echo ' checked'; }?>>
																															<label for="thursday"> Thursday </label>
																													</div>
																									</div>

													<!-- Friday -->
													<div class="form-group">
																													<div class="checkbox">
																															<input id="friday" type="checkbox" name="Fri" value="true"<?php if (!empty($st['days']) && substr_count($st['days'], "Fri") > 0) { echo ' checked'; }?>>
																															<label for="friday"> Friday </label>
																													</div>
																									</div>

													<!-- Saturday -->
													<div class="form-group">
																													<div class="checkbox">
																															<input id="saturday" type="checkbox" name="Sat" value="true"<?php if (!empty($st['days']) && substr_count($st['days'], "Sat") > 0) { echo ' checked'; }?>>
																															<label for="saturday"> Saturday </label>
																													</div>
																									</div>
																					</div>

																					<br />

																					<label>Select the Time to Receive the notifications<span class="text-danger">*</span></label>
																					<div class="input-group">
																							<input id="timepicker3" type="text" name="st_time" <?php if (!empty( $st['time'] )) { echo ' value="'.date("g:i a", strtotime($st['time'])).'"'; } ?> class="form-control">
																							<div class="input-group-append">
																									<span class="input-group-text"><i class="zmdi zmdi-time"></i></span>
																							</div>
																					</div><!-- input-group --><br /><br />

													<div class="form-group">
																		<h4 class="header-title m-t-0">Disable <?php echo $st['name']; ?> Notification</h4>
																				<br />
																		<label>If you want to disable the notification, check the box below</label>
																													<div class="checkbox">
																															<input id="disable" type="checkbox" name="DISABLE" value="DISABLE"<?php if (!empty($st) && $user['notification_'.$st['q']] == 'DISABLE') { echo ' checked'; }?>>
																															<label for="disable"> Disable? </label>
																													</div>
																									</div>
																					<?php
																							if ( !empty($st) ) {
																								echo '<input type="hidden" name="table_to_edit" value="notification_'.$st['q'].'">';
																							}
																					?>

                                            <div class="form-group text-right m-b-0">
                                                <button class="btn btn-primary waves-effect waves-light" name="submit" type="submit">
                                                    Edit Setup
                                                </button>
                                            </div>

                                        </form>
																				<?php
																				}
																			?>
                                    </div>

                                </div>

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
