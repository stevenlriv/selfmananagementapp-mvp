<?php 
	define  ( 'THEME_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );	
	include ( dirname(__FILE__).'/includes/lib.php' );

	if ( is_login_user() ) { 
		logout_user(); 
	}

	header ( 'Location: login.php' );
?>