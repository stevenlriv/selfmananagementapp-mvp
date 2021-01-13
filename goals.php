<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

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

	//Proccess the removal of a pm
	if ( !empty($_GET['delete']) && empty($_GET['confirm']) && get_pm($_GET['delete']) ) {
		$alert['content'] = 'Are you sure you want to delete it? <a href="?delete='.$_GET['delete'].'&confirm=true'.$get_url.'" class="btn btn-danger">YES, Delete It.</a>';
		$alert['type'] = 'warning';
		$alert['non-hide'] = true;
	}
	elseif ( get_pm($_GET['delete']) && !empty($_GET['delete']) && !empty($_GET['confirm']) && $_GET['confirm'] == 'true' ) {
		if (  delete_pm($_GET['delete']) ) {
			$alert['content'] = 'Great! Your goal was deleted.';
			$alert['type'] = 'success';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	//Proccess of mark as completed
	elseif ( !empty($_GET['completed']) && empty($_GET['confirm']) && get_pm($_GET['completed']) ) {
		$alert['content'] = 'Are you sure you want to mark it as completed? <a href="?completed='.$_GET['completed'].'&confirm=true'.$get_url.'" class="btn btn-success">YES.</a>';
		$alert['type'] = 'warning';
		$alert['non-hide'] = true;
	}
	elseif ( get_pm($_GET['completed']) && !empty($_GET['completed']) && !empty($_GET['confirm']) && $_GET['confirm'] == 'true' ) {
		if (  update_pm($_GET['completed'], 'pm_status', 'COMPLETED') ) {
			update_pm ( $_GET['completed'], 'pm_body_5', $actual_date->format('d').' '.$actual_date->format('M').' '.$actual_date->format('Y'));
			update_pm ( $_GET['completed'], 'pm_body_6', time());
			$alert['content'] = 'Great! Your goal was completed.';
			$alert['type'] = 'success';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	//Here we establish the query to the database
	$extraquery = 'AND pm_status = "PUBLISHED"';

	if(!empty($_GET['arch']) && $_GET['arch'] == 'logs') {
		$extraquery = 'AND pm_status = "COMPLETED"';
	}

		$query = 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Goals" '.$extraquery.' AND pm_body_3 = "'.$goal_type.'" ORDER BY pm_position ASC';

		//We verify if there is a pm in that section
		if( get_pm('count', $query) == 0 ) {
			$alert['content'] = 'There is nothing in your goals list, you should add something.';
			$alert['type'] = 'info';
		}

	//Theme Header Configuration
	$array = array (
		'title' => 'Goals List',
		'extra' => '',

		'favicon_folder' => 'goal',
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
												<?php
													if(!empty($_GET['q']) && $_GET['q'] == "long") {
													}
													else {
												 ?>
                        <h4 class="page-title float-left">Short Term Goals</h4>
												<?php
													}
												 ?>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">SelfMan</a></li>
                            <li class="breadcrumb-item"><a href="#">List</a></li>
                            <li class="breadcrumb-item active">Goals List</li>
                        </ol>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

						<?php
							//The Standard Small Description Required per Section
							small_desc('GOALS',
								'In this section you can manage your goals list.'
							);
						?>

            <div class="row">

				<div class="col-12">

						<?php
							if ( !empty($_GET['view']) && get_pm($_GET['view'])['id_user'] == $user['id_user'] && get_pm($_GET['view'])['pm_type'] == 'Goals' ) {

								$pm = get_pm($_GET['view']);
						?>

                        <div class="dropdown-menu dropdown-example pull-right " style="margin-bottom: 20px !important; margin-left: 20px !important;">
                            <a class="dropdown-item" href="/goals.php?<?php echo $get_url; ?>"><h6 class="dropdown-header">Go Back</h6></a>
                        </div>

                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important;">
                            <a class="dropdown-item" href="/new-goal.php?edit=<?php echo $pm['id_pm'].$get_url; ?>"><h6 class="dropdown-header">Edit Goals</h6></a>
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


                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important; margin-left: 20px !important;">
                            <a class="dropdown-item" href="/new-goal.php?<?php echo $get_url; ?>"><h6 class="dropdown-header">New Goal</h6></a>
                        </div>

												<?php
													if(!empty($_GET['arch']) && $_GET['arch'] == 'logs') {
												?>

						                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important;">
						                            <a class="dropdown-item" href="/goals.php"><h6 class="dropdown-header">Go Back</h6></a>
						                        </div>

												<?php
													}
													else {
												?>

						                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important;">
						                            <a class="dropdown-item" href="?arch=logs<?php echo $get_url; ?>"><h6 class="dropdown-header">View Completed Goals</h6></a>
						                        </div>


												<?php
													}
												?>
				</div>

				<div class="col-12">

                        <table class="table table-striped">
                            <thead class="thead-light">
                                <tr>
									<th>#</th>
                                    <th></th>
																		<th>Category</th>
																		<th>Date Created</th>

																		<?php
																			if(!empty($_GET['arch']) && $_GET['arch'] == 'logs') {
																				echo "<th>Date Completed</th>";
																			}
																		 ?>
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

																						<?php
																							//if there is content
																							if ($value['pm_body_2']!='') {
																						 ?>
                                            <td><a href="?view=<?php echo $value['id_pm'].$get_url; ?>"><?php echo $value['pm_body_1']; ?></a></td>
																						<?php
																							}
																							else {
																						 ?>
																						 <td><?php echo $value['pm_body_1']; ?></td>
																						 <?php
																					 		}
																						  ?>

																						<td><span class="label label-default"><?php echo $value['pm_body_4']; ?></span></td>
																						<td><?php echo $value['pm_query_date']; ?></td>

																						<?php
																							if(!empty($_GET['arch']) && $_GET['arch'] == 'logs') {
																								echo "<td>";
																									echo $value['pm_body_5'];
																								echo "</td>";
																							}
																						 ?>

                                            <td>
																							<?php
																								if( $value['pm_status'] != 'COMPLETED' ) {
																							?>
																							<a href="?completed=<?php echo $value['id_pm'].$get_url; ?>" class="card-link text-success"><i class="fa fa-check-square-o"></i> </a>

																																			<a href="/new-goal.php?edit=<?php echo $value['id_pm']; ?>" class="card-link"><i class="fa fa-edit"></i> </a>
																							<?php
																								}
																							?>


												<a href="?delete=<?php echo $value['id_pm'].$get_url; ?>" class="card-link text-danger"><i class="fa fa-trash"></i> </a>
											</td>
                                        </tr>
					<?php
							$i++;
						}
					?>

                            </tbody>
                        </table>

                        <p class="text-muted m-b-15 font-13">
                            **<b>Click the goal to see the description.</b> You can move around the goals, by dragging and dropping them. This will change the order in which they are displayed.
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
