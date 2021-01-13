<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

	//We get the data from the reading if we are going to edit it & We also verify that it belongs to the actual user
	//We also verify that the edit belongs to the actual data type
	if ( !empty($_GET['edit']) && get_pm($_GET['edit']) && get_pm($_GET['edit'])['id_user'] == $user['id_user']
				&& get_pm($_GET['edit'])['pm_type'] == 'Meal' ) {
		$pm = get_pm($_GET['edit']);
	}

	// Proccess all form submitions
	if ( isset($_POST['submit']) ) {

		if ( empty($_POST['pm_body_4']) ) {
			$_POST['pm_body_4'] = 'false';
		}

		if ( empty($_POST['pm_body_1']) ) {
			$alert['content'] = 'Please enter meal plan name.';
			$alert['type'] = 'error';
		}

		if ( empty($_POST['pm_body_2']) ) {
			//$alert['content'] = 'Please enter the meal description.';
			//$alert['type'] = 'error';
			$_POST['pm_body_2'] = '';
		}

		//Here We convert the 12 hour to 24 hour so SQL can organize correctly
		$_POST['pm_body_5'] = date("H:i", strtotime($_POST['pm_body_5']));

		//Here we add a cero to the date if it only contains 1 number in the hour. Count will be "00:00"
		if ( strlen($_POST['pm_body_5'])<5  ) {
			$_POST['pm_body_5'] = '0'.$_POST['pm_body_5'];
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

		//Proccess Updates or Create New Ones
		if ( !empty($_POST['pm_to_edit']) ) {
			if ( update_pm ( $_POST['pm_to_edit'], 'pm_body_1', trim($_POST['pm_body_1']) ) &&
				 update_pm ( $_POST['pm_to_edit'], 'pm_body_2', trim($_POST['pm_body_2']) ) &&
			 update_pm ( $_POST['pm_to_edit'], 'pm_body_3', trim($days) ) &&
		 				 update_pm ( $_POST['pm_to_edit'], 'pm_body_4', trim($_POST['pm_body_4']) ) &&
						 update_pm ( $_POST['pm_to_edit'], 'pm_body_5', trim($_POST['pm_body_5'])) ) {
				$alert['content'] = 'Great! Your updates where made.';
				$alert['type'] = 'success';
			}
			else {
				$alert['content'] = 'Oops! An error occurred, try again.';
				$alert['type'] = 'error';
			}
		}
		else {
			if ( empty($alert['content']) && new_pm ( 'Meal', '', '', $_POST['pm_body_1'], $_POST['pm_body_2'], $days, $_POST['pm_body_4'], $_POST['pm_body_5']  ) ) {
				$alert['content'] = 'Great! Your meal was added.';
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
		'title' => 'New Meal',
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
								if ( !empty($pm) ) {
							?>

								<h4 class="page-title float-left">Edit Meal Plan</h4>

							<?php
								}

								else {
							?>

								<h4 class="page-title float-left">New Meal Plan</h4>

							<?php
								}
							?>

                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="/">SelfMan</a></li>
                                <li class="breadcrumb-item"><a href="/personal-meal.php">Meal Plan</a></li>

								<?php
									if ( !empty($pm) ) {
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
										if ( empty($_GET['edit']) or !empty($_GET['edit']) && !get_pm($_GET['edit']) ) {
									?>
										<h4 class="header-title m-t-0">Fill The Form To Add a New Meal Plan</h4>
									<?php
										}
									?>

							<form action="/new-meal.php" data-parsley-validate novalidate method="POST">
                                <div class="p-20">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="userName">Meal Plan Name<span class="text-danger">*</span></label>
                                                <input type="text" name="pm_body_1" parsley-trigger="change" required
                                                        class="form-control"<?php if (!empty( $pm['pm_body_1'] )) { echo ' value="'.$pm['pm_body_1'].'"'; } ?>>
                                            </div>

												<br />

												<div class="form-group">
																				<div class="checkbox">
																						<input id="remember-1" type="checkbox" name="pm_body_4" value="true"<?php if (!empty($pm['pm_body_4']) && $pm['pm_body_4'] == 'false') { } else { echo ' checked'; }?>>
																						<label for="remember-1"> Send a notification at the time of the workout </label>
																				</div>
																		</div>

												<br />

												<div class="form-group">
														<label>Select the Days that this Meal Plan is going to be followed<span class="text-danger">*</span></label>

				<br /><br />

				<!-- Sunday -->
				<div class="form-group">
																				<div class="checkbox">
																						<input id="sunday" type="checkbox" name="Sun" value="true"<?php if (!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Sun") > 0) { echo ' checked'; }?>>
																						<label for="sunday"> Sunday </label>
																				</div>
																</div>

				<!-- Monday -->
				<div class="form-group">
																				<div class="checkbox">
																						<input id="monday" type="checkbox" name="Mon" value="true"<?php if (!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Mon") > 0) { echo ' checked'; }?>>
																						<label for="monday"> Monday </label>
																				</div>
																</div>

				<!-- Tuesday -->
				<div class="form-group">
																				<div class="checkbox">
																						<input id="tuesday" type="checkbox" name="Tue" value="true"<?php if (!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Tue") > 0) { echo ' checked'; }?>>
																						<label for="tuesday"> Tuesday </label>
																				</div>
																</div>

				<!-- Wednesday -->
				<div class="form-group">
																				<div class="checkbox">
																						<input id="wednesday" type="checkbox" name="Wed" value="true"<?php if (!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Wed") > 0) { echo ' checked'; }?>>
																						<label for="wednesday"> Wednesday </label>
																				</div>
																</div>

				<!-- Thursday -->
				<div class="form-group">
																				<div class="checkbox">
																						<input id="thursday" type="checkbox" name="Thu" value="true"<?php if (!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Thu") > 0) { echo ' checked'; }?>>
																						<label for="thursday"> Thursday </label>
																				</div>
																</div>

				<!-- Friday -->
				<div class="form-group">
																				<div class="checkbox">
																						<input id="friday" type="checkbox" name="Fri" value="true"<?php if (!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Fri") > 0) { echo ' checked'; }?>>
																						<label for="friday"> Friday </label>
																				</div>
																</div>

				<!-- Saturday -->
				<div class="form-group">
																				<div class="checkbox">
																						<input id="saturday" type="checkbox" name="Sat" value="true"<?php if (!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Sat") > 0) { echo ' checked'; }?>>
																						<label for="saturday"> Saturday </label>
																				</div>
																</div>
												</div>

												<br />

												<label>Select the Time to Eat the Meal<span class="text-danger">*</span></label>
												<div class="input-group">
														<input id="timepicker3" type="text" name="pm_body_5" <?php if (!empty( $pm['pm_body_5'] )) { echo ' value="'.date("g:i a", strtotime($pm['pm_body_5'])).'"'; } ?> class="form-control">
														<div class="input-group-append">
																<span class="input-group-text"><i class="zmdi zmdi-time"></i></span>
														</div>
												</div><!-- input-group --><br /><br />

                                            <div class="form-group">
																														<label for="userName">Meal Plan Description<span class="text-danger">*</span></label>

												<textarea id="text-editor" name="pm_body_2" class="form-control" rows="8" parsley-trigger="change"><?php if (!empty( $pm['pm_body_2'] )) { echo $pm['pm_body_2']; } ?></textarea>
                                            </div>

												<br />

                                            <div class="form-group text-right m-b-0">

												<button class="btn btn-primary waves-effect waves-light" name="submit" type="submit">

													<?php
														if ( !empty($pm) ) {
															echo 'Update Meal Plan';
														}
														else {
															echo 'Add New Meal Plan';
														}
													?>
                                                </button>

												<?php
														if ( !empty($pm) ) {
															echo '<input type="hidden" name="pm_to_edit" value="'.$pm['id_pm'].'">';
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
