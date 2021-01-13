<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

	//We proccess here the disable notifications
	if ( !empty($_GET['q']) && $_GET['q'] == 'disable' ) {
		if ( update_user($user['id_user'], 'notifications', 'OFF') ) {
			$alert['content'] = 'Great! Your notifications were disabled.';
			$alert['type'] = 'success';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	//We proccess here the enabled notifications
	if ( !empty($_GET['q']) && $_GET['q'] == 'enable' ) {
		if ( update_user($user['id_user'], 'notifications', 'ON') ) {
			$alert['content'] = 'Great! Your notifications were enabled.';
			$alert['type'] = 'success';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}
	// Proccess all form submitions
	if ( isset($_POST['submit_image']) ) {
		if ( !empty($_FILES['profile_image']) ) {
			$crypt = new CryptoLib();
			$random = $crypt->randomInt( 0000000, 9999999 );

			if ( new_image($_FILES['profile_image'], $random) ) {
				$url_link = get_domain ().'/assets/uploads/'.$random.'_'.$_FILES['profile_image']['name'];

				if ( update_user($user['id_user'], 'profile_image', trim($url_link)) ) {
					header( "refresh:2" );
					$alert['content'] = 'Great! Your have a new profile picture.';
					$alert['type'] = 'success';
				}
				else {
					$alert['content'] = 'Oops! An error occurred, try again.';
					$alert['type'] = 'error';
				}
			}
			else {
				$alert['content'] = 'Oops! An error occurred, try again.';
				$alert['type'] = 'error';
			}
		}
	}

	elseif ( isset($_POST['submit']) ) {
		//Remove vars that don't belong to the database
		unset($_POST['submit']);

		foreach ( $_POST as $name => $value ) {

			//This is added to eliminate the email warning message while editing emails with the same value
			if ( $name == 'email' && $value == $user['email'] ) {
				continue;
			}

			//Check if password is going to be updated, if not, skip it
			if ( $name == 'password' && $value == '' or $name == 'confirm' && $value == '' ) {
				continue;
			}

			if ( $name == 'password' ) {
				if ( $value == $_POST['confirm'] ) {
					if (update_user($user['id_user'], $name, trim($value))) {
						$success[]['id'] = $name;
					}
					else {
						$error[]['id'] = $name;
					}
				}
				else {
					$error[]['id'] = $name;
				}
				continue;
			}

			//Update the different form values
			if ( update_user($user['id_user'], $name, trim($value)) ) {
				$success[]['id'] = $name;
			}
			else {
				$error[]['id'] = $name;
			}
		}

		//Show the error messages
		if ( empty($error) && !empty($success) ) {
			header( "refresh:2" );
			$alert['content'] = 'Great! The user details were updated successfully.';
			$alert['type'] = 'success';
		}
		elseif ( !empty($error) && !empty($success) ) {
			header( "refresh:2" );
			$alert['content'] = 'Not all fields where updated successfully.';
			$alert['type'] = 'warning';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	//Theme Header Configuration
	$array = array (
		'title' => 'Edit Your Account Details',
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
                            <h4 class="page-title float-left">Edit Account</h4>

                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="#">SelfMan</a></li>
                                <li class="breadcrumb-item"><a href="#">Users</a></li>
                                <li class="breadcrumb-item active">Edit Account</li>
                            </ol>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

				<div class="row">

					<div class="col-12">

						<div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important; margin-left: 20px !important;">
							<?php
								if( $user['notifications'] == 'OFF' ) {
							?>
							<a class="dropdown-item" href="?q=enable"><h6 class="dropdown-header text-success">Enable Notifications</h6></a>

							<?php
								}
								else {


							 ?>
								<a class="dropdown-item" href="?q=disable"><h6 class="dropdown-header text-danger">Disable Notifications</h6></a>
							<?php
								}
							 ?>
						</div>

					</div>

				</div>

                <div class="row">
                    <div class="col-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-6">

                                    <div class="p-20">
                                        <form action="/user-settings.php" data-parsley-validate novalidate method="POST">
                                            <div class="form-group">
                                                <label for="userName">Full Name<span class="text-danger">*</span></label>
                                                <input type="text" name="fullname" maxlength="80"<?php if( !empty($user['fullname']) ) { echo ' value="'.$user['fullname'].'"'; } ?> parsley-trigger="change" required
                                                        class="form-control" id="userName">
                                            </div>

                                            <div class="form-group">
                                                <label for="emailAddress">Email address<span class="text-danger">*</span></label>
                                                <input type="email" name="email" maxlength="255"<?php if( !empty($user['email']) ) { echo ' value="'.$user['email'].'"'; } ?> parsley-trigger="change" required
                                                       class="form-control" id="emailAddress">
                                            </div>

																						<!--<div class="form-group">
                                                <label for="goal_weights">Goal Weight (lbs)<span class="text-danger">*</span></label>
                                                            <p class="text-muted m-b-15 font-13">
                                                                **Enter your aproximate Goal Weight. This is going to be used to test you during the Weight Audits.
                                                            </p>

                                                <input type="number" name="goal_weight" <?php if( !empty($user['goal_weight']) ) { echo ' value="'.$user['goal_weight'].'"'; } ?>
                                                       class="form-control" required>
                                            </div>-->

                                            <div class="form-group">
                                                <label for="emailAddress">Your Time Zone</label>
                                                            <p class="text-muted m-b-15 font-13">
                                                                **Used to send the Push Notifications.
                                                            </p>

                                                <select name="user_time_zone" class="form-control select2">
													<option>-- Please, select timezone --</option>
													<?php foreach(tz_list() as $t) { ?>
														<option value="<?php print $t['zone'] ?>"<?php
															if( !empty($user['user_time_zone']) && $user['user_time_zone'] == $t['zone'] ) {
																echo ' selected="selected"';
															}?>>
															<?php print $t['diff_from_GMT'] . ' - ' . $t['zone'] ?>
														</option>
													<?php } ?>
												</select>

                                            </div>

                                            <!--<div class="form-group">
                                                <label for="emailAddress">Pushover App Token</label>
                                                            <p class="text-muted m-b-15 font-13">
                                                                **<b>Required</b> for sending Pushover notifications. To learn how to set it up, <a href="/setup.php?q=pushover">click here</a>
                                                            </p>

                                                <input type="text" name="push_app_token" <?php if( !empty($user['push_app_token']) ) { echo ' value="'.$user['push_app_token'].'"'; } ?>
                                                       class="form-control">
                                            </div>-->

                                            <div class="form-group">
                                                <label for="emailAddress">Pushover User Token</label>
	                                                        <p class="text-muted m-b-15 font-13">
                                                                **<b>Required</b> for sending Pushover notifications. To learn how to set it up, <a href="/setup.php?q=pushover">click here</a>
                                                            </p>
                                                <input type="text" name="push_user_token" <?php if( !empty($user['push_user_token']) ) { echo ' value="'.$user['push_user_token'].'"'; } ?>
                                                       class="form-control">
                                            </div>

                                            <div class="form-group">

                                                <label for="pass1">Password</label>

                                                            <p class="text-muted m-b-15 font-13">
                                                                Leave it empty if you do not want to change it. Requires a <b>minimum of 8 characters</b>.
                                                            </p>

                                                <input id="pass1" name="password" maxlength="255" type="password" placeholder="Password"
                                                       class="form-control">

                                            </div>
                                            <div class="form-group">
                                                <label for="passWord2">Confirm Password</label>
                                                <input data-parsley-equalto="#pass1" type="password"
                                                       placeholder="Password" name="confirm" maxlength="255" class="form-control" id="passWord2">
                                            </div>

                                            <div class="form-group text-right m-b-0">
                                                <button class="btn btn-primary waves-effect waves-light" name="submit" type="submit">
                                                    Save Changes
                                                </button>
                                            </div>

                                        </form>
                                    </div>

                                </div>

                                <div class="col-sm-12 col-xs-12 col-md-6">

                                    <div class="p-20">
                                        <form action="/user-settings.php" enctype="multipart/form-data" method="POST">
                                            <div class="form-group">
                                                <label for="userName">Your Profile Picture</label>

												<?php
													if( !empty($user['profile_image']) ) {
												?>
													<p class="text-muted font-13 m-b-10">
														<img src="<?php echo $user['profile_image']; ?>" style="max-width:128px; max-height:128px;" class="rounded-circle" />
													</p>
													<br />
												<?php
													}
													else {
													?>
													<p class="text-muted font-13 m-b-10">
														<img src="/assets/images/user-default.png" style="max-width:128px; max-height:128px;" class="rounded-circle" />
													</p>
													<br />
													<?php
													}
												?>

												<input type="file" name="profile_image" class="filestyle" accept="image/*" data-input="false">
                                            </div>

											<br />
                                            <div class="form-group text-right m-b-0">
                                                <button class="btn btn-primary waves-effect waves-light" name="submit_image" type="submit">
													<?php
														if( !empty($user['profile_image']) ) {
															echo 'Change Profile Picture';
														}
														else {
															echo 'Upload Profile Picture';
														}
													?>
                                                </button>
                                            </div>

                                        </form>
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
