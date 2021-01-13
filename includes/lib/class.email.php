<?php
	/**
	 * This file compiles all emails actions
	 *
	 * @category   Themes
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */
	
	function send_recover_email($to_name, $to_email, $link) {
		global $translate;
		
		$message = $translate->__("Hello")." <b>$to_name</b>:<br /><br />";
		$message = $message.$translate->__("You have requested a password change on")." <b>".get_setting(1)."</b>.<br /><br />";
		$message = $message.$translate->__("If you didn't perform this action, just skip this email and the link will expire in 24 hours.")."<br /><br />";
		$message = $message.$translate->__("To reset your password, click the following link").": <br /><br /><a href='$link' target='_blank'>$link</a>";	
		
		$subject = $translate->__("Password recover request by")." $to_name";
		$from_email = 'no-reply@'.get_host();
		
		// For development purposes
		if ( SCRIPT_LIVE != true ) {
			$from_email = $from_email.'.com';
		}
		
		if ( send_email(get_setting(1), $from_email, $to_email, $to_name, $subject, $message) ) {
			return true;
		}
		
		return false;
	}
	
	function send_contact_email ($from_name, $from_email, $phone, $comments) {
		global $translate;
		
		$link = get_domain()."/admin/contact.php?id=".get_contact($from_email)['id_contact'];
		
		$message = $translate->__("Hello").":<br /><br /><b>$from_name</b> ";
		$message = $message.$translate->__("contacted you on")." <b>".get_setting(1)."</b>.<br /><br />";
		$message = $message.$translate->__("You can contact him by email").": <b>$from_email</b> ".$translate->__("or by phone at")." <b>$phone</b>. <br /><br />";
		$message = $message.$translate->__("Read below his inquire").": <br /><br />".$comments."<br /><br />";
		$message = $message.$translate->__("To see more information, click the following link").": <br /><br /><a href='$link' target='_blank'>$link</a>";	

		$subject = $translate->__("New Inquiry Notification of")." $from_name";
		
		if ( send_email($from_name, $from_email, get_setting(23), get_setting(1), $subject, $message) ) {
			return true;
		}
		
		return false;
	}
	
	function send_email ($from_name, $from_email, $to_email, $to_name, $subject, $comments, $attachment = '') {
		if ( empty($from_name) or !is_email($from_email) or !is_email($to_email) or empty($subject) or empty($comments) ) {
			return false;
		}
		
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->CharSet = 'utf-8';
		
		// SMTP
		if ( get_setting(29) == 'true' ) {
			date_default_timezone_set('Etc/UTC');
			$mail->isSMTP();
			
			if ( SCRIPT_LIVE == true ) {
				$mail->SMTPDebug = 0;
			}
			else {
				$mail->SMTPDebug = 2;
			}
			
			$mail->Host = get_setting(30);
			$mail->Port = get_setting(31);
			
			if ( get_setting(34) == 'true' ) {
				$mail->SMTPSecure = 'tls';
			}
			
			if ( !empty(get_setting(32)) && !empty(get_setting(33)) ) {
				$mail->SMTPAuth = true;
				$mail->Username = get_setting(32);
				$mail->Password = get_setting(33);
			}
		}

		$mail->setFrom($from_email, $from_name);
		$mail->addAddress($to_email, $to_name);

		$mail->Subject = $subject;
		$mail->msgHTML($comments);
	
		if ( !empty($attachment) ) {
			$mail->addAttachment($attachment);
		}
		
		if ($mail->send()) {
			return true;
		} 
		
		return false;
	}
?>