<?php
	/**
	 * The mission of this file is to add every detail from every option and organize them into array of ours, so it can be displayed in the daily audit
	 */
	if ( !defined('AUDIT_LOAD') ) { die ( header('Location: /404') ); }

	define ('DEBUG', false);

	// Here we include the user notifications array
	include ( dirname(__FILE__).'/user-notifications.php' );

	$user = is_login_user();
 	$actual_date = new DateTime($user['user_time_zone']);

	$audit = array();

	$dateday = $actual_date->format('D');

	//THE ARRAYS FORMAT WILL BE THE FOLLOWING "$audit[HOUR]"

	/**
	 * We proccess here the "TASK" database
	 *
	 *
	 */
	 foreach ( get_task('all', 'WHERE id_user ='.$user['id_user']) as $id => $value ) {
				//We only get the first two hour numbers
				$value['task_time'] = substr($value['task_time'], 0, -3);

				//We only get today tasks
				if ( substr_count($value['task_weekdays'], $dateday) > 0 ) {

						//We add the info to the arrays
						$audit[$value['task_time']][] = $value['task'];
				}
	}

	////////////////////

	 /**
 	 * We proccess here the "USER" database
 	 *
 	 *
 	 */

	 foreach ( get_user('all', 'WHERE id_user ='.$user['id_user']) as $id => $value ) {

		foreach( $users_notifications as $id => $child ) {

			//We get the data and if there is none, we set it to default
			if(!empty($value[$child['database']]) && $value[$child['database']] != 'OFF') {
					$array_data = explode(';', $value[$child['database']]);
					$st['days'] = $array_data[0];
					$st['time'] = $array_data[1];

			}
			else {
				$st['days'] = '';
				$st['time'] = '';
			}

				//We only get the first two hour numbers
	 		 $st['time'] = substr($st['time'], 0, -3);

				//We verify if is the right day to send the notification
				if ( substr_count($st['days'], $dateday) > 0 ) {
					//We add the info to the arrays
 				 $audit[$st['time']][] = $child['name'];
				}

 		 }
	}

	/**
	 * We proccess here the "WORKOUTS" database
	 *
	 *
	 */
	 foreach ( get_pm('all', 'WHERE id_user ='.$user['id_user'].' AND pm_type = "Workouts"') as $id => $value ) {
		 //We only get the first two hour numbers
		 $value['pm_body_5'] = substr($value['pm_body_5'], 0, -3);

		 //We only get today tasks
		 if ( substr_count($value['pm_body_3'], $dateday) > 0 ) {

				 //We add the info to the arrays
				 //So for now on the audit the workout are pre check with a check box, there is no need to add it HERE
				 //So we comment it out
				 $audit[$value['pm_body_5']][] = $value['pm_body_1'];
		 }
	}

	 /**
 	 * We proccess here the "MEAL" database
 	 *
 	 *
 	 */
	 foreach ( get_pm('all', 'WHERE id_user ='.$user['id_user'].' AND pm_type = "Meal"') as $id => $value ) {
		 //We only get the first two hour numbers
		 $value['pm_body_5'] = substr($value['pm_body_5'], 0, -3);

		 //We only get today tasks
		 if ( substr_count($value['pm_body_3'], $dateday) > 0 ) {

			 //We add the info to the arrays
			 //So for now on the audit the meals are pre check with a check box, there is no need to add it HERE
			 //So we comment it out
			$audit[$value['pm_body_5']][] = $value['pm_body_1'];
		 }
	}

	 /**
 	 * Here we account for SLEEP Time
 	 *
 	 *
 	 *
	$audit['00'][] = 'SLEEP';
	$audit['01'][] = 'SLEEP';
	$audit['02'][] = 'SLEEP';
	$audit['03'][] = 'SLEEP';
	$audit['04'][] = 'SLEEP';

	if ( substr_count('Sun', $dateday) > 0 ) {
		$audit['05'][] = 'SLEEP';
	}*/

	//DEGUG ARRAYS

	 if ( DEBUG ) {
		 	print_r($audit);
	 }
?>
