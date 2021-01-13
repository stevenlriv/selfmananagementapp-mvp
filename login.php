<?php
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );

	//If is log in reddirect to the dashboard
	if ( is_login_user() ) {
		header ( 'Location: index.php' );
	}

	if ( isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['password']) ) {
		if ( login_user ( $_POST['email'], $_POST['password'], "true" ) ) {
			header ( 'Location: index.php' );
		}
		else {
			$alert['content'] = 'Oops...An error ocurred. Please try again.';
			$alert['type'] = 'error';
		}
	}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login &mdash; Self Management App</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Manage your daily task, workouts and meal plans with an app that easily tracks your progress!">

        <link href="/assets/stack/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/stack/css/stack-interface.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/stack/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/stack/css/lightbox.min.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/stack/css/flickity.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/stack/css/iconsmind.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/stack/css/jquery.steps.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/stack/css/theme.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/stack/css/custom.css" rel="stylesheet" type="text/css" media="all" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:200,300,400,400i,500,600,700%7CMerriweather:300,300i" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body class=" ">
        <a id="start"></a>
        <div class="nav-container ">
            <nav class="bar bar-4 bar--transparent bar--absolute" data-fixed-at="200">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-1 col-md-2 col-md-offset-0 col-4">
                            <div class="bar__module">
                                <a href="#">
                                    <img class="logo logo-light" alt="logo" src="/assets/stack/img/sma-logo.png" />
																		<img class="logo logo-dark" alt="logo" src="/assets/stack/img/sma-logo.png" />
                                </a>
                            </div>
                            <!--end module-->
                        </div>
                        <div class="col-lg-4 col-lg-offset-0 col-md-5 col-md-offset-0 col-8 col-offset-2">
                            <div class="bar__module">
                                <a class="btn btn--sm type--uppercase" style="background: #fff;" href="#">
                                    <span class="btn__text" style="color: #666666;">
                                        Learn More
                                    </span>
                                </a>
                                <a class="btn btn--sm btn--primary type--uppercase" href="#">
                                    <span class="btn__text">
                                        Try Beta
                                    </span>
                                </a>
                            </div>
                            <!--end module-->
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </nav>
            <!--end bar-->
        </div>
        <div class="main-container">
            <section class="imageblock switchable feature-large height-100">
                <div class="imageblock__content col-lg-6 col-md-4 pos-right">
                    <div class="background-image-holder">
                        <img alt="image" src="/assets/stack/img/bonsai-1.jpg" />
                    </div>
                </div>
                <div class="container pos-vertical-center">
                    <div class="row">
                        <div class="col-lg-5 col-md-7">
                            <h2>Self Management App</h2>
                            <p class="lead">Manage your daily task, workouts and meal plans with an app that easily tracks your progress!</p>

														<?php
																if( !empty($alert['content']) && $alert['type'] == 'error' ) {
														?>
															<div class="alert bg--error">
                                	<div class="alert__body">
                                    	<span> <?php echo $alert['content']; ?> </span>
                                	</div>
                                	<div class="alert__close">&times;</div>
                            	</div>
														<?php
																}
														?>
                            <form action="/login.php" method="POST">
                                <div class="row">
                                    <div class="col-12">
                                        <input type="email" name="email" placeholder="Email Address" required/>
                                    </div>
                                    <div class="col-12">
                                        <input type="password" name="password" placeholder="Password" required/>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="submit" class="btn btn--primary type--uppercase">Log In</button>
                                    </div>
                                    <div class="col-12">
                                        <span class="type--fine-print">By using our site, you agree to the
                                            <a href="#">Terms of Service</a>
                                        </span>
                                    </div>
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
        </div>
        <!--<div class="loader"></div>-->
        <a class="back-to-top inner-link" href="#start" data-scroll-class="100vh:active">
            <i class="stack-interface stack-up-open-big"></i>
        </a>
        <script src="/assets/stack/js/jquery-3.1.1.min.js"></script>
        <script src="/assets/stack/js/flickity.min.js"></script>
        <script src="/assets/stack/js/easypiechart.min.js"></script>
        <script src="/assets/stack/js/parallax.js"></script>
        <script src="/assets/stack/js/typed.min.js"></script>
        <script src="/assets/stack/js/datepicker.js"></script>
        <script src="/assets/stack/js/isotope.min.js"></script>
        <script src="/assets/stack/js/ytplayer.min.js"></script>
        <script src="/assets/stack/js/lightbox.min.js"></script>
        <script src="/assets/stack/js/granim.min.js"></script>
        <script src="/assets/stack/js/jquery.steps.min.js"></script>
        <script src="/assets/stack/js/countdown.min.js"></script>
        <script src="/assets/stack/js/twitterfetcher.min.js"></script>
        <script src="/assets/stack/js/spectragram.min.js"></script>
        <script src="/assets/stack/js/smooth-scroll.min.js"></script>
        <script src="/assets/stack/js/scripts.js"></script>
    </body>
</html>
