<?php 

header ( 'Location: ../404.php' );

/////////////////////////
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );	
	include ( dirname(__FILE__).'/includes/lib.php' );

	//If is log in reddirect to the dashboard
	if ( is_login_user() ) { 
		header ( 'Location: index.php' );
	}
	
	if ( isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['password']) ) {
		if ( empty($_POST['remember']) ) { 
			$_POST['remember'] = ''; 
		}
		
		if ( login_user ( $_POST['email'], $_POST['password'], $_POST['remember'] ) ) {
			$alert['content'] = 'Great...Just wait a bit...';
			$alert['type'] = 'success';

			header ( 'Location: index.php' );
		}
		else {
			$alert['content'] = 'Oops! An error occurred, try again.';
			$alert['type'] = 'error';
		}
	}

	include ( dirname(__FILE__).'/includes/themes/_slimhead.php' );
?>

    <div class="account-pages"></div>
    <div class="clearfix"></div>
    <div class="wrapper-page">
						
        <div class="account-bg">
            <div class="card-box mb-0">
                <div class="text-center m-t-20">
                    <a href="/" class="logo">
                        <span>Self Management App</span>
                    </a>
                </div>
                <div class="m-t-10 p-20">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h6 class="text-muted text-uppercase m-b-0 m-t-0">Sign In</h6>
                        </div>
                    </div>
                    <form class="m-t-20" action="/login.php" method="POST">

                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="text" required="" name="email" placeholder="Email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="password" required="" name="password" placeholder="Password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <div class="checkbox checkbox-custom">
                                    <input id="checkbox-signup" type="checkbox" name="remember" value="true">
                                    <label for="checkbox-signup">
                                        Remember me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center row m-t-10">
                            <div class="col-12">
                                <button class="btn btn-success btn-block waves-effect waves-light" type="submit" name="submit">Log In</button>
                            </div>
                        </div>

                    </form>

                </div>

                <div class="clearfix"></div>
            </div>
        </div>
        <!-- end card-box-->

    </div>
    <!-- end wrapper page -->


<?php 
	include ( dirname(__FILE__).'/includes/themes/_slimfoot.php' );
?>