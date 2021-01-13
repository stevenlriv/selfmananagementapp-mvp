<?php
	if ( !defined('CRON_LOAD') ) { die ( header('Location: /404') ); }

	/**
	 * We proccess here the push-notifications from the "USER" database
	 *
	 * @send pushover or not
	 */

	 //----------------------------
		 if ( DEBUG ) {
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "USERS: \n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "--- --- --- --- --- --- --- ---\n\n\n";
		 }
		 //----------------------------

	foreach ( get_user('all') as $id => $value ) {

		//----------------------------
 		 if ( DEBUG ) {
			 echo "--- --- --- --- --- --- --- ---\n";
			 echo "USER-{$value['fullname']}: \n";
			 echo "--- --- --- --- --- --- --- ---\n\n\n";
		}
		
		//We get the data for that notification
		$actual_date = $standard_date->setTimezone(new DateTimeZone($value['user_time_zone']));


		//We define the Pushover Sound Variable for this Task
		$sound = '';

		//Here we add a cero to the date if it only contains 1 number in the hour. Count will be "00:00"
		$dateapm = $actual_date->format('H:i');
		if ( strlen($dateapm)<5  ) {
			$dateapm = '0'.$dateapm;
		}

		// This is todays Day, meaning Sunday, Monday, ETC (with just the first 3 letters)
		$dateday = $actual_date->format('D');

		//HERE WE PROCCESS THE LOOP FOR THE DIFFERENTS Notification

		foreach( $users_notifications as $id => $child ) {

			//We set the bool variables
			$send_now = false;
			$send_today = false;

			//We get the data and if there is none, we set it to default
			if(!empty($value[$child['database']]) && $value[$child['database']] != 'OFF' && $value[$child['database']] != 'DISABLE') {
					$array_data = explode(';', $value[$child['database']]);
					$st['days'] = $array_data[0];
					$st['time'] = $array_data[1];
			}
			else {
				$st['days'] = '';
				$st['time'] = '';
			}

				//We verify if is the right time to send the notification
				if ( $dateapm == $st['time'] ) {
					$send_now = true;
				}

				//We verify if is the right day to send the notification
				if ( substr_count($st['days'], $dateday) > 0 ) {
					$send_today = true;
				}

				//----------------------------
					if ( DEBUG ) {
						echo "--- --- --- --- --- --- --- ---\n";
						echo $child['name'].": \n";
						echo "TIME --- {$st['time']} == {$dateapm} \n";
						echo "Days Active --- {$st['days']} == {$dateday} \n";
					}
				//----------------------------

				/**
				 * We send the notification if the conditions have been meet
				 *
				 * Also we verify if the notification is enabled
				 */
				if ( $send_today == true && $send_now == true ) {
					//----------------------------
						if ( DEBUG ) {
							echo "Sending...".$child['name'].": \n";
						}
					//----------------------------
					send_notifications ($value['id_user'], 'Daily Routine', $child['notification'], $sound);
				}

				//----------------------------
					if ( DEBUG ) {
						echo "--- --- --- --- --- --- --- --- \n\n\n";
					}
				//----------------------------
 		 }
	}
?>
