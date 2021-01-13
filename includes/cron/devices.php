<?php
	if ( !defined('CRON_LOAD') ) { die ( header('Location: /404') ); }

	/**
	 * We proccess here the push-notifications from the "Insured Devices" database
	 *
	 * @send pushover or not
	 */

	 //----------------------------
		 if ( DEBUG ) {
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "Insured Devices: \n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n\n\n";
		 }
		 //----------------------------

	foreach ( get_as('all', 'WHERE as_type = "DevInsured"') as $id => $value ) {

		//We get the user data for that notification
		$user = get_user($value['id_user']);
		$actual_date = $standard_date->setTimezone(new DateTimeZone($user['user_time_zone']));

		//We define the Pushover Sound Variable for this Task
		$sound = '';

		//Here we add a cero to the date if it only contains 1 number in the hour. Count will be "00:00"
		$dateapm = $actual_date->format('H:i');
		if ( strlen($dateapm)<5  ) {
			$dateapm = '0'.$dateapm;
		}

		// This is month, day and year
		$dateday = $actual_date->format('m/d/Y');

		//We set the bool variables
		$send_now = false;
		$send_today = false;

		//SEND NOTIFICATION AT 8:00 AM (24 hour format)
		if ( $dateapm == '08:00' ) {
			$send_now = true;
		}

		//We verify if is the right day to send the notification
		//We reduce six months from the end date, to be able to send the notification 6 months NumberFormatter
		$value['as_body_4'] = new DateTime($value['as_body_4']);
		$value['as_body_4'] = $value['as_body_4']->modify('-6 months')->format('m/d/Y');

		if ( trim($value['as_body_4']) == trim($dateday) ) {
			$send_today = true;
		}

		//----------------------------
			if ( DEBUG ) {
				echo "--- --- --- --- --- --- --- ---\n";
				echo "Insured Devices: \n";
				echo "{$value['as_title']} \n";
				echo "Month For Notification --- {$value['as_body_4']} == {$dateday} \n";
				echo "--- --- --- --- --- --- --- --- \n\n\n";
			}
		//----------------------------

		/**
		 * We send the notification if the conditions have been meet
		 *
		 * Also we verify if the notification is enabled
		 */
		if ( $value['as_body_3'] == "true" && $send_today == true && $send_now == true ) {
			send_notifications ($user['id_user'], 'Insured Devices Claim', $value['as_title'], $sound);
		}
	}
?>
