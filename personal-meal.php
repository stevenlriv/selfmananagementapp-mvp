<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

	//Proccess the removal of a pm
	if ( !empty($_GET['delete']) && empty($_GET['confirm']) && get_pm($_GET['delete']) ) {
		$alert['content'] = 'Are you sure you want to delete it? <a href="?delete='.$_GET['delete'].'&confirm=true" class="btn btn-danger">YES, Delete It.</a>';
		$alert['type'] = 'warning';
		$alert['non-hide'] = true;
	}
	elseif ( get_pm($_GET['delete']) && !empty($_GET['delete']) && !empty($_GET['confirm']) && $_GET['confirm'] == 'true' ) {
		if (  delete_pm($_GET['delete']) ) {
			$alert['content'] = 'Great! Your meal was deleted.';
			$alert['type'] = 'success';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	//Here we establish the query to the database
		$query = 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Meal" ORDER BY pm_position ASC';

		//We verify if there is a pm in that section
		if( get_pm('count', $query) == 0 ) {
			$alert['content'] = 'There is nothing in your meal plan, you should add something.';
			$alert['type'] = 'info';
		}

	//Theme Header Configuration
	$array = array (
		'title' => 'Meal Plan',
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
                        <h4 class="page-title float-left">Meal Plan</h4>

                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">SelfMan</a></li>
                            <li class="breadcrumb-item"><a href="#">Personal Management</a></li>
                            <li class="breadcrumb-item active">Meal Plan</li>
                        </ol>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

						<?php
							//The Standard Small Description Required per Section
							small_desc('MEAL',
								'In this section you can manage your Meals Plans and get notifications on the exact DAY and TIME that you are going to eat the meal. You will need to enable the Pushover app for the notifications, to set it up <a href="/setup.php?q=pushover">click here</a>'
							);
						?>

            <div class="row">

				<div class="col-12">

						<?php
							if ( !empty($_GET['view']) && get_pm($_GET['view'])['id_user'] == $user['id_user'] && get_pm($_GET['view'])['pm_type'] == 'Meal' ) {

								$pm = get_pm($_GET['view']);
						?>

                        <div class="dropdown-menu dropdown-example pull-right " style="margin-bottom: 20px !important; margin-left: 20px !important;">
                            <a class="dropdown-item" href="/personal-meal.php"><h6 class="dropdown-header">Go Back</h6></a>
                        </div>

                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important;">
                            <a class="dropdown-item" href="/new-meal.php?edit=<?php echo $pm['id_pm']; ?>"><h6 class="dropdown-header">Edit Meal</h6></a>
                        </div>

				</div><!-- THIS DIV ONLY WORK WHILE VIEW IS ACTIVE -->

				<div class="col-12"><!-- THIS DIV ONLY WORK WHILE VIEW IS ACTIVE -->

                                <!--<div class="row">-->
                                    <!--<div class="col-md-6">-->
                                        <div class="card m-b-20 text-center">
                                            <div class="card-header">
                                                <ul class="nav nav-tabs card-header-tabs">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" href="#"><?php echo $pm['pm_body_1']; ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-body">
																							<p class="text-muted m-b-15">

																								<?php if (!empty($pm['pm_body_4']) && $pm['pm_body_4'] != 'false') { ?>
																										<span class="label label-info">Notification at <?php echo date("g:i a", strtotime($pm['pm_body_5'])); ?></span>
																								<?php } else { ?>
																										<span class="label label-danger">Inactive</span>
																								<?php } ?>

																								<?php if(!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Sun") > 0) { echo '<span class="label label-success"> Sunday </span>'; } ?>
																								<?php if(!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Mon") > 0) { echo '<span class="label label-success"> Monday </span>'; } ?>
																								<?php if(!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Tue") > 0) { echo '<span class="label label-success"> Tuesday </span>'; } ?>
																								<?php if(!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Wed") > 0) { echo '<span class="label label-success"> Wednesday </span>'; } ?>
																								<?php if(!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Thu") > 0) { echo '<span class="label label-success"> Thursday </span>'; } ?>
																								<?php if(!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Fri") > 0) { echo '<span class="label label-success"> Friday </span>'; } ?>
																								<?php if(!empty($pm['pm_body_3']) && substr_count($pm['pm_body_3'], "Sat") > 0) { echo '<span class="label label-success"> Saturday </span>'; } ?>
																							</p>

                                                <p class="card-text">
													<?php echo $pm['pm_body_2']; ?>
												</p>
                                            </div>
                                        </div>
                                    <!--</div>-->
								<!--</div>-->

						<?php
							}
							else {
						?>


                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important;">
                            <a class="dropdown-item" href="/new-meal.php"><h6 class="dropdown-header">New Meal Plan</h6></a>
                        </div>
				</div>

				<div class="col-12">

                        <table class="table table-striped">
                            <thead class="thead-light">
                                <tr>
									<th>#</th>
                                    <th></th>
                                    <th class="action">Actions</th>
                                </tr>
                            </thead>
							<tbody id="sortable_rows">
					<?php
						//$i is used to get the count of and order
						$i = 1;
						foreach ( get_pm('all', $query) as $id => $value ) {
					?>
                                        <tr plid="<?php echo $value['pm_position']; ?>" pmid="<?php echo $value['id_pm']; ?>">
                                            <th scope="row"><?php echo $i; ?></th>
																						<td>
																							<?php
																								if ($value['pm_body_4'] == 'true') {
																									echo '<span class="label label-success"> Active </span>';
																								}
																								else {
																									echo '<span class="label label-danger"> Inactive </span>';
																								}
																							?>

																							&nbsp;&nbsp;&nbsp;&nbsp;

																							<a href="?view=<?php echo $value['id_pm']; ?>"><?php echo $value['pm_body_1']; ?></a>

																						</td>
                                            <td>
												<a href="/new-meal.php?edit=<?php echo $value['id_pm']; ?>" class="card-link"><i class="fa fa-edit"></i> </a>
												<a href="?delete=<?php echo $value['id_pm']; ?>" class="card-link text-danger"><i class="fa fa-trash"></i> </a>
											</td>
                                        </tr>
					<?php
							$i++;
						}
					?>

                            </tbody>
                        </table>

                        <p class="text-muted m-b-15 font-13">
                            **<b>Click the name to see the meal plan.</b> You can move around the meals, by dragging and dropping them. This will change the order in which they are displayed.
                        </p>

						<?php
							}
						?>
                </div>
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
