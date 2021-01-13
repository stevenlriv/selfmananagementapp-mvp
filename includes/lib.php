<?php
	/**
	 * This file contents all required php library
	 *
	 * @category   Library
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	include ( dirname(__FILE__).'/lib/class.functions.php' );
	include ( dirname(__FILE__).'/lib/class.settings.php' );
	include ( dirname(__FILE__).'/lib/class.CryptoLib.php' );
	include ( dirname(__FILE__).'/lib/class.cookies.php' );

	include ( dirname(__FILE__).'/lib/class.PHPMailer.php' );
	include ( dirname(__FILE__).'/lib/class.PHPMailer.SMTP.php' );
	include ( dirname(__FILE__).'/lib/class.PHPMailer.Exception.php' );
	include ( dirname(__FILE__).'/lib/class.email.php' );

	include ( dirname(__FILE__).'/lib/class.users.php' );
	include ( dirname(__FILE__).'/lib/class.upload.php' );
	include ( dirname(__FILE__).'/lib/class.image.php' );

	include ( dirname(__FILE__).'/lib/class.audits.php' );
	include ( dirname(__FILE__).'/lib/class.pm.php' );
	include ( dirname(__FILE__).'/lib/class.task.php' );
	include ( dirname(__FILE__).'/lib/class.assets.php' );
	include ( dirname(__FILE__).'/lib/class.liabilities.php' );
	
	include ( dirname(__FILE__).'/lib/class.Pushover.php' );
	include ( dirname(__FILE__).'/lib/class.notifications.php' );
	
	include ( dirname(__FILE__).'/lib/class.theme.php' );
	include ( dirname(__FILE__).'/lib/class.contact.php' );
?>
