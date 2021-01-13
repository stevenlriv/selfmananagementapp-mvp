<?php 
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );	
	include ( dirname(__FILE__).'/includes/lib.php' );
	
	include ( dirname(__FILE__).'/includes/themes/_slimhead.php' );
?>
    <div class="account-pages"></div>
    <div class="clearfix"></div>
    <div class="wrapper-page">

        <div class="ex-page-content text-center">
            <div class="text-error">4<span class="ion-sad"></span>4</div>
            <h3 class="text-uppercase text-white font-600">Page not Found</h3>
            <p class="text-white m-t-30">
                It's looking like you may have taken a wrong turn. Don't worry... it happens to
                the best of us. You might want to check your internet connection. Here's a little tip that might
                help you get back on track.
            </p>
            <br>
            <a class="btn btn-pink waves-effect waves-light" href="/"> Return Home</a>

        </div>


    </div>
    <!-- end wrapper page -->
<?php
	include ( dirname(__FILE__).'/includes/themes/_slimfoot.php' );
?>