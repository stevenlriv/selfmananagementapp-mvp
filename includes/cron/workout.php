<?php
	if ( !defined('CRON_LOAD') ) { die ( header('Location: /404') ); }

	/**
	 * We proccess here the push-notifications from the "WORKOUTS" database
	 *
	 * @send pushover or not
	 */

	 //----------------------------
		 if ( DEBUG ) {
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "WORKOUTS: \n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n\n\n";
		 }
		 //----------------------------

	foreach ( get_pm('all', 'WHERE pm_type = "Workouts"') as $id => $value ) {

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

		// This is todays Day, meaning Sunday, Monday, ETC (with just the first 3 letters)
		$dateday = $actual_date->format('D');

		//We set the bool variables
		$send_now = false;
		$send_today = false;

		//We verify if is the right time to send the notification
		if ( $dateapm == $value['pm_body_5'] ) {
			$send_now = true;
		}

		//We verify if is the right day to send the notification
		if ( substr_count($value['pm_body_3'], $dateday) > 0 ) {
			$send_today = true;
		}

		//----------------------------
			if ( DEBUG ) {
				echo "--- --- --- --- --- --- --- ---\n";
				echo "Workout: \n";
				echo "{$value['pm_body_1']} \n";
				echo "TIME --- {$value['pm_body_5']} == {$dateapm} \n";
				echo "Days Active --- {$value['pm_body_3']} == {$dateday} \n";
				echo "--- --- --- --- --- --- --- --- \n\n\n";
			}
		//----------------------------

		/**
		 * We send the notification if the conditions have been meet
		 *
		 * Also we verify if the notification is enabled
		 */
		if ( $value['pm_body_4'] == "true" && $send_today == true && $send_now == true ) {
			send_notifications ($user['id_user'], 'Workout', $value['pm_body_1'], $sound);
		}
	}
?>
