<?php
	/**
	 * This file compiles all notifications actions
	 *
	 * @category   Themes
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	function send_notifications ($id_user, $title, $message, $sound = '') {
		$push = new Pushover();

		$standard_date = new DateTime();

		//We get the user data for that notification
		$user = get_user($id_user);
		$actual_date = $standard_date->setTimezone(new DateTimeZone($user['user_time_zone']));

		// We first check if that user has a "push_user_token", if not we skip the user
		if( $user['push_user_token'] == '' ) {
			return;
		}

		//Incase the user has a specific APP token
		if ( $user['push_app_token'] != '' ) {
				$push->setToken($user['push_app_token']);
		}
		else {
				$push->setToken(get_setting(2));
		}

		$push->setUser($user['push_user_token']);

		$push->setDebug(true);
		$push->setTimestamp(time());

		$push->setTitle($title);
		$push->setMessage($message);

		if ($sound != '') {
			$push->setSound($sound);
		}

		#$push->setUrl('http://chris.schalenborgh.be/blog/');
		#$push->setUrlTitle('cool php blog');

		#$push->setDevice('iPhone');
		#$push->setPriority(2);
		#$push->setRetry(60); //Used with Priority = 2; Pushover will resend the notification every 60 seconds until the user accepts.
		#$push->setExpire(3600); //Used with Priority = 2; Pushover will resend the notification every 60 seconds for 3600 seconds. After that point, it stops sending notifications.
		#$push->setCallback('http://chris.schalenborgh.be/');

		//Before sending it, we verify if the user notifications is enabled
		//And if the user is active (status == published)
		if ($user['notifications'] == "OFF" or $user['status'] != 'PUBLISHED') {
			return false;
		}

		if ($push->send()) {
			return true;
		}

		return false;
	}

/*
    function send_test_notifications () {
        curl_setopt_array($ch = curl_init(), array(
            CURLOPT_URL => "https://api.pushed.co/1/push",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
	            "app_key" => "pRpsVJim73XV5RIUBJzw",
	            "app_secret" => "FnfYAN8LBUmRqjM75UjtAMWoxXEduMtKRQmVB6LlaQ5PsNMkuVeZdW5Z17PKRS8r",
	            "target_type" => "app",
	            "content" => "This is a test from Self Management App."


	"access_token" => "access_token_code_given_by_pushed",
	"target_type" => "user",
	"target_alias" => "your_channel_alias",
	"content" => "Your notification content."
            ),
            CURLOPT_SAFE_UPLOAD => true,
            CURLOPT_RETURNTRANSFER => true
        ));
        curl_exec($ch);
        curl_close($ch);
    }
*/
?>
