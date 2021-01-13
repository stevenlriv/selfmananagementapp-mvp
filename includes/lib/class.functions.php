<?php
	/**
	 * This file compiles all functions
	 *
	 * @category   Functions
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	 /**
	  * Show a notification to the user on the section that he is actuali seeng
		*
		* But only of the notification cookie isn't enabled
		*/
	 function small_desc($cookie, $desc = '') {
		 	$user = is_login_user();

			if ( substr_count($user['cookies_track'], $cookie) <= 0 && $desc != '') {
		 	//if ( !get_cookie($cookie) && $desc != '') {
					echo '<div class="alert alert-info alert-dismissible fade show" role="alert" style="color: #000;">';
							echo '<button type="button" class="close close-smdc" data-dismiss="alert" small-desc="'.$cookie.'" aria-label="Close">';
									echo '<span aria-hidden="true">&times;</span>';
							echo '</button>';

							echo $desc;
					echo '</div>';
			}

 		return false;
 	}

	/**
	 * Return the dates from "00:00" to "00:00 AM/PM"
	 */
	 function readeable_time($time, $type = '') {
		 	$time = date("g:i a", strtotime($time));

 		return $time;
 	}

	/**
	 * Return the dates from "m/d/yyyy" to "Month Day, Year"
	 */
	 function readeable_date($date, $type = '') {
		 	$date = new DateTime($date);

		 	if($type == 'Year') {
				$date = $date->format('M').' '.$date->format('d').' of '.$date->format('Y');
			}
			elseif($type == 'Day') {
				$date = $date->format('d');
			}
			else {
				$date = $date->format('M').' '.$date->format('d');
			}

 		return $date;
 	}

	function is_aproveed_setup($array, $setup) {
	 foreach( $array as $id => $value ) {
		 if ( $setup == $value['q'] ) {
			 return $value;
		 }
	 }

	 return false;
 }

	 function is_aproveed_month($month) {
 		$array = array(
 			1 => 'January',
 			2 => 'February',
 			3 => 'March',
 			4 => 'April',
 			5 => 'May',
 			6 => 'June',
 			7 => 'July',
 			8 => 'August',
 			9 => 'September',
 			10 => 'October',
 			11 => 'November',
 			12 => 'December',
 		);

 		foreach( $array as $id => $value ) {
 			if ( $month == substr($value, 0, 3) ) {
 				return $value;
 			}
 		}

 		return false;
 	}

	 function is_aproveed_day($day) {
 		$array = array(
 			1 => 'Sunday',
 			2 => 'Monday',
 			3 => 'Tuesday',
 			4 => 'Wednesday',
 			5 => 'Thursday',
 			6 => 'Friday',
 			7 => 'Saturday',
 			8 => 'Year', //Name changed for display (really only the first 3 character are the ones required "Yea"
 		);

 		foreach( $array as $id => $value ) {
 			if ( $day == substr($value, 0, 3) ) {
 				return $value;
 			}
 		}

 		return false;
 	}

	function is_aproveed_cat($cat) {
	 $array = array(
		 1 => 'PERSONAL',
		 2 => 'SERVICES',
		 3 => 'DAYINLIFE',
		 4 => 'EMERGENCY',
	 );

	 foreach( $array as $id => $value ) {
		 if ( $cat == $value ) {
			 return $value;
		 }
	 }

	 return false;
 }

	/**
	 * Timezones list with GMT offset
	 *
	 * @return array
	 * @link http://stackoverflow.com/a/9328760
	 */
	function tz_list() {
		$zones_array = array();
		$timestamp = time();

		foreach(timezone_identifiers_list() as $key => $zone) {
			date_default_timezone_set($zone);
			$zones_array[$key]['zone'] = $zone;
			$zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp).' ';
		}
		return $zones_array;
	}

	function is_email($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } 
        return false;
	}

	// Get the host URL (ie. return: http://domain.com)
	function get_domain() {
		$url = base_url(TRUE);
		$url = substr($url, 0, -1);

		return $url;
	}

	// Get the host URL (ie. return: domain.com)
	function get_host() {
		$url = base_url(NULL, NULL, TRUE)['host'];

		return $url;
	}

	// Clean a String (eliminate html, php tags)
	function clean_string($str) {
		$text = strip_tags($str);

		return $text;
	}

	// Clean a TEXT or URL (return ie. this-is-an-example)
	function clean_url($str) {
		$text = $str;

		$text = str_replace('?lang=es', '', $text);
		$text = str_replace('?lang=en', '', $text);

		$text = preg_replace ( '~[^\\pL0-9]+~u', '-', $text );
		$text = trim ( $text, "-" );
		$text = iconv ( "utf-8", "us-ascii//TRANSLIT", $text );
		$text = strtolower ( $text );
		$text = preg_replace ( '~[^-a-z0-9]+~', '', $text );

		return $text;
	}

	/**
	 * Get the base URL
	 *
	 * base_url() will produce something like: http://domain.com/admin/users/
	 * base_url(TRUE) will produce something like: http://domain.com/
	 * base_url(TRUE, TRUE); || echo base_url(NULL, TRUE), will produce something like: http://domain.com/admin/
	 * base_url(NULL, NULL, TRUE) will produce something like:
	 *		array(3) {
	 *			["scheme"] => string(4) "http"
	 * 			["host"] => string(12) "domain.com"
	 *			["path"] => string(35) "/admin/users/"
	 *		}
	 */
    function base_url ( $atRoot = FALSE, $atCore = FALSE, $parse = FALSE ) {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf( $tmplt, $http, $hostname, $end );
        }
        else $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }

        return $base_url;
    }

	/**
	 * Get a relative date (ie. Today)
	 *
	 * get_relative_time($datetime, 1); //10 hours ago
	 * get_relative_time($datetime, 2); //10 hours and 50 minutes ago
	 * get_relative_time($datetime, 3); //10 hours, 50 minutes and 50 seconds ago
	 * get_relative_time($datetime, 4); //10 hours, 50 minutes and 50 seconds ago
	 *
	 * @return: date
	 */
	function get_relative_time ($datetime, $depth = 1) {
		global $translate;

		if(!ctype_digit($datetime)) {
			$datetime = strtotime($datetime);
		}

		$units = array(
			$translate->__("year") => 31104000,
			$translate->__("month") => 2592000,
			$translate->__("week") => 604800,
			$translate->__("day") => 86400,
			$translate->__("hour") => 3600,
			$translate->__("minute") => 60,
			$translate->__("second") => 1
		);

		$plural = "s";
		$conjugator = ' '.$translate->__('and').' ';
		$separator = ", ";
		$suffix1 = ' '.$translate->__('ago');
		$suffix2 = ' '.$translate->__('left');
		$now = $translate->__("now");
		$empty = "";

		$timediff = time()-$datetime;
		if ($timediff == 0) return $now;
		if ($depth < 1) return $empty;

		$max_depth = count($units);
		$remainder = abs($timediff);
		$output = "";
		$count_depth = 0;
		$fix_depth = true;

		foreach ($units as $unit=>$value) {
			if ($remainder>$value && $depth-->0) {
				if ($fix_depth) {
					$max_depth -= ++$count_depth;
					if ($depth>=$max_depth) $depth=$max_depth;
					$fix_depth = false;
				}
				$u = (int)($remainder/$value);
				$remainder %= $value;
				$pluralise = $u>1?$plural:$empty;
				$separate = $remainder==0||$depth==0?$empty:
                            ($depth==1?$conjugator:$separator);
				$output .= "{$u} {$unit}{$pluralise}{$separate}";
			}
			$count_depth++;
		}
		return $output.($timediff<0?$suffix2:$suffix1);
	}
?>
