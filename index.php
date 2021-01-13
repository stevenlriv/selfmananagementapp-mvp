<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );
	include ( dirname(__FILE__).'/includes/user.php' );

	//Load to the all task, since thats the main page




	//If is log in reddirect to the dashboard
	if ( is_login_user() ) {
		header ( 'Location: all-tasks.php' );
	}

	///////////////////////////////////////////
	//Theme Header Configuration
	$array = array (
		'title' => 'Dashboard',
		'extra' => '',

		'favicon_folder' => 'general',
		'mask-icon-color' => '#5bbad5',
		'theme-color' => '#ffffff',
		'TileColor' => '#2b5797',
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
                        <h4 class="page-title float-left">Dashboard</h4>

                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Uplon</a></li>
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

						<?php
							//The Standard Small Description Required per Section
							small_desc('INDEX',
								'Welcome to the SelfManagement App, here you will be able to manage different task of your life. Click on the Menu, or On the Profile Image to start searching around'
							);

							small_desc('SLEEP',
								'Remember to set up the time that you go to sleep. Click <a href="/setup.php?q=sleep">here</a> to set it up.'
							);
						?>


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
