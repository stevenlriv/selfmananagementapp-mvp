<?php
/**
 * Here we establish the array for general use of user notifications
 */

	//Approved Setups
	$users_notifications = array(
		1 => array(
					'q' => 'pushover',
					'name' => 'Push Over',
				),

		2 => array(
					'q' => 'daily',
					'name' => 'Daily Accountability',

					'database' => 'notification_daily',
					'notification' => 'Perform Daily Accountability',
					),

		3 => array(

							'q' => 'sleep',
							'name' => 'Bed Time',

							'database' => 'notification_sleep',
							'notification' => 'Go to Bed',
					),

					4 => array(
								'q' => 'awake',
								'name' => 'Wake Up',

								'database' => 'notification_awake',
								'notification' => 'Wake Up',
								),
	);

//Defined cron loads, lets the app know that is being access by the cron file
if( is_login_user()['privilege'] == 2657 OR defined('CRON_LOAD') ) {

	$users_notifications = array(
		1 => array(
					'q' => 'pushover',
					'name' => 'Push Over',
				),

		2 => array(
					'q' => 'expenses',
					'name' => 'Expenses',

 					'database' => 'notification_expenses',
 					'notification' => 'Track expenses of the day',
				),

		3 => array(
					'q' => 'affirmation',
					'name' => 'Morning Affirmations',

 					'database' => 'notification_affirmation',
 					'notification' => 'Morning Affirmations',
				),

		4 => array(
					'q' => 'affirmation_night',
					'name' => 'Bedtime Affirmations',

		 			'database' => 'notification_affirmation_night',
		 			'notification' => 'Bedtime Affirmations',
					),

		/*5 => array(
					'q' => 'weight',
					'name' => 'Weight Tracking',

 					'database' => 'notification_weight',
 					'notification' => 'Weight Yourself',
				),*/

		6 => array(
					'q' => 'readinglist',
					'name' => 'Reading List',

 					'database' => 'notification_readinglist',
 					'notification' => 'Reading/Learning Session',

					),
		7 => array(
					'q' => 'daily',
					'name' => 'Daily Accountability',

					'database' => 'notification_daily',
					'notification' => 'Perform Daily Accountability',
					),
		/*8 => array(
					'q' => 'finance',
					'name' => 'Finance Management Audit',

					'database' => 'notification_finance',
					'notification' => 'Perform Finance Management Audit',
				),*/

				9 => array(
							'q' => 'sleep',
							'name' => 'Bed Time',

							'database' => 'notification_sleep',
							'notification' => 'Go to Bed',
							),

							10 => array(
										'q' => 'awake',
										'name' => 'Wake Up',

										'database' => 'notification_awake',
										'notification' => 'Wake Up',
										),
	);
}
?>
