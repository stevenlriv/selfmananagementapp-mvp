<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

	//We get the data from the affirmation if we are going to edit it & We also verify that it belongs to the actual user
	//We also verify that the edit belongs to the actual data type
	if ( !empty($_GET['edit']) && get_pm($_GET['edit']) && get_pm($_GET['edit'])['id_user'] == $user['id_user']
	&& get_pm($_GET['edit'])['pm_type'] == 'Affirmation') {
		$pm = get_pm($_GET['edit']);
	}

	// Proccess all form submitions
	if ( isset($_POST['submit']) ) {

		if ( empty($_POST['pm_body_1']) ) {
			$alert['content'] = 'Please enter an affirmation.';
			$alert['type'] = 'error';
		}

		//Proccess Updates or Create New Ones
		if ( !empty($_POST['pm_to_edit']) ) {
			if ( update_pm ( $_POST['pm_to_edit'], 'pm_body_1', trim($_POST['pm_body_1']) ) ) {
				$alert['content'] = 'Great! Your updates where made.';
				$alert['type'] = 'success';
			}
			else {
				$alert['content'] = 'Oops! An error occurred, try again.';
				$alert['type'] = 'error';
			}
		}
		else {
			if ( empty($alert['content']) && new_pm ( 'Affirmation', '', '', $_POST['pm_body_1'] ) ) {
				$alert['content'] = 'Great! Your affirmation was added.';
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
		'title' => 'New Affirmation',
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

								<h4 class="page-title float-left">Edit Affirmation</h4>

							<?php
								}

								else {
							?>

								<h4 class="page-title float-left">New Affirmation</h4>

							<?php
								}
							?>

                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="/">SelfMan</a></li>
                                <li class="breadcrumb-item"><a href="/personal-affirmations.php">Affirmations</a></li>

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
										<h4 class="header-title m-t-0">Fill The Form To Add a New Affirmation</h4>
									<?php
										}
									?>

							<form action="/new-affirmation.php" data-parsley-validate novalidate method="POST">
                                <div class="p-20">
                                        <div class="form-group">
                                            <div class="form-group">
												<textarea id="textarea" name="pm_body_1" class="form-control" rows="4" parsley-trigger="change" required><?php if (!empty( $pm['pm_body_1'] )) { echo $pm['pm_body_1']; } ?></textarea>
                                            </div>

												<br />

                                            <div class="form-group text-right m-b-0">

												<button class="btn btn-primary waves-effect waves-light" name="submit" type="submit">

													<?php
														if ( !empty($pm) ) {
															echo 'Update Affirmation';
														}
														else {
															echo 'Add New Affirmation';
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
