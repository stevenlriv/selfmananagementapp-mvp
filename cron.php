<?php
	/**
	 * This Cron File is set to be executed every single minute
	 *
	 * Setting up the Cron Jobs on the command line for every minute
	 *	1) crontab -e
	 *	2) * * * * * php /var/www/html/cron.php
     *  2) * * * * * php /home/dh_qne97v/selfmanagementapp.com/cron.php
	 *
	 * Set debug to true to be able to see the cron file proccess
	 */
	define ('DEBUG', true);

	define  ( 'CRON_LOAD', true );
	include ( dirname(__FILE__).'/includes/configuration.php' );
	include ( dirname(__FILE__).'/includes/lib.php' );

	/**
	 * We set the time vars here, so they are outside of the loop and gets loaded specifically only when the script is
	 * loaded for the first time, so it does not matter how long it takes for the file to execute, the standard
	 * variables required are going to stay constant
	 */
	$standard_date = new DateTime();

	/**
	 * We proccess here the push-notifications from the "TASK" database
	 *
	 * @send pushover or not
	 */
	 include ( dirname(__FILE__).'/includes/cron/task.php' );

	 /**
 	 * We proccess here the push-notifications from the "USER" database
 	 *
 	 * @send pushover or not
 	 */
	include ( dirname(__FILE__).'/includes/user-notifications.php' );
 	include ( dirname(__FILE__).'/includes/cron/user.php' );

	/**
	 * We proccess here the push-notifications from the "WORKOUTS" database
	 *
	 * @send pushover or not
	 */
	 include ( dirname(__FILE__).'/includes/cron/workout.php' );

	 /**
 	 * We proccess here the push-notifications from the "MEAL" database
 	 *
 	 * @send pushover or not
 	 */
 	 include ( dirname(__FILE__).'/includes/cron/meal.php' );

	 /**
 	 * We proccess here the push-notifications from the "INSURED DEVICES" database
 	 *
 	 * @send pushover or not
 	 */

 	 include ( dirname(__FILE__).'/includes/cron/devices.php' );

	 /**
 	 * We proccess here the push-notifications from the "INSURANCE" database
 	 *
 	 * @send pushover or not
 	 */

 	 include ( dirname(__FILE__).'/includes/cron/insurance.php' );
?>
