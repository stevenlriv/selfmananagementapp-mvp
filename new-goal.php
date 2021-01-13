<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

	//We get the data from the reading if we are going to edit it & We also verify that it belongs to the actual user
	//We also verify that the edit belongs to the actual data type
	if ( !empty($_GET['edit']) && get_pm($_GET['edit']) && get_pm($_GET['edit'])['id_user'] == $user['id_user']
				&& get_pm($_GET['edit'])['pm_type'] == 'Goals' ) {
		$pm = get_pm($_GET['edit']);
	}

	//We establish the long terms goals url
	$get_url = '';
	$goal_type = "short";//default goal type for queries
	if(!empty($_GET['q']) && $_GET['q'] == "long") {
		$get_url = '&q=long';
		$goal_type = "long";
	}

	if(!empty($_GET['arch']) && $_GET['arch'] == "logs") {
		$get_url = $get_url.'&arch=logs';
	}


	// Proccess all form submitions
	if ( isset($_POST['submit']) ) {

		if ( empty($_POST['pm_body_1']) ) {
			$alert['content'] = 'Please enter a goals.';
			$alert['type'] = 'error';
		}

		//Proccess Updates or Create New Ones
		if ( !empty($_POST['pm_to_edit']) ) {
			if ( update_pm ( $_POST['pm_to_edit'], 'pm_body_1', trim($_POST['pm_body_1']) ) &&
				 update_pm ( $_POST['pm_to_edit'], 'pm_body_2', trim($_POST['pm_body_2']) ) &&
			  update_pm ( $_POST['pm_to_edit'], 'pm_body_3', trim($_POST['pm_body_3']) ) &&
			 update_pm ( $_POST['pm_to_edit'], 'pm_body_4', trim($_POST['pm_body_4']) )  ) {
				$alert['content'] = 'Great! Your updates where made.';
				$alert['type'] = 'success';
			}
			else {
				$alert['content'] = 'Oops! An error occurred, try again.';
				$alert['type'] = 'error';
			}
		}
		else {
			if ( empty($alert['content']) && new_pm ( 'Goals', '', '', $_POST['pm_body_1'], $_POST['pm_body_2'], $_POST['pm_body_3'], $_POST['pm_body_4']  ) ) {
				$alert['content'] = 'Great! Your goal was added.';
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
		'title' => 'New Goal',
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

								<h4 class="page-title float-left">Edit Goal</h4>

							<?php
								}

								else {
							?>

								<h4 class="page-title float-left">New Goal</h4>

							<?php
								}
							?>

                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="/">SelfMan</a></li>
                                <li class="breadcrumb-item"><a href="/goals.php?<?php echo $get_url; ?>">Goals List</a></li>

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
										<h4 class="header-title m-t-0">Fill The Form To Add a New Goals</h4>
									<?php
										}
									?>

							<form action="/new-goal.php<?php if(!empty($get_url)) echo "?".$get_url; ?>" data-parsley-validate novalidate method="POST">
                                <div class="p-20">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="userName">Goals<span class="text-danger">*</span></label>
                                                <input type="text" name="pm_body_1" parsley-trigger="change" required
                                                        class="form-control"<?php if (!empty( $pm['pm_body_1'] )) { echo ' value="'.$pm['pm_body_1'].'"'; } ?>>
                                            </div>

												<br />

												<div class="form-group">
														<label for="emailAddress">Goal Timeframe</label>

														<select name="pm_body_3" class="form-control select2">
			<option value="short" <?php if(!empty($pm['pm_body_3']) && $pm['pm_body_3'] == 'short') { echo "selected"; } ?>>Short Term</option>
			<option value="long" <?php if(!empty($pm['pm_body_3']) && $pm['pm_body_3'] == 'long' or empty($pm['pm_body_3']) && !empty($_GET['q']) && $_GET['q'] == 'long') { echo "selected"; } ?>>Long Term</option>
		</select>
	</div>
<br />
	<div class="form-group">
			<label for="emailAddress">Goal Category</label>

			<select name="pm_body_4" class="form-control select2">
<option value="Financial" <?php if(!empty($pm['pm_body_4']) && $pm['pm_body_4'] == 'Financial') { echo "selected"; } ?>>Financial</option>
<option value="My Self" <?php if(!empty($pm['pm_body_4']) && $pm['pm_body_4'] == 'My Self') { echo "selected"; } ?>>My Self</option>
<option value="I want" <?php if(!empty($pm['pm_body_4']) && $pm['pm_body_4'] == 'I want') { echo "selected"; } ?>>I Want</option>
</select>
</div>
<br />
                                            <div class="form-group">
                                                            <label>
                                                                Goals Specifics
                                                            </label>
																														<p class="text-muted font-13 m-b-10">
																															You can write the specifics of your accomplishment treat or your punishment task (or requirements)
																														</p>

												<textarea id="text-editor" name="pm_body_2" class="form-control" rows="8" parsley-trigger="change"><?php if (!empty( $pm['pm_body_2'] )) { echo $pm['pm_body_2']; } ?></textarea>
                                            </div>

												<br />

                                            <div class="form-group text-right m-b-0">

												<button class="btn btn-primary waves-effect waves-light" name="submit" type="submit">

													<?php
														if ( !empty($pm) ) {
															echo 'Update Goal';
														}
														else {
															echo 'Add New Goal';
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
