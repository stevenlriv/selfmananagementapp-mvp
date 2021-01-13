<?php
/**
 * Just the user General House Keeping
 */

 	// Only Access To logged in users, to any page linked to this file
 	if ( !is_login_user() ) {
	 	header ( 'Location: login.php' );
 	}

 	$user = is_login_user();
 	$actual_date = new DateTime($user['user_time_zone']);

	//We Notify if the user does not have the Pushover app Enabled
	 if ( empty($user['push_user_token']) ) {
		 $alert['non-hide'] = true;
		 $general_alert['type'] = 'error';
		 $general_alert['content'] = 'Your Pushover is not set up at the moment, and the notifications are not going to be sent. You will need to add your <b>user token</b> in your account settings. To set it up <a href="/setup.php?q=pushover">click here</a>';
		}
?>
