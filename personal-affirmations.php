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
			$alert['content'] = 'Great! Your affirmation was deleted.';
			$alert['type'] = 'success';
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	//Here we establish the query to the database
		$query = 'WHERE id_user = '.$user['id_user'].' AND pm_type = "Affirmation" ORDER BY pm_position ASC';

		//We verify if there is a pm in that section
		if( get_pm('count', $query) == 0 ) {
			$alert['content'] = 'There is no affirmations, you should add one.';
			$alert['type'] = 'info';
		}

	//Theme Header Configuration
	$array = array (
		'title' => 'Affirmations',
		'extra' => '',

		'favicon_folder' => 'Affirmation',
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
                        <h4 class="page-title float-left">Affirmations</h4>

                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">SelfMan</a></li>
                            <li class="breadcrumb-item"><a href="#">Personal Management</a></li>
                            <li class="breadcrumb-item active">Affirmations</li>
                        </ol>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

						<?php
							//The Standard Small Description Required per Section
							small_desc('AFFIRMATION',
								'In this section you can manage your Daily Affirmation routine.'
							);
						?>

						<?php
							if ( empty($user['notification_affirmation']) or  $user['notification_affirmation'] == 'OFF' ) {
						?>
							<div class="alert alert-danger alert-white alert-dismissible fade show" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                        <span aria-hidden="true">&times;</span>
			                    </button>
			                    Your Mornings Affirmation Notifications isn't set up yet. To set it up, <a href="/setup.php?q=affirmation">click here</a>.
			                    </div>
						<?php
							}
						?>

						<?php
							if ( empty($user['notification_affirmation_night']) or  $user['notification_affirmation_night'] == 'OFF' ) {
						?>
							<div class="alert alert-danger alert-white alert-dismissible fade show" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                        <span aria-hidden="true">&times;</span>
			                    </button>
			                    Your Bedtime Affirmations Notifications isn't set up yet. To set it up, <a href="/setup.php?q=affirmation_night">click here</a>.
			                    </div>
						<?php
							}
						?>

            <div class="row">

				<div class="col-12">

                        <div class="dropdown-menu dropdown-example pull-right" style="margin-bottom: 20px !important;">
                            <a class="dropdown-item" href="/new-affirmation.php"><h6 class="dropdown-header">New Affirmation</h6></a>
                        </div>

				</div>

				<div class="col-12">

                        <table class="table table-striped">
                            <thead class="thead-light">
                                <tr>
									<th>#</th>
                                    <th></th>
                                    <th>Actions</th>
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
                                            <td><?php echo $value['pm_body_1']; ?></td>
                                            <td>
												<a href="/new-affirmation.php?edit=<?php echo $value['id_pm']; ?>" class="card-link"><i class="fa fa-edit"></i> </a>
												<a href="?delete=<?php echo $value['id_pm']; ?>" class="card-link text-danger"><i class="fa fa-trash"></i></a>
											</td>
                                        </tr>
					<?php
							$i++;
						}
					?>

                            </tbody>
                        </table>

                                                            <p class="text-muted m-b-15 font-13">
                                                                **You can move around the affirmations, by dragging and dropping them. This will change the order in which they are displayed.
                                                            </p>
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
