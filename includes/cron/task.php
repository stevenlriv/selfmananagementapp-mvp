<?php
	if ( !defined('CRON_LOAD') ) { die ( header('Location: /404') ); }

	/**
	 * We proccess here the push-notifications from the "TASK" database
	 *
	 * @send pushover or not
	 */

	 //----------------------------
		 if ( DEBUG ) {
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "TASKS: \n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n\n\n";
		 }
		 //----------------------------

	foreach ( get_task('all') as $id => $value ) {

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

		/**
		 * First we verify if is a yearly task, if its a yearly one
		 */
		if ( substr_count($value['task_weekdays'], 'Yea') > 0 ) {
			//We add a different sound to the YEARLY task
			$sound = 'echo';

			//SEND NOTIFICATION AT 8:00 AM for yearly task (24 hour format)
			if ( $dateapm == '08:00' ) {
				$send_now = true;
			}

			//We verify if is the right day to send the notification
			if ( trim($value['task_time']) == trim($actual_date->format('m/d')) ) {
				$send_today = true;
			}

			//----------------------------
				if ( DEBUG ) {
					echo "--- --- --- --- --- --- --- ---\n";
					echo "YEARLY TASK: \n";
					echo "{$value['task']} \n";
					echo "DATE --- {$value['task_time']} == {$actual_date->format('m/d')} \n";
					echo "--- --- --- --- --- --- --- --- \n\n\n";
				}
			//----------------------------

		}
		else {

			//We verify if is the right time to send the notification
			if ( $dateapm == $value['task_time'] ) {
				$send_now = true;
			}

			//We verify if is the right day to send the notification
			if ( substr_count($value['task_weekdays'], $dateday) > 0 ) {
				$send_today = true;
			}

			//----------------------------
				if ( DEBUG ) {
					echo "--- --- --- --- --- --- --- ---\n";
					echo "Daily Task: \n";
					echo "{$value['task']} \n";
					echo "TIME --- {$value['task_time']} == {$dateapm} \n";
					echo "Days Active --- {$value['task_weekdays']} == {$dateday} \n";
					echo "--- --- --- --- --- --- --- --- \n\n\n";
				}
			//----------------------------
		}

		/**
		 * We send the notification if the conditions have been meet
		 *
		 * Also we verify if the notification is enabled
		 */
		if ( $value['task_push'] == "true" && $send_today == true && $send_now == true ) {
			send_notifications ($user['id_user'], $value['task_title'], $value['task'], $sound);
		}
	}
?>
