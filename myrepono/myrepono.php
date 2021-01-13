<?PHP
/*

myRepono Backup API
http://myRepono.com/
Copyright 2017 ionix Limited

By using this code you agree to indemnify ionix Limited and myRepono from any liability that might arise from its use. Selling the code for this program without prior written consent is expressly forbidden. Obtain permission before redistributing this program over the Internet or in any other medium. In all cases this copyright header must remain intact.  We cannot be held responsible for any harm this may cause.

We advise against modification of this file without prior discussion with ionix Limited as modification is likely to affect the functionality of this API and the ability for the myRepono.com systems to interact with it.

myRepono Terms of Service: 		http://myRepono.com/terms/
myRepono Privacy Policy: 		http://myRepono.com/privacy/
myRepono Documentation & FAQ: 	http://myRepono.com/faq/
myRepono Support: 				http://myRepono.com/contact/

*/


// NOTE: Configuration may be ignored if myrepono_config.php file exists.
// CONFIGURATION


$client_key = "YJ00-1MA6-R76E-58ZE";				// API key for authentication.
$client_password = "h4v75uobh0phjiuqfm3";		// API password for authentication.

$api_packet_filesize = "7";						// Maximum backup packet filesize (in MB).
$api_packet_files = "250";						// Maximum files per backup packet.
$api_mysql_packet_size = "1";					// Maximum SQL backup packet filesize (in MB).
$api_max_file_filesize = "7";					// Maximum filesize that can be backed up without splitting (in MB).
$api_max_files_per_process = "250";				// No. of files/directories to index per process.
$api_max_http_filesize = "64";					// Maximum filesize that can be transferred via HTTP rather than FTP (in MB).
$api_timeout = "60";							// API timeout limit (in minutes, max. 180), cannot be set if PHP safe_mode is on (set to 0 to disable).
$api_memory = "256";							// API memory limit for backup processes (in MB), cannot be set on all systems (set to 0 to disable).

$api_create_htaccess = "0";						// Create .htaccess file in API directory to limit access by IP.
$api_create_data_htaccess = "1";				// Create .htaccess file in API data directory to limit all access.
$api_mcrypt_rijndael = "0";						// Enable 256-Bit Rijndael/AES encryption of backup packets.
$api_myrepono_https = "0";						// Connections use HTTPS protocol (1 = Yes, 0 = No).
$api_recursive_filesize = "1";					// Enable recursive directory filesize calculations.
$api_ip_authenticate = "1";						// Enable IP address authentication checks (1 = Yes, 0 = No).

$api_allow_exec = "1";							// Allow usage of PHP exec() function, and therefore mysqldump, split and csplit.
$api_mysqldump = "1";							// Enable mysqldump for mySQL database backups (automatically disabled if not supported).
$api_mysqldump_path = "mysqldump";				// Path to mysqldump command-line function.
$api_csplit = "1";								// Enable csplit for splitting SQL files over 2GB (automatically disabled if not supported).
$api_csplit_path = "csplit";					// Path to csplit command-line function.
$api_split = "1";								// Enable split for splitting files over 2GB (automatically disabled if not supported).
$api_split_path = "split";						// Path to split command-line function.
$api_allow_mysqli = "1";						// Allow usage of PHP mySQLi extension rather than mySQL (automatically disabled if not supported).
$api_allow_chmod = "1";							// Allow usage of PHP chmod() function to adjust file permissions.
$api_force_curl = "0";							// Force usage of CURL extension if allow_url_fopen not 'on'.

$api_data_directory = "data";					// Name of API data directory within API directory, or full path to API data directory (no trailing slash).

$enable_debug_log = "0";						// Enable debug log (warning, creates a large quantity of log files in API data directory).
$enable_error_log = "1";						// Enable PHP error logging (creates 'error.log' file in API directory).
$enable_peak_memory_log = "0";					// Enable peak memory usage logging (creates 'memory.log' file in API data directory)


// END CONFIGURATION


if (function_exists('ini_set')) {
	ini_set('error_reporting', E_ALL | E_STRICT);
	ini_set('display_errors', 'Off');
	if ($enable_error_log=="1") {
		ini_set('log_errors', 'On');
		ini_set('error_log', 'error.log');
	} else {
		ini_set('log_errors', 'Off');
	}
}

if (file_exists(dirname(__FILE__)."/myrepono_config.php")) {
	require_once(dirname(__FILE__)."/myrepono_config.php");
}

if (function_exists('mb_internal_encoding')) {
	mb_internal_encoding("UTF-8");
}

if (function_exists('mb_http_output')) {
	mb_http_output("UTF-8");
}

if (($client_key=="") || ($client_password=="")) {
	print "0";
	exit;
}

$api_version = "2.3";
$api_data_directory = str_replace('\\','/',$api_data_directory);
$api_data_directory = str_replace('//','/',$api_data_directory);
if ((basename($api_data_directory)==$api_data_directory) && (stristr($api_data_directory,'/')===false)) {
	$api_data_directory = dirname(__FILE__)."/".$api_data_directory;
}
$api_data_directory = str_replace('\\','/',$api_data_directory);
$api_data_directory = str_replace('//','/',$api_data_directory);
$api_packet_filesize = ($api_packet_filesize * 1024) * 1024;
$api_mysql_packet_size = ($api_mysql_packet_size * 1024) * 1024;
$api_max_file_filesize = ($api_max_file_filesize * 1024) * 1024;
$api_max_http_filesize = ($api_max_http_filesize * 1024) * 1024;
$api_packet_files--;
$api_filetree_base_identifier = "MYR-G6-DIRS-W7X5Y3Z1";
$api_filetree_files_identifier = "MYR-P4-FILS-D7C5B3A1";
$api_libcurlemu = "0";
$api_read_length = "262144";
$api_use_mysql_list_tables = "0";
if ($api_myrepono_https=="2") {
	$api_myrepono_https = "0";
}

if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('America/New_York');
	// Do not change timezone - synchronised with myRepono system.
}

$envip = "";
if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
	$envip = $_SERVER['HTTP_CF_CONNECTING_IP'];
}
if (($envip=="") && (isset($_SERVER['HTTP_CLIENT_IP']))) {
	$envip = $_SERVER['HTTP_CLIENT_IP'];
}
if (($envip=="") && (isset($_SERVER['HTTP_X_FORWARDED_FOR']))) {
	$envip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	if (stristr($envip,", ")) {
		$envip = explode(", ", $envip);
		$envip = $envip[0];
	}
}
if (($envip=="") && (function_exists('getenv'))) {
	$envip = getenv('REMOTE_ADDR');
}
if (($envip=="") && (isset($_ENV['REMOTE_ADDR']))) {
	$envip = $_ENV['REMOTE_ADDR'];
}
if (($envip=="") && (isset($_SERVER['REMOTE_ADDR']))) {
	$envip = $_SERVER['REMOTE_ADDR'];
}
if (stristr($envip,":")) {
	$envip_explode = explode(":", $envip);
	if (count($envip_explode)<6) {
		$envip = $envip_explode[0];
	}
}
$ip_authenticated = "0";

if (($api_ip_authenticate=="1") && ($envip!="")) {

	$allow_ips = "";
	$using_ip_cache = "0";
	if (file_exists($api_data_directory."/CACHE-IP.tmp")) {
		if (@filemtime($api_data_directory."/CACHE-IP.tmp")>time()-900) {
			if ($allow_ips = @file_get_contents($api_data_directory."/CACHE-IP.tmp")) {
				$using_ip_cache = "1";
			}
		}
	}

	if (($using_ip_cache!="1") || (trim($allow_ips)=="")) {

		if ($api_myrepono_https=="1") {
			$allow_ips = myr_connect("https://myrepono.com/sys/ip/");
		} else {
			$allow_ips = myr_connect("http://myrepono.com/sys/ip/");
		}

		if ($allow_ips!="") {
			if ($fh = @fopen($api_data_directory."/CACHE-IP.tmp", 'w')) {
				fwrite($fh, $allow_ips);
				fclose($fh);
			}
		}
	}

	if ($allow_ips!="") {

		$allow_ips = explode("\n",$allow_ips);
		$htaccess_file = "order deny,allow\ndeny from all\n";

		for ($i=0; $i<count($allow_ips); $i++) {

			$allow_ips[$i] = str_replace(" ","",$allow_ips[$i]);

			if ($allow_ips[$i]!="") {
				if ($allow_ips[$i]==$envip) {

					$ip_authenticated = "1";

				}
				$htaccess_file .= "allow from ".$allow_ips[$i]."\n";
			}
		}

		if ($ip_authenticated=="1") {
			if ($api_create_htaccess=="1") {
				if (@filemtime(dirname(__FILE__)."/.htaccess")<time()-3600) {
					if ($fh = @fopen(dirname(__FILE__)."/.htaccess", 'w')) {
						fwrite($fh, $htaccess_file);
						fclose($fh);
					}
				}
			}

			if ($api_create_data_htaccess=="1") {
				if (!file_exists($api_data_directory."/.htaccess")) {
					if ($fh = @fopen($api_data_directory."/.htaccess", 'w')) {
						fwrite($fh, "order allow,deny\ndeny from all\nOptions -ExecCGI\nRemoveHandler .php .phtml .php3 .php4 .php5 .php6\nRemoveType .php .phtml .php3 .php4 .php5 .php6\n<IfModule mod_php5.c>\nphp_admin_flag engine off\n</IfModule>");
						fclose($fh);
					}
				}
			}
		}
	}

} elseif ($api_ip_authenticate=="0") {

	$ip_authenticated = "1";

}

if ($ip_authenticated!="1") {

	print "0";
	exit;

}

$request_key = "";
if (isset($_GET["k"])) {
	$request_key = myr_safe_string($_GET["k"]);
}

$request_password = "";
if (isset($_GET["p"])) {
	$request_password = myr_safe_string($_GET["p"]);
}

if (($request_key=="") && ($request_password=="")) {

	print "0";
	exit;

}

if (($request_key==$client_key) && ($request_password==$client_password)) {

	myr_timeout();
	myr_memory();

	if ($enable_peak_memory_log=="1") {
		if (function_exists('register_shutdown_function')) {
			register_shutdown_function('myr_memory_peak_usage_monitor');
		}
	}

	if (!file_exists($api_data_directory."/")) {
		mkdir($api_data_directory."/", 0777);
		if (!file_exists($api_data_directory."/")) {
			mkdir($api_data_directory."/", 0777, true);
			if (!file_exists($api_data_directory."/")) {
				mkdir($api_data_directory."/", 0755, true);
				if (!file_exists($api_data_directory."/")) {

					print "0|18"; // Unable to create API data directory, please create manually.
					exit;

				}
			}
		}
	} else {
		if (!is_writable($api_data_directory."/")) {
			if ($api_allow_chmod=="1") {
				@chmod($api_data_directory,0777);
			}
			if (!is_writable($api_data_directory."/")) {

				print "0|18"; // Unable to write to API data directory, please check permissions.
				exit;

			}
		}
	}

	$request_directory = "";
	if (isset($_GET["d"])) {
		$request_directory = myr_safe_string($_GET["d"], "0");
	}
	$request_directory = str_replace('\\','/',$request_directory);
	$request_directory = str_replace('//','/',$request_directory);

	$request_ping = "";
	if (isset($_GET["ping"])) {
		$request_ping = myr_safe_string($_GET["ping"]);
	}

	$request_backup = "";
	if (isset($_GET["backup"])) {
		$request_backup = myr_safe_string($_GET["backup"]);
	}

	$request_restore = "";
	if (isset($_GET["restore"])) {
		$request_restore = myr_safe_string($_GET["restore"]);
	}

	$request_upgrade = "";
	if (isset($_GET["upgrade"])) {
		$request_upgrade = myr_safe_string($_GET["upgrade"]);
	}

	if (($request_ping=="1") && ($request_restore!="1")) {

		print "VERSION:$api_version\n";
		exit;

	} elseif ($request_ping=="2") {

		print "HOME:".str_replace('\\','/',__FILE__)."\n";
		exit;

	} elseif ($request_ping=="3") {

		print "DEBUG:".myr_debug_api()."\n";
		exit;

	} elseif (($request_backup=="1") || ($request_backup=="2") || ($request_backup=="3")) {

		$directory_files_list = "";
		$backup = request_backup();
		exit;

	} elseif (($request_restore=="1") || ($request_restore=="2") || ($request_restore=="3")) {

		if ($request_ping=="1") {

			print "VERSION:$api_version\nRESTORE\n";

		} else {

			$request_session = "";
			if (isset($_GET["session"])) {
				$request_session = myr_safe_string($_GET["session"]);
			}
			$request_session_string = "";
			if (isset($_GET["session_string"])) {
				$request_session_string = myr_safe_string($_GET["session_string"]);
			}

			if ($request_session!="") {

				if ($request_restore=="1") {

					print restore_stage_1();

				} elseif ($request_restore=="2") {

					print restore_stage_2();

				} elseif ($request_restore=="3") {

					print restore_stage_3();

				} else {

					print "0|5";

				}
			} else {
				print "0";
			}
		}
		exit;

	} elseif ($request_upgrade=="1") {

		print myr_api_upgrade();
		exit;

	}

	$request_ping = "";

	if ($request_directory=="") {

		$request_database_host = "";
		if (isset($_GET["dbh"])) {
			$request_database_host = myr_safe_string($_GET["dbh"], "0");
		}

		$request_database_name = "";
		if (isset($_GET["dbn"])) {
			$request_database_name = myr_safe_string($_GET["dbn"], "0");
		}

		$request_database_user = "";
		if (isset($_GET["dbu"])) {
			$request_database_user = myr_safe_string($_GET["dbu"], "0");
		}

		$request_database_pass = "";
		if (isset($_GET["dbp"])) {
			$request_database_pass = myr_safe_string($_GET["dbp"], "0");
		}

		if (($request_database_host!="") && ($request_database_name!="")) {

			request_database($request_database_host, $request_database_name, $request_database_user, $request_database_pass);
			exit;

		}

		$request_directory = myr_safe_string(__FILE__);
		$request_directory = str_replace('\\','/',$request_directory);
		$request_directory = str_replace('//','/',$request_directory);
		$request_script = basename(myr_safe_string(__FILE__));
		$request_directory = str_replace($request_script,"",$request_directory);
		$request_directory = str_replace('\\','/',$request_directory);
		$request_directory = str_replace('//','/',$request_directory);

	}

	if (is_dir($request_directory)) {

		if (isset($_GET["dr"])) {
			$disable_recursive = myr_safe_string($_GET["dr"]);
			if ($disable_recursive=="1") {
				$api_recursive_filesize = "0";
			}
		}

		print "DIRECTORY:$request_directory\n";
		print "VERSION:$api_version\n";
		print "CONTENTS\n";

		$directory_count = "0";
		$file_count = "0";

		if ($directory = opendir($request_directory)) {

			while (false !== ($directory_record = readdir($directory))) {

				if (($directory_record!="..") && ($directory_record!=".")) {

					if (is_dir("$request_directory/$directory_record")) {

						if ($api_recursive_filesize=="1") {
							$directory_size = directorysize("$request_directory/$directory_record/");
						} else {
							$directory_size = "0";
						}

						$directory_count++;
						print "DIR|$directory_record|$directory_size\n";

					} elseif (is_file("$request_directory/$directory_record")) {

						$file_dates = file_dates("$request_directory/$directory_record");
						$file_owners = file_owners("$request_directory/$directory_record");
						$file_permissions = file_permissions("$request_directory/$directory_record");
						$file_size = myr_filesize("$request_directory/$directory_record");

						$file_count++;
						print "FILE|$directory_record|$file_size|$file_dates|$file_owners|$file_permissions\n";

					}
				}
			}
		}

		print "END\n";
		print "DC:$directory_count|FC:$file_count\n";
		exit;

	} else {

		print "0|1";
		exit;

	}

} else {

	print "0";
	exit;

}


function request_database($database_host, $database_name, $database_user, $database_pass, $database_type = "mysql") {

	global $api_version, $ip_authenticated, $api_timeout, $api_allow_mysqli;

	if (($ip_authenticated=="1") && ($database_host!="") && ($database_name!="")) {
		if ($database_type=="mysql") {

			if ($api_allow_mysqli=="1") {
				if (myr_test_extension('mysqli')!==true) {
					$api_allow_mysqli = "0";
				}
			}

			if (($api_allow_mysqli=="1") && (@function_exists('mysqli_query'))) {

				$mysqli = mysqli_init();
				if ($api_timeout!="0") {
					mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, $api_timeout);
				}

				$database_host = explode(':', $database_host);
				$database_port = false;

				if ((isset($database_host[1])) && ($database_host[1]!='') && (is_numeric($database_host[1]))) {
					$database_port = $database_host[1];
				}
				$database_host = $database_host[0];

				$mysqli_connect = false;

				if ($database_port===false) {
					$mysqli_connect = mysqli_real_connect($mysqli, $database_host, $database_user, $database_pass, $database_name);
				} else {
					$mysqli_connect = mysqli_real_connect($mysqli, $database_host, $database_user, $database_pass, $database_name, $database_port);
				}

				if ($mysqli_connect!==false) {

					$mysql_charset = "utf8";

					if (function_exists('mysqli_character_set_name')) {
						if ($mysql_charset = mysqli_character_set_name($mysqli)) {
							} else {
								$mysql_charset = "utf8";
						}
					}

					if (function_exists('mysqli_set_charset')) {
						mysqli_set_charset($mysqli, $mysql_charset);
					}

					$query = "show table status;";

					if ($mysqli_query = mysqli_query($mysqli, $query)) {

						$number = mysqli_num_rows($mysqli_query);

						print "DATABASE:$database_name\n";
						print "VERSION:$api_version\n";
						print "CONTENTS\n";

						while ($mysqli_table = mysqli_fetch_array($mysqli_query, MYSQLI_ASSOC)) {

							$dbname = $mysqli_table['Name'];
							$dbengine = $mysqli_table['Engine'];
							$dbrows = $mysqli_table['Rows'];
							$dbdata_length = $mysqli_table['Data_length'];
							$dbupdate_time = $mysqli_table['Update_time'];
							$dbcollation = $mysqli_table['Collation'];
							$dbcomment = $mysqli_table['Comment'];

							print "DB|$dbname|$dbrows|$dbdata_length|$dbengine|$dbcollation|$dbupdate_time|$dbcomment\n";

						}

						print "END\n";
						print "DC:$number\n";

					} else {

						database_error("0|3");

					}

				} else {

					database_error("0|2");

				}

				mysqli_close($mysqli);

			} else {

				@mysql_connect($database_host, $database_user, $database_pass) or database_error("0|2");
				@mysql_select_db($database_name) or database_error("0|3");

				$mysql_charset = "utf8";
				if (function_exists('mysql_client_encoding')) {
					if ($mysql_charset = @mysql_client_encoding()) {
					} else {
						$mysql_charset = "utf8";
					}
				}
				if (function_exists('mysql_set_charset')) {
					@mysql_set_charset($mysql_charset);
				}

				$query = "show table status;";
				$result = @mysql_query($query);
				$number = @mysql_num_rows($result);

				print "DATABASE:$database_name\n";
				print "VERSION:$api_version\n";
				print "CONTENTS\n";

				for ($i=0; $i<$number; $i++) {

					$dbname = @mysql_result($result,$i,"Name");
					$dbengine = @mysql_result($result,$i,"Engine");
					$dbversion = @mysql_result($result,$i,"Version");
					$dbrow_format = @mysql_result($result,$i,"Row_format");
					$dbrows = @mysql_result($result,$i,"Rows");
					$dbavg_row_length = @mysql_result($result,$i,"Avg_row_length");
					$dbdata_length = @mysql_result($result,$i,"Data_length");
					$dbmax_data_length = @mysql_result($result,$i,"Max_data_length");
					$dbindex_length = @mysql_result($result,$i,"Index_length");
					$dbdata_free = @mysql_result($result,$i,"Data_free");
					$dbauto_increment = @mysql_result($result,$i,"Auto_increment");
					$dbcreate_time = @mysql_result($result,$i,"Create_time");
					$dbupdate_time = @mysql_result($result,$i,"Update_time");
					$dbcheck_time = @mysql_result($result,$i,"Check_time");
					$dbcollation = @mysql_result($result,$i,"Collation");
					$dbchecksum = @mysql_result($result,$i,"Checksum");
					$dbcreate_options = @mysql_result($result,$i,"Create_options");
					$dbcomment = @mysql_result($result,$i,"Comment");

					print "DB|$dbname|$dbrows|$dbdata_length|$dbengine|$dbcollation|$dbupdate_time|$dbcomment\n";

				}

				@mysql_close();

				print "END\n";
				print "DC:$number\n";

			}
		}
	}
}


function request_backup() {

	global $client_key, $client_password, $request_backup, $enable_debug_log, $directory_files_list, $api_packet_filesize, $api_packet_files, $api_max_file_filesize, $api_mcrypt_rijndael, $api_version, $api_mysqldump, $api_mysqldump_path, $api_use_mysql_list_tables, $api_myrepono_https, $api_max_files_per_process, $api_data_directory, $api_allow_exec, $api_csplit, $api_csplit_path, $api_split, $api_split_path, $api_allow_chmod, $api_force_curl, $api_read_length, $api_mysql_packet_size, $api_max_http_filesize, $ip_authenticated, $api_timeout, $api_allow_mysqli;

	$output = "";
	$solo_packet_filename = array();
	$backup_packet = array();

	if ($ip_authenticated!="1") {
		print "0";
		exit;
	}

	if (isset($_GET["session"])) {
		$request_session = myr_safe_string($_GET["session"]);
	}

	$request_packet = "";
	if (isset($_GET["packet"])) {
		$request_packet = myr_safe_string($_GET["packet"]);
	}

	$request_ftp = "";
	if (isset($_GET["ftp"])) {
		$request_ftp = myr_safe_string($_GET["ftp"]);
	}

	if ($request_session=="") {

		print "0|0";
		exit;

	} else {

		$request_session_random_string = random_string(12);

		if ($request_backup=="1") {

			myr_clean_data();

			$fh = fopen($api_data_directory."/".basename("$request_session.tmp"), 'w');
			fwrite($fh, $request_session_random_string);
			fclose($fh);

			print "1|$request_session_random_string|";
			exit;

		} elseif (($request_backup=="2") || ($request_backup=="3")) {

			$request_session_random_string = file_get_contents($api_data_directory."/".basename("$request_session.tmp"));

		}

		$request_session_string = substr($client_key,1,1).substr($client_key,3,1).substr($client_password,4,1).substr($request_session,1,1).substr($request_session,5,1).substr($request_session,8,1).substr($request_session,12,2).substr($request_session_random_string,2,2).substr($request_session_random_string,7,1).substr($request_session_random_string,10,1);
		$request_session_string = md5($request_session_string);

		$date = date("Y-m-d H:i:s");
		$date_strip = str_replace("-","",$date);
		$date_strip = str_replace(":","",$date_strip);
		$date_strip = str_replace(" ","",$date_strip);

		$backup_log = "$date\n\n";

		if ($request_backup=="3") {
			if (file_exists($api_data_directory."/".basename("INDEX-".$request_session.".dat"))) {
				if ($request_packet=="") {

					print "0";
					exit;

				}
			} else {

				print "0";
				exit;

			}
		}

		if ($request_backup=="3") {

			if (file_exists($api_data_directory."/".basename("INDEX-".$request_session.".dat"))) {

				$index_packets = myr_serialize_open($api_data_directory."/".basename("INDEX-".$request_session.".dat"));

				if (isset($index_packets['packets_count'])) {

					$backup_packet_count = $index_packets['packets_count'];

				} else {

					if ($enable_debug_log=="1") {

						$backup_log .= "Index does not contain packet count.\n";
						$backup_log_filename = "BACKUP-".$date_strip.".log";
						$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
						fwrite($fh, $backup_log);
						fclose($fh);

					}

					print "0";
					exit;

				}

			} else {

				if ($enable_debug_log=="1") {

					$backup_log .= "Index not found ($request_session).\n";
					$backup_log_filename = "BACKUP-".$date_strip.".log";
					$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
					fwrite($fh, $backup_log);
					fclose($fh);

				}

				print "0";
				exit;
			}

			if ($request_packet=="") {
				$backup_packet_number = "0";
			} else {
				$backup_packet_number = $request_packet;
			}

			if ($enable_debug_log=="1") {

				$backup_log .= "\$backup_packet_number = $backup_packet_number\n\$backup_packet_count = $backup_packet_count\n";

			}

			$backup_filename = "BACKUP-$request_session"."-".$backup_packet_number.".zip";

			if ($request_packet=="END") {

				if (file_exists($api_data_directory."/".basename("INDEX-".$request_session.".dat"))) {
					unlink($api_data_directory."/".basename("INDEX-".$request_session.".dat"));
				}

				for ($i=0; $i<($backup_packet_count+1); $i++) {
					if (file_exists($api_data_directory."/".basename("INDEX-".$request_session."-".$i.".dat"))) {
						unlink($api_data_directory."/".basename("INDEX-".$request_session."-".$i.".dat"));
					}
				}

				if (file_exists($api_data_directory."/".basename("$request_session.tmp"))) {
					unlink($api_data_directory."/".basename("$request_session.tmp"));
				}

				$mysql_export_files = myr_serialize_open($api_data_directory."/".basename("SQL-$request_session.dat"));

				for ($i=0; $i<count($mysql_export_files); $i++) {

					if (file_exists($mysql_export_files[$i])) {
						unlink($mysql_export_files[$i]);
					}
				}

				if (file_exists($api_data_directory."/".basename("SQL-$request_session.dat"))) {
					unlink($api_data_directory."/".basename("SQL-$request_session.dat"));
				}

				myr_clean_data();

				print "1";
				exit;

			}

			if (file_exists($api_data_directory."/".basename("INDEX-".$request_session."-".$backup_packet_number.".dat"))) {

				$backup_packet = myr_serialize_open($api_data_directory."/".basename("INDEX-".$request_session."-".$backup_packet_number.".dat"));

				//if (isset($backup_packet['MYR-BF'])) {
				//	unset($backup_packet['MYR-BF']);
				//}
				//if (isset($backup_packet['MYR-BPF'])) {
				//	unset($backup_packet['MYR-BPF']);
				//}

			} else {

				if ($enable_debug_log=="1") {

					$backup_log .= "Packet index not found: $backup_packet_number\n";
					$backup_log_filename = "BACKUP-".$date_strip.".log";
					$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
					fwrite($fh, $backup_log);
					fclose($fh);

				}

				print "0";
				exit;
			}

			if (!isset($backup_packet[$backup_packet_number])) {
				$backup_packet[$backup_packet_number] = array();
			}

			if ($enable_debug_log=="1") {
				$backup_log .= "\n==\n\$backup_packet_number = $backup_packet_number\nFile Count: ".count($backup_packet[$backup_packet_number])."\n==\n";
				$backup_log .= "Packet $backup_packet_number\nFilename: $backup_filename\n";
			}

			$download_backup_filename_packet = "";
			$request_file_packet = "0";
			$request_file_packet_total = "0";
			if (isset($_GET["file_packet"])) {
				$request_file_packet = myr_safe_string($_GET["file_packet"]);
			}
			if (isset($_GET["file_packet_total"])) {
				$request_file_packet_total = myr_safe_string($_GET["file_packet_total"]);
			}

			if (!is_numeric($request_file_packet)) {
				$request_file_packet = "0";
			} elseif ($request_file_packet<0) {
				$request_file_packet = "0";
			}

			if (!is_numeric($request_file_packet_total)) {
				$request_file_packet_total = "0";
			} elseif ($request_file_packet_total<0) {
				$request_file_packet_total = "0";
			}

			$backup_table_of_contents = array();

			if ((count($backup_packet[$backup_packet_number])=="1") && (myr_filesize($backup_packet[$backup_packet_number][0])>$api_max_file_filesize)) {

				if (($request_ftp=="0") && (myr_filesize($backup_packet[$backup_packet_number][0])>$api_max_http_filesize)) {
					$request_ftp = "1";
				}

				$backup_filename_extension = explode(".",basename($backup_packet[$backup_packet_number][0]));
				$backup_filename_extension_count = count($backup_filename_extension) - 1;

				$backup_filename = "SBACKUP-$request_session"."-".$backup_packet_number.".".$backup_filename_extension[$backup_filename_extension_count];

				if ($enable_debug_log=="1") {
					$backup_log .= "Solo backup packet.\n";
					$backup_log .= "New Filename: $backup_filename\n";
				}

				$solo_packet_filename[$backup_packet_number] = $backup_packet[$backup_packet_number][0];

				$backup_table_of_contents[] = dirname($backup_packet[$backup_packet_number][0])."/$backup_packet_number|0|".basename($backup_packet[$backup_packet_number][0]);

				$backup_solo = "1";

				if ($request_file_packet>0) {

					$download_backup_filename_packet = "SBACKUP-$request_session"."-".$backup_packet_number."-".$request_file_packet.".".$backup_filename_extension[$backup_filename_extension_count];

					if (!file_exists($api_data_directory."/".$download_backup_filename_packet)) {
						$download_backup_filename_packet = "";
					}
				}

				if ($enable_debug_log=="1") {
					$backup_log .= "\$download_backup_filename_packet = $download_backup_filename_packet\n\$request_file_packet = $request_file_packet\n\n";
				}

				if ($enable_debug_log=="1") {

					$backup_log_filename = "BACKUP-".$date_strip.".log";

					if ($backup_log!="") {

						$backup_log .= "Memory usage: ".myr_memory_peak_usage()." bytes\n";

						$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
						fwrite($fh, $backup_log);
						fclose($fh);

					}
				}

				if (($download_backup_filename_packet=="") || ($request_file_packet=="0")) {

					$download_backup_split_success = "0";

					if (substr($backup_packet[$backup_packet_number][0],0,strlen($api_data_directory))==$api_data_directory) {
						if (rename($backup_packet[$backup_packet_number][0],$api_data_directory."/".$backup_filename)) {
							if ($enable_debug_log=="1") {
								$backup_log .= "Backup packet successfully renamed for split.\n";
							}
						} elseif ($api_allow_exec=="1") {
							exec("mv ".@escapeshellarg($backup_packet[$backup_packet_number][0])." ".@escapeshellarg($api_data_directory."/".$backup_filename));
							if (!file_exists($api_data_directory."/".$backup_filename)) {
								exec("move ".@escapeshellarg($backup_packet[$backup_packet_number][0])." ".@escapeshellarg($api_data_directory."/".$backup_filename));
							} elseif ($enable_debug_log=="1") {
								$backup_log .= "Backup packet successfully moved for split (using exec).\n";
							}
						}
					} else {
						if (copy($backup_packet[$backup_packet_number][0],$api_data_directory."/".$backup_filename)) {
							if ($enable_debug_log=="1") {
								$backup_log .= "Backup packet successfully copied for split.\n";
							}
						} elseif ($api_allow_exec=="1") {
							exec("cp ".@escapeshellarg($backup_packet[$backup_packet_number][0])." ".@escapeshellarg($api_data_directory."/".$backup_filename));
							if (!file_exists($api_data_directory."/".$backup_filename)) {
								exec("copy ".@escapeshellarg($backup_packet[$backup_packet_number][0])." ".@escapeshellarg($api_data_directory."/".$backup_filename));
							} elseif ($enable_debug_log=="1") {
								$backup_log .= "Backup packet successfully copied for split (using exec).\n";
							}
						}
					}

					if (($request_ftp=="0") && (file_exists($api_data_directory."/".$backup_filename))) {

						if ($enable_debug_log=="1") {
							$backup_log .= "Backup packet found after copy for split.\n";
						}

						$fp_r = fopen($api_data_directory."/".$backup_filename, 'rb');

						if (!$fp_r) {

							if ($enable_debug_log=="1") {
								$backup_log .= "File too large to open with pure PHP.\n";
							}

						} else {

							$total_fp_r_strlen = "0";

							$backup_filename_packet_number = "0";
							$backup_filename_packet = "SBACKUP-$request_session"."-".$backup_packet_number."-".$backup_filename_packet_number.".".$backup_filename_extension[$backup_filename_extension_count];

							$download_backup_filename_packet = $backup_filename_packet;

							$fp_w = fopen($api_data_directory."/".$backup_filename_packet, 'wb');

							while ( ($data_line = fgets($fp_r)) !== false) {

								$total_fp_r_strlen = $total_fp_r_strlen + strlen($data_line);
								fwrite($fp_w, $data_line);

								if ($total_fp_r_strlen>=$api_max_file_filesize) {

									fclose($fp_w);
									$total_fp_r_strlen = "0";

									$backup_filename_packet_number++;
									$backup_filename_packet = "SBACKUP-$request_session"."-".$backup_packet_number."-".$backup_filename_packet_number.".".$backup_filename_extension[$backup_filename_extension_count];
									$request_file_packet_total = $backup_filename_packet_number;

									if ($request_file_packet==$backup_filename_packet_number) {

										$download_backup_filename_packet = $backup_filename_packet;

									}

									$fp_w = fopen($api_data_directory."/".$backup_filename_packet, 'wb');

								}

								$download_backup_split_success = "1";

							}

							fclose($fp_w);
							fclose($fp_r);

						}

						if ($download_backup_split_success!="1") {

							if ($api_csplit=="1") {
								myr_test_exec("csplit");
							}

							if ($api_split=="1") {
								myr_test_exec("split");
							}

						} else {

							unlink($api_data_directory."/".$backup_filename);

						}

						if ($enable_debug_log=="1") {
							$backup_log .= "\$download_backup_split_success = $download_backup_split_success\n\$api_allow_exec = $api_allow_exec\n\$api_csplit = $api_csplit\n\$api_split = $api_split\n";
						}

						if (($download_backup_split_success!="1") && ($api_allow_exec=="1") && ($api_split=="1")) {


							if ($api_allow_chmod=="1") {
								@chmod($api_data_directory."/".$backup_filename,0777);
							}

							$split_success = "0";

							$split_command = $api_split_path." -b".($api_max_file_filesize - 1024)." ".@escapeshellarg($api_data_directory."/".$backup_filename)." SPLIT-BACKUP-".@escapeshellarg("SBACKUP-$request_session"."-".$backup_packet_number."-");

							if ($enable_debug_log=="1") {

								exec($split_command,$split_response);
								$split_response = implode("\n",$split_response);
								$backup_log .= "\$split_response = /$split_response/\n\n";

							} else {

								exec($split_command);

							}

							if ($split_directory = opendir($api_data_directory."/")) {

								while (false !== ($split_directory_record = readdir($split_directory))) {

									if (($split_directory_record!="..") && ($split_directory_record!=".")) {

										if (is_file($api_data_directory."/".$split_directory_record)) {

											if (stristr($split_directory_record,"SPLIT-SBACKUP-$request_session"."-".$backup_packet_number."-")) {

												$split_directory_record_id = str_replace("SBACKUP-$request_session"."-".$backup_packet_number."-","",$split_directory_record);

												$split_directory_record_l = strlen($split_directory_record_id);
												$split_directory_record_n = 0;
												for ($split_directory_record_i=0; $split_directory_record_i<$split_directory_record_l; $split_directory_record_i++) {
													$split_directory_record_n = $split_directory_record_n*26 + ord($split_directory_record_id[$split_directory_record_i]) - 0x40;
												}
												$split_directory_record_id = $split_directory_record_n - 891;

												$split_directory_record_new = str_replace("SPLIT-SBACKUP-","SBACKUP-",$split_directory_record);
												$split_directory_record_new .= ".sql";

												$split_success = "1";

												if (is_numeric($split_directory_record_id)) {

													if ($request_file_packet==$split_directory_record_id) {

														$download_backup_filename_packet = $backup_filename_packet;

													}
												}
											}
										}
									}
								}

							} else {
								if ($enable_debug_log=="1") {
									$backup_log .= "Unable to read API directory for split packets.\n";
								}
							}

							if ($split_success!="1") {

								if ($enable_debug_log=="1") {
									$backup_log .= "split of large backup packet failed, command: $split_command\n";
								}

							} else {

								unlink($api_data_directory."/".$backup_filename);

							}

						} else {
							if ($enable_debug_log=="1") {
								$backup_log .= "Backup packet to large to process.\n";
							}
						}

						if (($split_success=="0") && ($download_backup_split_success!="1") && ($api_allow_exec=="1") && ($api_csplit=="1")) {

							if ($api_allow_chmod=="1") {
								@chmod($api_data_directory."/".$backup_filename,0777);
							}

							$csplit_success = "0";

							$csplit_command = $api_csplit_path." -fSPLIT-BACKUP-".@escapeshellarg("SBACKUP-$request_session"."-".$backup_packet_number."-")." -k -n1 -s".@escapeshellarg($api_data_directory."/".$backup_filename)." 100 {100}";

							if ($enable_debug_log=="1") {

								exec("cd ".$api_data_directory."\n".$csplit_command,$csplit_response);
								$csplit_response = implode("\n",$csplit_response);
								if ($enable_debug_log=="1") {
									$backup_log .= "\$csplit_response = /$csplit_response/\n\n";
								}

							} else {

								exec("cd ".$api_data_directory."\n".$csplit_command);

							}

							if ($csplit_directory = opendir($api_data_directory."/")) {

								while (false !== ($csplit_directory_record = readdir($csplit_directory))) {

									if (($csplit_directory_record!="..") && ($csplit_directory_record!=".")) {

										if (is_file($api_data_directory."/".$csplit_directory_record)) {

											if (stristr($csplit_directory_record,"SPLIT-SBACKUP-$request_session"."-".$backup_packet_number."-")) {

												$csplit_directory_record_id = str_replace("SBACKUP-$request_session"."-".$backup_packet_number."-","",$csplit_directory_record);

												$csplit_directory_record_new = str_replace("SPLIT-SBACKUP-","SBACKUP-",$csplit_directory_record);
												$csplit_directory_record_new .= ".sql";

												$csplit_success = "1";

												if (is_numeric($csplit_directory_record_id)) {

													if ($request_file_packet==$csplit_directory_record_id) {

														$download_backup_filename_packet = $backup_filename_packet;

													}
												}
											}
										}
									}
								}

							} else {
								if ($enable_debug_log=="1") {
									$backup_log .= "Unable to read API directory for split packets.\n";
								}
							}

							if ($csplit_success!="1") {
								if ($enable_debug_log=="1") {
									$backup_log .= "csplit of large backup packet failed, command: $csplit_command\n";
								}
							} else {

								unlink($api_data_directory."/".$backup_filename);

							}

						} else {
							if ($enable_debug_log=="1") {
								$backup_log .= "Backup packet to large to process.\n";
							}
						}
					} else {
						if ($enable_debug_log=="1") {
							$backup_log .= "Backup packet to large to copy.\n";
						}
					}
				}


				if ($enable_debug_log=="1") {

					$backup_log_filename = "BACKUP-".$date_strip.".log";

					if ($backup_log!="") {

						$backup_log .= "Memory usage: ".myr_memory_peak_usage()." bytes\n";

						$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
						fwrite($fh, $backup_log);
						fclose($fh);

					}

				}

			} else {

				$backup_solo = "0";

				$newzip = new dZip($api_data_directory."/".$backup_filename);
				$add_file_count = "0";

				for ($i=0; $i<count($backup_packet[$backup_packet_number]); $i++) {

					if (isset($backup_packet[$backup_packet_number][$i])) {
						if ($backup_packet[$backup_packet_number][$i]!="") {

							$newzip->addFile($backup_packet[$backup_packet_number][$i],$backup_packet[$backup_packet_number][$i]);

							$backup_table_of_contents[] = dirname($backup_packet[$backup_packet_number][$i])."/$backup_packet_number|$add_file_count|".basename($backup_packet[$backup_packet_number][$i]);

							$add_file_count++;

						}
					}
				}

				$newzip->save();

			}

			if ($download_backup_filename_packet!="") {

				$tmp_backup_filename = $backup_filename;
				$backup_filename = $download_backup_filename_packet;

			}

			$backup_file_filesize_pre_crypt = myr_filesize($api_data_directory."/".$backup_filename);

			if ($enable_debug_log=="1") {
				$backup_log .= "Filename: $backup_filename (Pre Crypt: $backup_file_filesize_pre_crypt, Solo: $backup_solo)\n";
			}

			if ($backup_file_filesize_pre_crypt>$api_max_http_filesize) {
				$request_ftp = "1";
				if ($enable_debug_log=="1") {
					$backup_log .= "Max HTTP: $backup_file_filesize_pre_crypt>$api_max_http_filesize\n";
				}
			}

			if ((($backup_solo=="1") && ($request_ftp=="1")) || (($backup_solo=="1") && ($download_backup_filename_packet=="") && ($backup_file_filesize_pre_crypt>($api_max_file_filesize * 5)))) {

				$new_backup_filename = "N".$backup_filename;
				if (file_exists($api_data_directory."/".$backup_filename)) {
					rename($api_data_directory."/".$backup_filename, $api_data_directory."/".$new_backup_filename);
				}
				$backup_filename = $new_backup_filename;

				if ($enable_debug_log=="1") {
					$backup_log .= "File too large to encrypt.\n";
					$backup_log .= "New Filename: $backup_filename\n";
				}

			} else {

				if (encrypt_file($api_data_directory."/".$backup_filename,$request_session)) {

					unlink($api_data_directory."/".$backup_filename);
					$backup_filename_crypt = $backup_filename."-crypt";
					rename($api_data_directory."/".$backup_filename_crypt,$api_data_directory."/".$backup_filename);

					if ($enable_debug_log=="1") {
						$backup_log .= "File successfully encrypted.\n";
					}

				} elseif ($enable_debug_log=="1") {

					unlink($api_data_directory."/".$backup_filename);
					$backup_filename_crypt = $backup_filename."-crypt";
					unlink($api_data_directory."/".$backup_filename_crypt);

					$backup_log .= "File encryption failed.\n";

				}

			}

			$backup_filesize = myr_filesize($api_data_directory."/".$backup_filename);
			$backup_checksum = "";

			if ($enable_debug_log=="1") {
				$backup_log .= "File: $backup_filename ($backup_filesize)\n";
			}

			$backup_checksum = "";
			if (!isset($solo_packet_filename[$backup_packet_number])) {
				$solo_packet_filename[$backup_packet_number] = "";
			}
			if ($solo_packet_filename[$backup_packet_number]!="") {
				$backup_checksum = $solo_packet_filename[$backup_packet_number];
			}

			if (is_array($backup_table_of_contents)) {
				$backup_table_of_contents = file_tree_array($backup_table_of_contents);
				$backup_table_of_contents = serialize($backup_table_of_contents);
				$backup_table_of_contents = str_replace("\n","",$backup_table_of_contents);
				$backup_table_of_contents = str_replace("\r","",$backup_table_of_contents);
				$backup_table_of_contents = str_replace("||","|[(MYRDELIM)]|",$backup_table_of_contents);
			}

			print "$backup_filename||\n$backup_filesize||\nPACKET:".($backup_packet_number+1)."||\nPACKETS:".$backup_packet_count."||\nOF:$backup_checksum||\nTOC:$backup_table_of_contents||\n";

			if ($enable_debug_log=="1") {
				$backup_log .= "$backup_filename||\n$backup_filesize||\nPACKET:".($backup_packet_number+1)."||\nPACKETS:".$backup_packet_count."||\nOF:$backup_checksum||\nTOC:$backup_table_of_contents||\n";
			}

			if ($request_file_packet_total!="") {

				print "FP:$request_file_packet/$request_file_packet_total||\n";

				if ($enable_debug_log=="1") {
					$backup_log .= "FP:$request_file_packet/$request_file_packet_total||\n";
				}
			}

			$backup_table_of_contents = "";

			if ($backup_filesize>$api_max_http_filesize) {
				$request_ftp = "1";
				if ($enable_debug_log=="1") {
					$backup_log .= "Max HTTP: $backup_filesize>$api_max_http_filesize\n";
				}
			}

			if ($request_ftp=="1") {

				print "FTP:".$api_data_directory."/".$backup_filename;
				if ($enable_debug_log=="1") {
					$backup_log .= "FTP:".$api_data_directory."/".$backup_filename."\n";
				}
				if ($api_allow_chmod=="1") {
					@chmod($api_data_directory."/".$backup_filename,0755);
				}

			} else {

				$fp_r = fopen($api_data_directory."/".$backup_filename, 'rb');

				if ((!$fp_r) && ($api_allow_chmod=="1")) {

					@chmod($api_data_directory."/".$backup_filename,0777);

					$fp_r = fopen($api_data_directory."/".$backup_filename, 'rb');

				}

				if ($fp_r) {

					while (!feof($fp_r)) {

						print fread($fp_r, $api_read_length);

					}

					fclose($fp_r);

					//readfile($api_data_directory."/".$backup_filename);

				} else {

					$request_ftp = "1";
					print "FTP:".$api_data_directory."/".$backup_filename;

					if ($enable_debug_log=="1") {
						$backup_log .= "FTP:".$api_data_directory."/".$backup_filename."\n";
					}
					if ($api_allow_chmod=="1") {
						@chmod($api_data_directory."/".$backup_filename,0755);
					}

				}

			}

			$backup_filesize_mb = ($backup_filesize / 1024) / 1024;
			$backup_filesize_mb = sprintf ("%01.5f",$backup_filesize_mb);

			if ($enable_debug_log=="1") {
				$backup_log .= "File sent ($backup_filesize_mb MB)\n";
			}

			if ($request_ftp=="1") {
			} elseif (file_exists($api_data_directory."/".$backup_filename)) {

				unlink($api_data_directory."/".$backup_filename);

			}


			if ($enable_debug_log=="1") {

				$backup_log_filename = "BACKUP-".$date_strip.".log";
				if ($backup_log!="") {

					$backup_log .= "Memory usage: ".myr_memory_peak_usage()." bytes\n";

					$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
					fwrite($fh, $backup_log);
					fclose($fh);

				}
			}

		} elseif ($request_backup=="2") {

			$request_backup_action = "";
			if (isset($_GET["backup_action"])) {
				$request_backup_action = myr_safe_string($_GET["backup_action"]);
			}

			$request_backup_action_packet = "";
			if (isset($_GET["backup_action_packet"])) {
				$request_backup_action_packet = myr_safe_string($_GET["backup_action_packet"]);
			}
			if ($request_backup_action_packet=="") {
				$request_backup_action_packet = "0";
			}

			if ($request_backup_action=="") {

				if ($api_myrepono_https=="1") {
					$request_backup_url = "https://myrepono.com/sys/backup_request/?session=$request_session&string=$request_session_string&random_string=$request_session_random_string&instructions=1";
				} else {
					$request_backup_url = "http://myrepono.com/sys/backup_request/?session=$request_session&string=$request_session_string&random_string=$request_session_random_string&instructions=1";
				}

				if ($enable_debug_log=="1") {
					$backup_log .= "Request URL: $request_backup_url\n\n";
				}

				$request_backup_instructions = myr_connect($request_backup_url);

				$backup_total_files = "0";
				$backup_filenames = array();
				$backup_db_count = "0";
				$backup_database = array();
				$backup_database_table = array();
				$backup_excludes = array();

				if ($request_backup_instructions=="0") {

					if ($enable_debug_log=="1") {
						$backup_log .= "Backup failed: $request_backup_instructions\n";
					}



					if ($enable_debug_log=="1") {
						$backup_log_filename = "BACKUP-".$date_strip.".log";
						$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
						fwrite($fh, $backup_log);
						fclose($fh);
					}

					print "0|0";
					exit;

				} elseif ($request_backup_instructions!="") {

					if ($enable_debug_log=="1") {
						$backup_log .= "Instructions\n$request_backup_instructions\n\n";
					}

					$request_backup_instructions_lines = explode("\n",$request_backup_instructions);

					for ($i=0; $i<count($request_backup_instructions_lines); $i++) {

						$request_backup_instruction = explode("|",$request_backup_instructions_lines[$i]);

						if ($request_backup_instruction[0]=="FILES") {

							$backup_total_files = $request_backup_instruction[1];

						}

						if ($request_backup_instruction[0]=="FILE") {

							$backup_filenames[] = $request_backup_instruction[2];

						}

						if ($request_backup_instruction[0]=="DB") {

							$backup_database[$backup_db_count]['id'] = $request_backup_instruction[1];
							$backup_database[$backup_db_count]['dbhost'] = $request_backup_instruction[2];
							$backup_database[$backup_db_count]['dbname'] = $request_backup_instruction[3];
							$backup_database[$backup_db_count]['dbuser'] = $request_backup_instruction[4];
							$backup_database[$backup_db_count]['dbpass'] = $request_backup_instruction[5];
							$backup_database[$backup_db_count]['backup_all'] = $request_backup_instruction[6];

							$backup_db_count++;

						}

						if ($request_backup_instruction[0]=="TABLE") {

							$backup_db_id = $request_backup_instruction[1];
							$backup_database_table[$backup_db_id][] = $request_backup_instruction[3];

						}

						if ($request_backup_instruction[0]=="FEXCL") {

							$backup_excludes[] = $request_backup_instruction[1];

						}


					}

				}

				$backup_excludes_count = count($backup_excludes);
				for ($i=0; $i<$backup_excludes_count; $i++) {
					if (isset($backup_excludes[$i])) {
						if (trim($backup_excludes[$i])=="") {
							unset($backup_excludes[$i]);
						}
					}
				}

				if (count($backup_filenames)==0) {
					$backup_filenames = '';
				}

				$cache_backup_instructions = array(
					'backup_filenames' => $backup_filenames,
					'backup_database' => $backup_database,
					'backup_database_table' => $backup_database_table,
					'backup_file_excludes' => $backup_excludes
				);

				myr_serialize_save($api_data_directory."/".basename("CACHE-$request_session.dat"), $cache_backup_instructions);
				$cache_backup_instructions = array();

				if (($enable_debug_log=="1") && ($backup_log!="")) {

					$backup_log .= "Memory usage: ".myr_memory_peak_usage()." bytes\n";
					$backup_log_filename = "BACKUP-".$date_strip.".log";
					$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
					fwrite($fh, $backup_log);
					fclose($fh);

				}

				print "1|$backup_db_count|";

				exit;


			} elseif ((is_numeric($request_backup_action)) && ($request_backup_action!="END")) {


				$cache_backup_instructions = array();

				if (file_exists($api_data_directory."/".basename("CACHE-$request_session.dat"))) {

					$cache_backup_instructions = myr_serialize_open($api_data_directory."/".basename("CACHE-$request_session.dat"));

				} else {

					print "0";
					exit;

				}

				$backup_filenames = array();
				if (isset($cache_backup_instructions['backup_filenames'])) {

					$backup_filenames = $cache_backup_instructions['backup_filenames'];

				}
				$backup_total_files = count($backup_filenames);

				$backup_database = array();
				if (isset($cache_backup_instructions['backup_database'])) {

					$backup_database = $cache_backup_instructions['backup_database'];

				}
				$backup_db_count = count($backup_database);

				$backup_database_table = array();
				if (isset($cache_backup_instructions['backup_database_table'])) {

					$backup_database_table = $cache_backup_instructions['backup_database_table'];

				}

				$mysql_export_files = array();
				if (isset($cache_backup_instructions['mysql_export_files'])) {

					$mysql_export_files = $cache_backup_instructions['mysql_export_files'];

				}

				$backup_file_excludes = array();
				if (isset($cache_backup_instructions['backup_file_excludes'])) {
					$backup_file_excludes = $cache_backup_instructions['backup_file_excludes'];
				}
				$backup_file_excludes_count = count($backup_file_excludes);

				$backup_mysql_date = date("Y-m-d H:i:s");

				$backup_database_count = count($backup_database);
				$backup_database_tables_count = "0";
				$mysql_tables_list = array();

				$backup_db_tables_count = "0";

				if ($backup_database_count>0) {

					for ($i=0; $i<$backup_database_count; $i++) {

						if ($request_backup_action==$i) {

							if ($api_allow_mysqli=="1") {
								if (myr_test_extension('mysqli')!==true) {
									$api_allow_mysqli = "0";
								}
							}

							if (($api_allow_mysqli=="1") && (@function_exists('mysqli_query'))) {

								$mysqli = mysqli_init();
								if ($api_timeout!="0") {
									mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, $api_timeout);
								}

								$database_host = explode(':', $backup_database[$i]['dbhost']);
								$database_port = false;

								if ((isset($database_host[1])) && ($database_host[1]!='') && (is_numeric($database_host[1]))) {
									$database_port = $database_host[1];
								}
								$database_host = $database_host[0];
								$backup_database[$i]['dbhost'] = $database_host;

								$mysqli_connect = false;

								if ($database_port===false) {
									$mysqli_connect = mysqli_real_connect($mysqli, $backup_database[$i]['dbhost'], $backup_database[$i]['dbuser'], $backup_database[$i]['dbpass'], $backup_database[$i]['dbname']);
								} else {
									$mysqli_connect = mysqli_real_connect($mysqli, $backup_database[$i]['dbhost'], $backup_database[$i]['dbuser'], $backup_database[$i]['dbpass'], $backup_database[$i]['dbname'], $database_port);
								}

								if ($mysqli_connect!==false) {

									$backup_log .= "mySQLi connected\n";

									$mysql_charset = "utf8";

									if (function_exists('mysqli_character_set_name')) {
										if ($mysql_charset = mysqli_character_set_name($mysqli)) {
										} else {
											$mysql_charset = "utf8";
										}
									}

									$backup_log .= "Charset: $mysql_charset\n";

									if (function_exists('mysqli_set_charset')) {
										mysqli_set_charset($mysqli, $mysql_charset);
									}

									if ($mysqli_query = mysqli_query($mysqli, "SHOW TABLES FROM `".$backup_database[$i]['dbname']."`")) {

										if ($backup_database[$i]['backup_all']=="1") {

											while ($mysqli_table = mysqli_fetch_array($mysqli_query)) {

												$backup_database_tables_count++;
												array_push($mysql_tables_list, $mysqli_table[0]);

											}

										} else {

											$backup_database_id = $backup_database[$i]['id'];
											$backup_database_tables_count = count($backup_database_table[$backup_database_id]);

											if ($backup_database_tables_count>0) {

												for ($j=0; $j<$backup_database_tables_count; $j++) {

													array_push($mysql_tables_list, $backup_database_table[$backup_database_id][$j]);

												}
											}
										}

									} else {

										$backup_log .= "Database list tables failed: ".$backup_database[$i]['dbname']."\n\n";

									}

								} else {

									database_error("0|2");

								}

							} else {

								@mysql_connect($backup_database[$i]['dbhost'], $backup_database[$i]['dbuser'], $backup_database[$i]['dbpass']) or database_error("0|2");
								@mysql_select_db($backup_database[$i]['dbname']) or database_error("0|3");

								$mysql_charset = "utf8";
								if (function_exists('mysql_client_encoding')) {
									if ($mysql_charset = @mysql_client_encoding()) {
									} else {
										$mysql_charset = "utf8";
									}
								}
								if (function_exists('mysql_set_charset')) {
									@mysql_set_charset($mysql_charset);
								}

								if (!isset($api_use_mysql_list_tables)) {
									$api_use_mysql_list_tables = "0";
								}

								if ($api_use_mysql_list_tables!="1") {

									$mysql_query = "SHOW TABLES FROM `".$backup_database[$i]['dbname']."`";
									$mysql_result = @mysql_query($mysql_query) or $backup_log .= "Database list tables failed: ".$backup_database[$i]['dbname']."\n\n";

									if ($backup_database[$i]['backup_all']=="1") {

										while ($table_name = @mysql_fetch_row($mysql_result)) {

											$backup_database_tables_count++;
											array_push($mysql_tables_list, $table_name[0]);

										}

									} else {

										$backup_database_id = $backup_database[$i]['id'];
										$backup_database_tables_count = count($backup_database_table[$backup_database_id]);

										if ($backup_database_tables_count>0) {

											for ($j=0; $j<$backup_database_tables_count; $j++) {

												array_push($mysql_tables_list, $backup_database_table[$backup_database_id][$j]);

											}
										}
									}

								} else {

									$mysql_tables = @mysql_list_tables($backup_database[$i]['dbname']) or $backup_log .= "Database list tables failed: ".$backup_database[$i]['dbname']."\n\n";

									if ($backup_database[$i]['backup_all']=="1") {

										while ($table_name = @mysql_fetch_array($mysql_tables)) {

											$backup_database_tables_count++;
											array_push($mysql_tables_list, $table_name[0]);

										}

									} else {

										$backup_database_id = $backup_database[$i]['id'];
										$backup_database_tables_count = count($backup_database_table[$backup_database_id]);

										if ($backup_database_tables_count>0) {

											for ($j=0; $j<$backup_database_tables_count; $j++) {

												array_push($mysql_tables_list, $backup_database_table[$backup_database_id][$j]);

											}
										}
									}
								}
							}

							$backup_db_tables_count = count($mysql_tables_list);

							for ($j=0; $j<count($mysql_tables_list); $j++) {

								if ($request_backup_action_packet==$j) {

									$mysql_export = "";

									if ($api_allow_exec=="1") {
										if ($api_mysqldump=="1") {
											myr_test_exec("mysqldump");
										}
									}

									if (($api_allow_exec=="1") && ($api_mysqldump=="1")) {

										$mysql_export_part = "0";

										$backup_mysql_filename = $api_data_directory."/"."BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-".$mysql_export_part.".sql";

										$mysqldump_command = $api_mysqldump_path." -h ".@escapeshellarg($backup_database[$i]['dbhost'])." -u ".@escapeshellarg($backup_database[$i]['dbuser'])." -p".@escapeshellarg($backup_database[$i]['dbpass'])." -cfQ --skip-add-locks ".@escapeshellarg($backup_database[$i]['dbname'])." ".@escapeshellarg($mysql_tables_list[$j])." -r".@escapeshellarg($backup_mysql_filename);

										exec($mysqldump_command);

										$mysql_export_part = "0";

										$mysql_export_header = "-- myRepono mySQL Export\n-- http://myrepono.com\n--\n-- $backup_mysql_date\n--\n-- Host: ".$backup_database[$i]['dbhost']."\n-- Database: ".$backup_database[$i]['dbname']."\n-- Table: ".$mysql_tables_list[$j]."\n-- Generated with mysqldump\n--\n\n";

										$mysql_export_footer = "\n\n-- End of mySQL export\n";

										if ((file_exists($backup_mysql_filename)) && (myr_filesize($backup_mysql_filename)>32)) {

											$fh = fopen($backup_mysql_filename, 'ab');
											fwrite($fh, $mysql_export_footer);
											fclose($fh);

											$backup_mysql_filename_filesize = myr_filesize($backup_mysql_filename);

											if ($backup_mysql_filename_filesize>=$api_max_file_filesize) {

												$backup_mysql_filename_x = $api_data_directory."/"."BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-x.sql";

												if (rename($backup_mysql_filename,$backup_mysql_filename_x)) {
												} else {
													if ($api_allow_exec=="1") {
														exec("mv ".@escapeshellarg($backup_mysql_filename)." ".@escapeshellarg($backup_mysql_filename_x));
														if (!file_exists($backup_mysql_filename_x)) {
															exec("move ".@escapeshellarg($backup_mysql_filename)." ".@escapeshellarg($backup_mysql_filename_x));
														}
													}
												}

												if (file_exists($backup_mysql_filename_x)) {

													$fp_r = fopen($backup_mysql_filename_x, 'rb');

													if (!$fp_r) {

														if ($api_allow_exec=="1") {
															if ($api_csplit=="1") {
																myr_test_exec("csplit");
															}
														}

														if (($api_allow_exec=="1") && ($api_csplit=="1")) {

															if ($api_allow_chmod=="1") {
																@chmod($backup_mysql_filename_x,0777);
															}

															$csplit_success = "0";

															$csplit_command = $api_csplit_path." -fSPLIT-BACKUP-".@escapeshellarg($date_strip)."-".@escapeshellarg($backup_database[$i]['id'])."-".@escapeshellarg($backup_database[$i]['dbname'])."-".@escapeshellarg($mysql_tables_list[$j])."- -k -n1 -s".@escapeshellarg($backup_mysql_filename_x)." '".'/^INSERT.*$/+5'."' {100000}";

															if ($enable_debug_log=="1") {

																exec("cd ".$api_data_directory."\n".$csplit_command,$csplit_response);
																$csplit_response = implode("\n",$csplit_response);
																$backup_log .= "\$csplit_response = /$csplit_response/\n\n";

															} else {

																exec("cd ".$api_data_directory."\n".$csplit_command);

															}

															$csplit_directory_packet_id = "0";

															if ($csplit_directory = opendir($api_data_directory."/")) {

																while (false !== ($csplit_directory_record = readdir($csplit_directory))) {

																	if (($csplit_directory_record!="..") && ($csplit_directory_record!=".")) {

																		if ($enable_debug_log=="1") {
																			$backup_log .= "csplit is_file: $api_data_directory/$csplit_directory_record\n";
																		}

																		if (is_file($api_data_directory."/".$csplit_directory_record)) {

																			if ($enable_debug_log=="1") {
																				$backup_log .= " csplit stristr($csplit_directory_record,SPLIT-BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-)\n\n";
																			}

																			if (stristr($csplit_directory_record,"SPLIT-BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-")) {

																				$csplit_directory_packet_id++;

																				$csplit_directory_record_id = str_replace("SPLIT-BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-","",$csplit_directory_record);
																				if (is_numeric($csplit_directory_record_id)) {
																				} else {
																					$csplit_directory_record_id = $csplit_directory_packet_id;
																				}

																				$csplit_directory_record_new = str_replace("SPLIT-BACKUP-","BACKUP-",$csplit_directory_record);
																				$csplit_directory_record_new .= ".sql";

																				rename($api_data_directory."/".$csplit_directory_record, $api_data_directory."/".$csplit_directory_record_new);

																				$mysql_export_header = "-- myRepono mySQL Export\n-- http://myrepono.com\n--\n-- $backup_mysql_date\n--\n-- Host: ".$backup_database[$i]['dbhost']."\n-- Database: ".$backup_database[$i]['dbname']."\n-- Table: ".$mysql_tables_list[$j]."\n-- Part: ".$csplit_directory_record_id."\n-- Generated with mysqldump\n--\n\n";

																				$mysql_export_footer = "\n\n-- End of mySQL export\n";

																				$fp_r = fopen($api_data_directory."/".$csplit_directory_record_new, 'rb');

																				$csplit_directory_record_new_tmp = $csplit_directory_record_new.".tmp";

																				$fp_w = fopen($api_data_directory."/".$csplit_directory_record_new_tmp, 'wb');

																				fwrite($fp_w, $mysql_export_header);

																				while ( ($data_line = fgets($fp_r)) !== false) {

																					fwrite($fp_r, $data_line);

																				}

																				fwrite($fp_w, $mysql_export_footer);

																				fclose($fp_w);

																				fclose($fp_r);

																				unlink($csplit_directory_record_new);

																				if (move($api_data_directory."/".$csplit_directory_record_new_tmp,$api_data_directory."/".$csplit_directory_record_new)) {
																				} else {
																					if ($api_allow_exec=="1") {
																						exec("mv ".@escapeshellarg($api_data_directory."/".$csplit_directory_record_new_tmp)." ".@escapeshellarg($api_data_directory."/".$csplit_directory_record_new));
																						if (file_exists($api_data_directory."/".$csplit_directory_record_new_tmp)) {
																							exec("move ".@escapeshellarg($api_data_directory."/".$csplit_directory_record_new_tmp)." ".@escapeshellarg($api_data_directory."/".$csplit_directory_record_new));
																						}
																					}
																				}

																				$mysql_export_content = "";

																				$mysql_export_files[] = $api_data_directory."/".$csplit_directory_record_new;

																				$csplit_success = "1";

																			}
																		}
																	}
																}
															} else {

																$mysql_export_files[] = $backup_mysql_filename_x;
																if ($enable_debug_log=="1") {
																	$backup_log .= "Unable to read API directory for split files.\n";
																}
															}

															if ($csplit_success!="1") {

																$mysql_export_files[] = $backup_mysql_filename_x;
																if ($enable_debug_log=="1") {
																	$backup_log .= "csplit of large mysqldump backup failed, command: $csplit_command\n";
																}
															} else {

																unlink($backup_mysql_filename_x);

															}

														} else {

															$mysql_export_files[] = $backup_mysql_filename_x;
															if ($enable_debug_log=="1") {
																$backup_log .= "Large mysqldump export could not be opened for split, and \$api_allow_exec is not enabled.\n";
															}
														}

													} else {

														$backup_mysql_filename_new = $api_data_directory."/"."BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-".$mysql_export_part.".sql";

														$mysql_export_header = "-- myRepono mySQL Export\n-- http://myrepono.com\n--\n-- $backup_mysql_date\n--\n-- Host: ".$backup_database[$i]['dbhost']."\n-- Database: ".$backup_database[$i]['dbname']."\n-- Table: ".$mysql_tables_list[$j]."\n-- Part: ".$mysql_export_part."\n-- Generated with mysqldump\n--\n\n";

														if (file_exists($backup_mysql_filename_new)) {
															unlink($backup_mysql_filename_new);
														}

														$fp_w = fopen($backup_mysql_filename_new, 'wb');
														fwrite($fp_w, $mysql_export_header);
														fclose($fp_w);
														$mysql_export_files[] = $backup_mysql_filename_new;

														$mysql_split_packet_size = ($api_max_file_filesize / 4) * 3;
														if ($mysql_split_packet_size<$api_mysql_packet_size) {
															$mysql_split_packet_size = $api_mysql_packet_size;
														}

														$total_fp_r_strlen = "0";
														$data = "";

														while ( ($data_line = fgets($fp_r)) !== false) {

															if ($total_fp_r_strlen!="0") {

																clearstatcache();
																$backup_mysql_filename_new_filesize = myr_filesize($backup_mysql_filename_new);

																if (($backup_mysql_filename_new_filesize>=$mysql_split_packet_size) || ($total_fp_r_strlen>=$mysql_split_packet_size)) {

																	$mysql_split_packet_hold = "0";

																	if ($mysql_split_packet_hold=="0") {

																		$fp_w = fopen($backup_mysql_filename_new, 'ab'); // append
																		fwrite($fp_w, $mysql_export_footer);
																		fclose($fp_w);

																		$mysql_export_part++;
																		$backup_mysql_filename_new = $api_data_directory."/"."BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-".$mysql_export_part.".sql";

																		$mysql_export_header = "-- myRepono mySQL Export\n-- http://myrepono.com\n--\n-- $backup_mysql_date\n--\n-- Host: ".$backup_database[$i]['dbhost']."\n-- Database: ".$backup_database[$i]['dbname']."\n-- Table: ".$mysql_tables_list[$j]."\n-- Part: ".$mysql_export_part."\n-- Generated with mysqldump\n--\n\n";

																		$total_fp_r_strlen = "0";

																		$fp_w = fopen($backup_mysql_filename_new, 'wb');
																		fwrite($fp_w, $mysql_export_header);
																		$mysql_export_files[] = $backup_mysql_filename_new;

																	} else {

																		$fp_w = fopen($backup_mysql_filename_new, 'ab'); // append

																	}

																} else {

																	$fp_w = fopen($backup_mysql_filename_new, 'ab'); // append

																}

															} else {

																$mysql_split_packet_hold = "0";
																$fp_w = fopen($backup_mysql_filename_new, 'ab'); // append

															}

															$total_fp_r_strlen = $total_fp_r_strlen + strlen($data_line);

															fwrite($fp_w, $data_line);

															fclose($fp_w);

														}

														if ($fp_w) {
															@fclose($fp_w);
														}
														fclose($fp_r);
														unlink($backup_mysql_filename_x);

													}

												} else {

													$mysql_export_files[] = $backup_mysql_filename;

													if ($enable_debug_log=="1") {
														$backup_log .= "Large mysqldump export could not be renamed for split.\n";
													}
												}

											} else {

												$mysql_export_files[] = $backup_mysql_filename;

											}
										}

									} else {

										$mysql_field_primary_keys = "";
										$mysql_field_secondary_keys = "";
										$mysql_field_auto_increment = "0";
										$mysql_field_array = array();

										if (($api_allow_mysqli=="1") && (@function_exists('mysqli_query'))) {

											$query = "SHOW TABLE STATUS LIKE '$mysql_tables_list[$j]'";

											if ($mysqli_query = mysqli_query($mysqli, $query)) {

												while ($mysqli_table = mysqli_fetch_array($mysqli_query, MYSQLI_ASSOC)) {

													$mysql_table_data_name = $mysqli_table['Name'];
													$mysql_table_data_engine = $mysqli_table['Engine'];
													$mysql_table_data_version = $mysqli_table['Version'];
													$mysql_table_data_row_format = $mysqli_table['Row_format'];
													$mysql_table_data_rows = $mysqli_table['Rows'];
													$mysql_table_data_avg_row_length = $mysqli_table['Avg_row_length'];
													$mysql_table_data_data_length = $mysqli_table['Data_length'];
													$mysql_table_data_max_data_length = $mysqli_table['Max_data_length'];
													$mysql_table_data_index_length = $mysqli_table['Index_length'];
													$mysql_table_data_data_free = $mysqli_table['Data_free'];
													$mysql_table_data_auto_increment = $mysqli_table['Auto_increment'];
													$mysql_table_data_create_time = $mysqli_table['Create_time'];
													$mysql_table_data_update_time = $mysqli_table['Update_time'];
													$mysql_table_data_check_time = $mysqli_table['Check_time'];
													$mysql_table_data_collation = $mysqli_table['Collation'];
													$mysql_table_data_checksum = $mysqli_table['Checksum'];
													$mysql_table_data_create_options = $mysqli_table['Create_options'];
													$mysql_table_data_comment = $mysqli_table['Comment'];

												}

												$mysql_table_data_charset = "utf8";
												if (function_exists('mysqli_character_set_name')) {
													if ($mysql_table_data_charset = mysqli_character_set_name($mysqli)) {
													} else {
														$mysql_table_data_charset = "utf8";
													}
												}

												$backup_log .= "Table charset: $mysql_table_data_charset\n";

												if (($enable_debug_log=="1") && ($backup_log!="")) {

													$backup_log .= "Memory usage: ".myr_memory_peak_usage()." bytes\n";
													$backup_log_filename = "BACKUP-".$date_strip.".log";
													$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
													fwrite($fh, $backup_log);
													fclose($fh);

												}

												$mysql_charset_changed = "0";
												$mysql_table_data_collation_explode = explode("_", $mysql_table_data_collation);
												$mysql_table_data_collation_explode = $mysql_table_data_collation_explode[0];
												if ($mysql_table_data_collation_explode!=$mysql_table_data_charset) {
													$allowed_table_data_charset = array(
														'big5' => 1,
														'dec8' => 1,
														'cp850' => 1,
														'hp8' => 1,
														'koi8r' => 1,
														'latin1' => 1,
														'latin2' => 1,
														'swe7' => 1,
														'ascii' => 1,
														'ujis' => 1,
														'sjis' => 1,
														'hebrew' => 1,
														'tis620' => 1,
														'euckr' => 1,
														'koi8u' => 1,
														'gb2312' => 1,
														'greek' => 1,
														'cp1250' => 1,
														'gbk' => 1,
														'latin5' => 1,
														'armscii8' => 1,
														'utf8' => 1,
														'ucs2' => 1,
														'cp866' => 1,
														'keybcs2' => 1,
														'macce' => 1,
														'macroman' => 1,
														'cp852' => 1,
														'latin7' => 1,
														'cp1251' => 1,
														'cp1256' => 1,
														'cp1257' => 1,
														'binary' => 1,
														'geostd8' => 1,
														'cp932' => 1,
														'eucjpms' => 1
													);
													if (isset($allowed_table_data_charset[$mysql_table_data_collation_explode])) {
														$mysql_table_data_charset = $mysql_table_data_collation_explode;
														if (function_exists('mysqli_set_charset')) {
															mysqli_set_charset($mysqli, $mysql_table_data_charset);
														}
														$mysql_charset_changed = "1";
													}
												}
											}

											$mysql_show_create = "0";
											$mysql_show_create_table = "";

											$query = "SHOW CREATE TABLE `$mysql_tables_list[$j]`;";

											if ($mysqli_query = mysqli_query($mysqli, $query)) {

												$number = mysqli_num_rows($mysqli_query);

												$backup_log = "SHOW CREATE: $number\n";

												while ($table_definition = mysqli_fetch_array($mysqli_query)) {

													$backup_log = "count(\$table_definition) = ".count($table_definition)."\n";

													$table_definition_count = count($table_definition);
													for ($k=1; $k<round($table_definition_count / 2); $k++) {

														if (isset($table_definition[$k])) {

															$mysql_show_create_table = $table_definition[$k];
															if ($mysql_show_create_table!="") {

																if (stristr($mysql_show_create_table,"CREATE TABLE")) {

																	if (!stristr($mysql_show_create_table,"CREATE TABLE IF NOT EXISTS ")) {
																		$mysql_show_create_table = str_replace("CREATE TABLE ", "CREATE TABLE IF NOT EXISTS ", $mysql_show_create_table);
																	}

																	$mysql_export .= "\n\n-- --------------------------------------------------------\n\n--\n-- Table structure for table `$mysql_tables_list[$j]` (SHOW)\n--\n\n";
																	$mysql_export .= $mysql_show_create_table.";\n\n";
																	$mysql_show_create = "1";
																}
																$mysql_show_create_table = "";

															}
														}
													}
												}
											}

											$mysql_null_fields = array();

											$query = "DESCRIBE `$mysql_tables_list[$j]`;";

											if ($mysqli_query = mysqli_query($mysqli, $query)) {

												$number = mysqli_num_rows($mysqli_query);

												if ($mysql_show_create=="0") {

													$mysql_export .= "\n\n-- --------------------------------------------------------\n\n--\n-- Table structure for table `$mysql_tables_list[$j]` (DESCRIBE)\n--\n\n";

													$mysql_export .= "CREATE TABLE IF NOT EXISTS `".$mysql_tables_list[$j]."` (";

												}

												$mysql_field_array = array();

												while ($mysqli_table = mysqli_fetch_array($mysqli_query, MYSQLI_ASSOC)) {

													$mysql_field_name = $mysqli_table['Field'];
													$mysql_field_type = $mysqli_table['Type'];
													$mysql_field_null = $mysqli_table['Null'];
													$mysql_field_key = $mysqli_table['Key'];
													$mysql_field_default = $mysqli_table['Default'];
													$mysql_field_extra = $mysqli_table['Extra'];

													if ($mysql_field_name!="") {

														$mysql_field_array[] = "$mysql_field_name";

													}

													if (strtolower($mysql_field_null)=="yes") {

														$mysql_null_fields[$mysql_field_name] = "1";

													}

													if ($mysql_show_create=="0") {

														$mysql_export .= "\n  `$mysql_field_name` $mysql_field_type";

														if (strtolower($mysql_field_null)=="yes") {


														} else {

															$mysql_export .= " NOT NULL";

															if ($mysql_field_default!="") {

																if ($mysql_field_default=="CURRENT_TIMESTAMP") {

																	$mysql_export .= " default $mysql_field_default";

																} else {

																	$mysql_export .= " default '$mysql_field_default'";

																}
															}
														}

														if (strtolower($mysql_field_extra)=="auto_increment") {

															$mysql_field_auto_increment = "1";

														}

														if ($mysql_field_extra!="") {

															$mysql_export .= " $mysql_field_extra";

														}
													}

													if ($mysql_field_key!="") {

														if ($mysql_field_name!="") {

															$mysql_field_primary_keys[] = "$mysql_field_name";

															if (strtolower($mysql_field_key)=="mul") {

																$mysql_field_secondary_keys = "1";

															}
														}
													}

													if ($k==($number - 1)) {
													} elseif ($mysql_show_create=="0") {

														$mysql_export .= ",";

													}
												}

												$mysql_field_primary_key = "";

												$mysql_field_primary_keys_count = count($mysql_field_primary_keys);

												if ($mysql_field_primary_keys_count>0) {

													if ($mysql_field_primary_keys_count=="1") {

														if (!isset($mysql_field_primary_keys[0])) {
															$mysql_field_primary_keys[0] = "";
														}

														if ($mysql_field_primary_keys[0]!="") {

															$mysql_field_primary_key = ",\n  PRIMARY KEY (`$mysql_field_primary_keys[0]`)";

														}

													} else {

														$mysql_field_primary_key = ",";
														$mysql_field_primary_key_list = "";

														if ($mysql_field_secondary_keys=="1") {

															for ($k=0; $k<$mysql_field_primary_keys_count; $k++) {

																if ($mysql_field_primary_keys[$k]!="") {

																	if ($mysql_field_primary_key_list!="") {

																		$mysql_field_primary_key_list .= ",";

																	}

																	if ($k>0) {

																		$mysql_field_primary_key .= ",";

																	}

																	$mysql_field_primary_key_list .= "`$mysql_field_primary_keys[$k]`";

																	$mysql_field_primary_key .= "\n  UNIQUE KEY `$mysql_field_primary_keys[$k]` ($mysql_field_primary_key_list)";

																}
															}

														} else {

															$mysql_field_primary_key_name = $mysql_field_primary_keys[0];

															for ($k=0; $k<$mysql_field_primary_keys_count; $k++) {

																if ($mysql_field_primary_keys[$k]!="") {

																	if ($mysql_field_primary_key_list!="") {

																		$mysql_field_primary_key_list .= ",";

																	}

																	$mysql_field_primary_key_list .= "`$mysql_field_primary_keys[$k]`";

																}
															}

															$mysql_field_primary_key .= "\n  UNIQUE KEY `$mysql_field_primary_key_name` ($mysql_field_primary_key_list)";

														}
													}
												}

												if ($mysql_table_data_engine=="") {

													$mysql_table_data_engine = "MyISAM";

												}

												if ($mysql_show_create=="0") {

													$mysql_export .= "$mysql_field_primary_key\n) ENGINE=$mysql_table_data_engine";

													if ($mysql_table_data_charset!="") {

														 $mysql_export .= " CHARSET=$mysql_table_data_charset";

													}

													if ($mysql_table_data_collation!="") {

														 $mysql_export .= " COLLATE=$mysql_table_data_collation";

													}

													if ($mysql_field_auto_increment=="1") {

														 $mysql_export .= " AUTO_INCREMENT=$mysql_table_data_auto_increment";

													}

													$mysql_export .= ";\n\n";

												}

												$mysql_export .= "--\n-- Dumping data for table `$mysql_tables_list[$j]`\n--\n";

												$mysql_field_array_count = count($mysql_field_array);

												$mysql_field_list = "";

												for ($k=0; $k<$mysql_field_array_count; $k++) {

													if ($mysql_field_array[$k]!="") {

														if ($mysql_field_list!="") {

															$mysql_field_list .= ", ";

														}

														$mysql_field_list .= "`$mysql_field_array[$k]`";

													}
												}

												$query = "SELECT $mysql_field_list FROM `$mysql_tables_list[$j]`;";

												if ($mysqli_query = mysqli_query($mysqli, $query)) {

													$number = mysqli_num_rows($mysqli_query);

													$mysql_export_part = "0";

													while ($mysqli_table = mysqli_fetch_array($mysqli_query, MYSQLI_ASSOC)) {

														$mysql_field_values_list = "";

														for ($l=0; $l<$mysql_field_array_count; $l++) {

															$mysql_field_array_value = $mysqli_table[$mysql_field_array[$l]];

															if ($mysql_field_values_list!="") {

																$mysql_field_values_list .= ", ";

															}

															$mysql_null_field_name = $mysql_field_array[$l];
															if ((isset($mysql_null_fields[$mysql_null_field_name])) && ($mysql_field_array_value=="")) {

																$mysql_field_values_list .= "NULL";

															} else {

																$mysql_field_values_list .= "'".mysqli_real_escape_string($mysqli, $mysql_field_array_value)."'";

															}
														}

														$mysql_export .= "\nINSERT INTO `$mysql_tables_list[$j]` ($mysql_field_list) VALUES ($mysql_field_values_list);";

														if (strlen($mysql_export)>$api_mysql_packet_size) {

															$mysql_export = "-- myRepono mySQL Export\n-- http://myrepono.com\n--\n-- $backup_mysql_date\n--\n-- Host: ".$backup_database[$i]['dbhost']."\n-- Database: ".$backup_database[$i]['dbname']."\n-- Table: ".$mysql_tables_list[$j]."\n-- Part: $mysql_export_part\n--\n\n".$mysql_export."\n\n-- End of mySQL export part $mysql_export_part\n";

															$backup_mysql_filename = $api_data_directory."/"."BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-".$mysql_export_part.".sql";

															$fh = fopen($backup_mysql_filename, 'wb');
															fwrite($fh, $mysql_export);
															fclose($fh);

															$mysql_export_files[] = $backup_mysql_filename;
															$mysql_export = "";

															$mysql_export_part++;

														}
													}

													if ($mysql_export!="") {

														$mysql_export = "-- myRepono mySQL Export\n-- http://myrepono.com\n--\n-- $backup_mysql_date\n--\n-- Host: ".$backup_database[$i]['dbhost']."\n-- Database: ".$backup_database[$i]['dbname']."\n-- Table: ".$mysql_tables_list[$j]."\n-- Part: $mysql_export_part\n--\n".$mysql_export."\n\n-- End of mySQL export part $mysql_export_part\n";

														$backup_mysql_filename = $api_data_directory."/"."BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-".$mysql_export_part.".sql";

														$fh = fopen($backup_mysql_filename, 'wb');
														fwrite($fh, $mysql_export);
														fclose($fh);

														$mysql_export_files[] = $backup_mysql_filename;

														$mysql_export = "";

													}
												}
											}

											if ($mysql_charset_changed=="1") {
												if (function_exists('mysqli_set_charset')) {
													mysqli_set_charset($mysqli, $mysql_charset);
												}
												$mysql_charset_changed = "0";
											}

										} else {

											$query = "SHOW TABLE STATUS LIKE '$mysql_tables_list[$j]'";
											$result = @mysql_query($query);
											$number = @mysql_num_rows($result);

											for ($k=0; $k<$number; $k++) {

												$mysql_table_data_name = @mysql_result($result,$k,"Name");
												$mysql_table_data_engine = @mysql_result($result,$k,"Engine");
												$mysql_table_data_version = @mysql_result($result,$k,"Version");
												$mysql_table_data_row_format = @mysql_result($result,$k,"Row_format");
												$mysql_table_data_rows = @mysql_result($result,$k,"Rows");
												$mysql_table_data_avg_row_length = @mysql_result($result,$k,"Avg_row_length");
												$mysql_table_data_data_length = @mysql_result($result,$k,"Data_length");
												$mysql_table_data_max_data_length = @mysql_result($result,$k,"Max_data_length");
												$mysql_table_data_index_length = @mysql_result($result,$k,"Index_length");
												$mysql_table_data_data_free = @mysql_result($result,$k,"Data_free");
												$mysql_table_data_auto_increment = @mysql_result($result,$k,"Auto_increment");
												$mysql_table_data_create_time = @mysql_result($result,$k,"Create_time");
												$mysql_table_data_update_time = @mysql_result($result,$k,"Update_time");
												$mysql_table_data_check_time = @mysql_result($result,$k,"Check_time");
												$mysql_table_data_collation = @mysql_result($result,$k,"Collation");
												$mysql_table_data_checksum = @mysql_result($result,$k,"Checksum");
												$mysql_table_data_create_options = @mysql_result($result,$k,"Create_options");
												$mysql_table_data_comment = @mysql_result($result,$k,"Comment");

											}

											$mysql_table_data_charset = "utf8";
											if (function_exists('mysql_client_encoding')) {
												if ($mysql_table_data_charset = @mysql_client_encoding()) {
												} else {
													$mysql_table_data_charset = "utf8";
												}
											}

											$mysql_charset_changed = "0";
											$mysql_table_data_collation_explode = explode("_", $mysql_table_data_collation);
											$mysql_table_data_collation_explode = $mysql_table_data_collation_explode[0];
											if ($mysql_table_data_collation_explode!=$mysql_table_data_charset) {
												$allowed_table_data_charset = array(
													'big5' => 1,
													'dec8' => 1,
													'cp850' => 1,
													'hp8' => 1,
													'koi8r' => 1,
													'latin1' => 1,
													'latin2' => 1,
													'swe7' => 1,
													'ascii' => 1,
													'ujis' => 1,
													'sjis' => 1,
													'hebrew' => 1,
													'tis620' => 1,
													'euckr' => 1,
													'koi8u' => 1,
													'gb2312' => 1,
													'greek' => 1,
													'cp1250' => 1,
													'gbk' => 1,
													'latin5' => 1,
													'armscii8' => 1,
													'utf8' => 1,
													'ucs2' => 1,
													'cp866' => 1,
													'keybcs2' => 1,
													'macce' => 1,
													'macroman' => 1,
													'cp852' => 1,
													'latin7' => 1,
													'cp1251' => 1,
													'cp1256' => 1,
													'cp1257' => 1,
													'binary' => 1,
													'geostd8' => 1,
													'cp932' => 1,
													'eucjpms' => 1
												);
												if (isset($allowed_table_data_charset[$mysql_table_data_collation_explode])) {
													$mysql_table_data_charset = $mysql_table_data_collation_explode;
													if (function_exists('mysql_set_charset')) {
														@mysql_set_charset($mysql_table_data_charset);
													}
													$mysql_charset_changed = "1";
												}
											}

											$mysql_show_create = "0";
											$mysql_show_create_table = "";

											$query = "SHOW CREATE TABLE `$mysql_tables_list[$j]`;";

											$result = @mysql_query($query);
											$number = @mysql_num_rows($result);

											$backup_log = "SHOW CREATE: $number\n";

											if ($number>0) {

												while ($table_definition = @mysql_fetch_row($result)) {

													$table_definition_count = count($table_definition);
													for ($k=1; $k<$table_definition_count; $k++) {

														$mysql_show_create_table = $table_definition[$k];
														if ($mysql_show_create_table!="") {

															if (stristr($mysql_show_create_table,"CREATE TABLE")) {

																if (!stristr($mysql_show_create_table,"CREATE TABLE IF NOT EXISTS ")) {
																	$mysql_show_create_table = str_replace("CREATE TABLE ", "CREATE TABLE IF NOT EXISTS ", $mysql_show_create_table);
																}

																$mysql_export .= "\n\n-- --------------------------------------------------------\n\n--\n-- Table structure for table `$mysql_tables_list[$j]` (SHOW)\n--\n\n";
																$mysql_export .= $mysql_show_create_table.";\n\n";
																$mysql_show_create = "1";
															}
															$mysql_show_create_table = "";

														}
													}
												}
											}

											$mysql_null_fields = array();

											$query = "DESCRIBE `$mysql_tables_list[$j]`;";

											$result = @mysql_query($query);
											$number = @mysql_num_rows($result);

											if ($mysql_show_create=="0") {

												$mysql_export .= "\n\n-- --------------------------------------------------------\n\n--\n-- Table structure for table `$mysql_tables_list[$j]` (DESCRIBE)\n--\n\n";

												$mysql_export .= "CREATE TABLE IF NOT EXISTS `".$mysql_tables_list[$j]."` (";

											}

											$mysql_field_array = array();

											for ($k=0; $k<$number; $k++) {

												$mysql_field_name = @mysql_result($result,$k,"Field");
												$mysql_field_type = @mysql_result($result,$k,"Type");
												$mysql_field_null = @mysql_result($result,$k,"Null");
												$mysql_field_key = @mysql_result($result,$k,"Key");
												$mysql_field_default = @mysql_result($result,$k,"Default");
												$mysql_field_extra = @mysql_result($result,$k,"Extra");

												if ($mysql_field_name!="") {

													$mysql_field_array[] = "$mysql_field_name";

												}

												if (strtolower($mysql_field_null)=="yes") {

													$mysql_null_fields[$mysql_field_name] = "1";

												}

												if ($mysql_show_create=="0") {

													$mysql_export .= "\n  `$mysql_field_name` $mysql_field_type";

													if (strtolower($mysql_field_null)=="yes") {


													} else {

														$mysql_export .= " NOT NULL";

														if ($mysql_field_default!="") {

															if ($mysql_field_default=="CURRENT_TIMESTAMP") {

																$mysql_export .= " default $mysql_field_default";

															} else {

																$mysql_export .= " default '$mysql_field_default'";

															}
														}
													}

													if (strtolower($mysql_field_extra)=="auto_increment") {

														$mysql_field_auto_increment = "1";

													}

													if ($mysql_field_extra!="") {

														$mysql_export .= " $mysql_field_extra";

													}
												}

												if ($mysql_field_key!="") {

													if ($mysql_field_name!="") {

														$mysql_field_primary_keys[] = "$mysql_field_name";

														if (strtolower($mysql_field_key)=="mul") {

															$mysql_field_secondary_keys = "1";

														}
													}
												}

												if ($k==($number - 1)) {
												} elseif ($mysql_show_create=="0") {

													$mysql_export .= ",";

												}
											}

											$mysql_field_primary_key = "";

											$mysql_field_primary_keys_count = count($mysql_field_primary_keys);

											if ($mysql_field_primary_keys_count>0) {

												if ($mysql_field_primary_keys_count=="1") {

													if (!isset($mysql_field_primary_keys[0])) {
														$mysql_field_primary_keys[0] = "";
													}

													if ($mysql_field_primary_keys[0]!="") {

														$mysql_field_primary_key = ",\n  PRIMARY KEY (`$mysql_field_primary_keys[0]`)";

													}

												} else {

													$mysql_field_primary_key = ",";
													$mysql_field_primary_key_list = "";

													if ($mysql_field_secondary_keys=="1") {

														for ($k=0; $k<$mysql_field_primary_keys_count; $k++) {

															if ($mysql_field_primary_keys[$k]!="") {

																if ($mysql_field_primary_key_list!="") {

																	$mysql_field_primary_key_list .= ",";

																}

																if ($k>0) {

																	$mysql_field_primary_key .= ",";

																}

																$mysql_field_primary_key_list .= "`$mysql_field_primary_keys[$k]`";

																$mysql_field_primary_key .= "\n  UNIQUE KEY `$mysql_field_primary_keys[$k]` ($mysql_field_primary_key_list)";

															}
														}

													} else {

														$mysql_field_primary_key_name = $mysql_field_primary_keys[0];

														for ($k=0; $k<$mysql_field_primary_keys_count; $k++) {

															if ($mysql_field_primary_keys[$k]!="") {

																if ($mysql_field_primary_key_list!="") {

																	$mysql_field_primary_key_list .= ",";

																}

																$mysql_field_primary_key_list .= "`$mysql_field_primary_keys[$k]`";

															}
														}

														$mysql_field_primary_key .= "\n  UNIQUE KEY `$mysql_field_primary_key_name` ($mysql_field_primary_key_list)";

													}
												}
											}

											if ($mysql_table_data_engine=="") {

												$mysql_table_data_engine = "MyISAM";

											}

											if ($mysql_show_create=="0") {

												$mysql_export .= "$mysql_field_primary_key\n) ENGINE=$mysql_table_data_engine";

												if ($mysql_table_data_charset!="") {

													 $mysql_export .= " CHARSET=$mysql_table_data_charset";

												}

												if ($mysql_table_data_collation!="") {

													 $mysql_export .= " COLLATE=$mysql_table_data_collation";

												}

												if ($mysql_field_auto_increment=="1") {

													 $mysql_export .= " AUTO_INCREMENT=$mysql_table_data_auto_increment";

												}

												$mysql_export .= ";\n\n";

											}

											$mysql_export .= "--\n-- Dumping data for table `$mysql_tables_list[$j]`\n--\n";

											$mysql_field_array_count = count($mysql_field_array);

											$mysql_field_list = "";

											for ($k=0; $k<$mysql_field_array_count; $k++) {

												if ($mysql_field_array[$k]!="") {

													if ($mysql_field_list!="") {

														$mysql_field_list .= ", ";

													}

													$mysql_field_list .= "`$mysql_field_array[$k]`";

												}
											}

											$query = "SELECT $mysql_field_list FROM `$mysql_tables_list[$j]`;";

											$result = @mysql_query($query);
											$number = @mysql_num_rows($result);

											$mysql_export_part = "0";

											for ($k=0; $k<$number; $k++) {

												$mysql_field_values_list = "";

												for ($l=0; $l<$mysql_field_array_count; $l++) {

													$mysql_field_array_value = @mysql_result($result,$k,"$mysql_field_array[$l]");

													if ($mysql_field_values_list!="") {

														$mysql_field_values_list .= ", ";

													}

													$mysql_null_field_name = $mysql_field_array[$l];
													if ((isset($mysql_null_fields[$mysql_null_field_name])) && ($mysql_field_array_value=="")) {

														$mysql_field_values_list .= "NULL";

													} else {

														$mysql_field_values_list .= "'".myr_safe_string($mysql_field_array_value, "0")."'";

													}
												}

												$mysql_export .= "\nINSERT INTO `$mysql_tables_list[$j]` ($mysql_field_list) VALUES ($mysql_field_values_list);";

												if (strlen($mysql_export)>$api_mysql_packet_size) {

													$mysql_export = "-- myRepono mySQL Export\n-- http://myrepono.com\n--\n-- $backup_mysql_date\n--\n-- Host: ".$backup_database[$i]['dbhost']."\n-- Database: ".$backup_database[$i]['dbname']."\n-- Table: ".$mysql_tables_list[$j]."\n-- Part: $mysql_export_part\n--\n\n".$mysql_export."\n\n-- End of mySQL export part $mysql_export_part\n";

													$backup_mysql_filename = $api_data_directory."/"."BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-".$mysql_export_part.".sql";

													$fh = fopen($backup_mysql_filename, 'wb');
													fwrite($fh, $mysql_export);
													fclose($fh);

													$mysql_export_files[] = $backup_mysql_filename;
													$mysql_export = "";

													$mysql_export_part++;

												}
											}

											if ($mysql_export!="") {

												$mysql_export = "-- myRepono mySQL Export\n-- http://myrepono.com\n--\n-- $backup_mysql_date\n--\n-- Host: ".$backup_database[$i]['dbhost']."\n-- Database: ".$backup_database[$i]['dbname']."\n-- Table: ".$mysql_tables_list[$j]."\n-- Part: $mysql_export_part\n--\n".$mysql_export."\n\n-- End of mySQL export part $mysql_export_part\n";

												$backup_mysql_filename = $api_data_directory."/"."BACKUP-$date_strip"."-".$backup_database[$i]['id']."-".$backup_database[$i]['dbname']."-".$mysql_tables_list[$j]."-".$mysql_export_part.".sql";

												$fh = fopen($backup_mysql_filename, 'wb');
												fwrite($fh, $mysql_export);
												fclose($fh);

												$mysql_export_files[] = $backup_mysql_filename;

												$mysql_export = "";

											}

											if ($mysql_charset_changed=="1") {
												if (function_exists('mysql_set_charset')) {
													mysql_set_charset($mysql_charset);
												}
												$mysql_charset_changed = "0";
											}
										}
									}
								}
							}

							if (($api_allow_mysqli=="1") && (@function_exists('mysqli_query'))) {

								mysqli_close($mysqli);

							} else {

								@mysql_close();

							}
						}
					}
				}

				$cache_backup_instructions['mysql_export_files'] = $mysql_export_files;

				myr_serialize_save($api_data_directory."/".basename("CACHE-$request_session.dat"), $cache_backup_instructions);
				$cache_backup_instructions = array();

				if (($enable_debug_log=="1") && ($backup_log!="")) {

					$backup_log .= "Memory usage: ".myr_memory_peak_usage()." bytes\n";
					$backup_log_filename = "BACKUP-".$date_strip.".log";
					$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
					fwrite($fh, $backup_log);
					fclose($fh);

				}

				print "1|$request_backup_action|$backup_db_count|$request_backup_action_packet|".round($backup_db_tables_count)."|";

				exit;


			} elseif ($request_backup_action=="END") {


				$cache_backup_instructions = array();

				if (file_exists($api_data_directory."/".basename("CACHE-$request_session.dat"))) {

					$cache_backup_instructions = myr_serialize_open($api_data_directory."/".basename("CACHE-$request_session.dat"));

				} else {

					print "0";
					exit;

				}

				$index_packets = array(
					'packets_count' => "1",
					'total_filesize' => "0",
					'packet_filesizes' => array()
				);

				if (file_exists($api_data_directory."/".basename("INDEX-$request_session.dat"))) {

					$index_packets = myr_serialize_open($api_data_directory."/".basename("INDEX-$request_session.dat"));

				}

				if (!isset($index_packets['packets_count'])) {
					$index_packets['packets_count'] = "1";
				} elseif (!is_numeric($index_packets['packets_count'])) {
					$index_packets['packets_count'] = "1";
				}
				if (!isset($index_packets['total_filesize'])) {
					$index_packets['total_filesize'] = "0";
				}
				if (!isset($index_packets['packet_filesizes'])) {
					$index_packets['packet_filesizes'] = array();
				}

				$multi_stage_index = "0/0";
				$multi_stage_index = explode("/",$multi_stage_index);
				if (!isset($multi_stage_index[0])) {
					$multi_stage_index[0] = "0";
				}
				if (!isset($multi_stage_index[1])) {
					$multi_stage_index[1] = "0";
				}
				$multi_stage_index_end = "1";

				if (isset($_GET["msi"])) {

					$multi_stage_index = myr_safe_string(strip_tags($_GET["msi"]));
					$multi_stage_index = explode("/",$multi_stage_index);

					if (!isset($multi_stage_index[0])) {
						$multi_stage_index[0] = "0";
					}
					if (!isset($multi_stage_index[1])) {
						$multi_stage_index[1] = "0";
					}

				}

				if ((isset($_GET["mfp"])) && (is_numeric($_GET["mfp"]))) {

					$api_max_files_per_process_tmp = myr_safe_string(strip_tags($_GET["mfp"]));

					if (($api_max_files_per_process_tmp>0) && ($api_max_files_per_process_tmp<10000)) {

						$api_max_files_per_process = $api_max_files_per_process_tmp;

					}
				}

				$backup_filesize = "0";
				$backup_packet_filesize = "0";

				if (isset($index_packets['total_filesize'])) {
					$backup_filesize = $index_packets['total_filesize'];
					$backup_log .= "0: \$index_packets[total_filesize] = ".$index_packets['total_filesize']."\n";
				}

				$backup_packet_count = $index_packets['packets_count'] - 1;
				$index_packet_id = $backup_packet_count;

				while (file_exists($api_data_directory."/".basename("INDEX-".$request_session."-".($index_packet_id+1).".dat"))) {

					$index_packets['packets_count']++;
					$backup_packet_count = $index_packets['packets_count'];
					$index_packet_id = $backup_packet_count;

				}

				if (file_exists($api_data_directory."/".basename("INDEX-".$request_session."-".$index_packet_id.".dat"))) {

					$backup_packet = myr_serialize_open($api_data_directory."/".basename("INDEX-".$request_session."-".$index_packet_id.".dat"));

					//if (isset($backup_packet['MYR-BF'])) {
					//	$backup_filesize = $backup_packet['MYR-BF'];
					//	unset($backup_packet['MYR-BF']);
					//}
					//if (isset($backup_packet['MYR-BPF'])) {
					//	$backup_packet_filesize = $backup_packet['MYR-BPF'];
					//	unset($backup_packet['MYR-BPF']);
					//}

				} else {

					$backup_packet = array();

				}

				if (isset($index_packets['packet_filesizes'][$index_packet_id])) {
					$backup_packet_filesize = $index_packets['packet_filesizes'][$index_packet_id];
				}

				$backup_filenames = array();
				if (isset($cache_backup_instructions['backup_filenames'])) {

					$backup_filenames = $cache_backup_instructions['backup_filenames'];

				}
				$backup_total_files = count($backup_filenames);

				$backup_database = array();
				if (isset($cache_backup_instructions['backup_database'])) {

					$backup_database = $cache_backup_instructions['backup_database'];

				}
				$backup_db_count = count($backup_database);

				$backup_database_table = array();
				if (isset($cache_backup_instructions['backup_database_table'])) {

					$backup_database_table = $cache_backup_instructions['backup_database_table'];

				}

				$mysql_export_files = array();
				if (isset($cache_backup_instructions['mysql_export_files'])) {

					$mysql_export_files = $cache_backup_instructions['mysql_export_files'];

				}

				$backup_file_excludes = array();
				if (isset($cache_backup_instructions['backup_file_excludes'])) {
					$backup_file_excludes = $cache_backup_instructions['backup_file_excludes'];
				}
				$backup_file_excludes_count = count($backup_file_excludes);

				$cache_backup_instructions = array();

				$backup_filenames_count = count($backup_filenames);
				if ($backup_filenames_count!=$backup_total_files) {
					if ($enable_debug_log=="1") {
						$backup_log .= "ERROR: File counts do not match.\nCheck count: $backup_total_files\nActual files: $backup_filenames_count\n\n";
					}
				}

				if ($backup_filenames_count>0) {

					if (($multi_stage_index[0]=="0") && ($multi_stage_index[1]=="0")) {

						$mysql_export_files_count = count($mysql_export_files);

						if ($mysql_export_files_count>0) {

							if ($enable_debug_log=="1") {
								$backup_log .= "Databases: $mysql_export_files_count\n";
							}

							for ($i=0; $i<$mysql_export_files_count; $i++) {

								if (isset($mysql_export_files[$i])) {

									if ($mysql_export_files[$i]!="") {

										$backup_file_filesize = "0";
										if (file_exists($mysql_export_files[$i])) {
											$backup_file_filesize = myr_filesize($mysql_export_files[$i]);
										}

										$backup_packet_filesize = $backup_packet_filesize + $backup_file_filesize;
										$backup_filesize = $backup_filesize + $backup_file_filesize;

										if ($enable_debug_log=="1") {
											$backup_log .= "$mysql_export_files[$i]\n";
										}

										if (!isset($backup_packet[$backup_packet_count])) {
											$backup_packet[$backup_packet_count] = array();
										}

										if (($backup_packet_filesize>$api_packet_filesize) || (count($backup_packet[$backup_packet_count])>$api_packet_files)) {
											if (count($backup_packet[$backup_packet_count])=="0") {
											} else {

												//if ($multi_stage_index_end!="1") {
												//	$backup_packet['MYR-BF'] = $backup_filesize - $backup_file_filesize;
												//	$backup_packet['MYR-BPF'] = $backup_packet_filesize - $backup_file_filesize;
												//}

												$index_packets['total_filesize'] = $backup_filesize;
												$index_packets['packet_filesizes'][$index_packet_id] = $backup_packet_filesize - $backup_file_filesize;

												myr_serialize_save($api_data_directory."/".basename("INDEX-".$request_session."-".$index_packet_id.".dat"), $backup_packet);

												myr_serialize_save($api_data_directory."/".basename("INDEX-$request_session.dat"), $index_packets);

												$backup_packet_count++;
												$backup_packet_filesize = $backup_file_filesize;

												$index_packet_id++;
												$index_packets['packets_count']++;

												$backup_packet = array();

											}
										}

										$backup_packet[$backup_packet_count][] = $mysql_export_files[$i];

									}
								}
							}

							if ($enable_debug_log=="1") {
								$backup_log .= "\n";
							}
						}
					}

					if ($enable_debug_log=="1") {
						$backup_log .= "Files: $backup_filenames_count\n";
					}

					$myrepono_home_file_path = dirname(__FILE__);
					$myrepono_home_file_path = str_replace('\\','/',$myrepono_home_file_path);
					$myrepono_home_file_path = str_replace('//','/',$myrepono_home_file_path);

					$backup_excluded_files = array();

					$i_start = "0";
					if ($multi_stage_index[0]>0) {
						$i_start = $multi_stage_index[0];
						if ($i_start<0) {
							$i_start = "0";
						}
					}
					$j_start = "0";
					if ($multi_stage_index[1]>0) {
						$j_start = $multi_stage_index[1];
						if ($j_start<0) {
							$j_start = "0";
						}
					}
					$j_start_tmp = $j_start;

					$process_file_count = "0";

					for ($i=$i_start; $i<$backup_filenames_count; $i++) {

						if ($multi_stage_index_end=="0") {

							$i = "99999999";

						} else {

							if (isset($backup_filenames[$i])) {

								$backup_filenames[$i] = str_replace('\\','/',$backup_filenames[$i]);
								$backup_filenames[$i] = str_replace('//','/',$backup_filenames[$i]);

								if (is_dir($backup_filenames[$i])) {

									if ((dirname($backup_filenames[$i])==$myrepono_home_file_path) || (stristr($backup_filenames[$i],$myrepono_home_file_path))) {

										if ($enable_debug_log=="1") {
											$backup_log .= "Skipped: $backup_filenames[$i]\n";
										}

									} else {

										$backup_file_excludes_match = "0";

										if ($backup_file_excludes_count>0) {
											for ($k=0; $k<$backup_file_excludes_count; $k++) {
												if (isset($backup_file_excludes[$k])) {
													$directory_wildcard = $backup_filenames[$i];
													if ((is_dir($directory_wildcard)) && (substr($directory_wildcard,strlen($directory_wildcard)-1,1)!="/")) {
														$directory_wildcard .= "/";
													}
													$backup_log .= "Excl: $directory_wildcard,$backup_file_excludes[$k]\n";
													if (myr_wildcard($directory_wildcard,$backup_file_excludes[$k])) {
														$backup_file_excludes_match = "1";
													}
												}
											}
										}

										if ($backup_file_excludes_match=="1") {

											$backup_file_excludes_match = "0";
											//$backup_excluded_files[] = $backup_filenames[$i];

											if ($enable_debug_log=="1") {
												$backup_log .= "Excluded: $backup_filenames[$i]\n";
											}

										} elseif ((file_exists($backup_filenames[$i])) && (is_readable($backup_filenames[$i]))) {

											$directory_files_list = "";
											$tmp = directory_files_cache($backup_filenames[$i], $backup_file_excludes);

											for ($j=$j_start; $j<count($directory_files_list); $j++) {

												$directory_files_list[$j] = str_replace('\\','/',$directory_files_list[$j]);
												$directory_files_list[$j] = str_replace('//','/',$directory_files_list[$j]);

												if (($multi_stage_index_end=="0") || ($j>($api_max_files_per_process + $j_start_tmp))) {

													$multi_stage_index[0] = $i;
													$multi_stage_index[1] = $j;
													$multi_stage_index_end = "0";

													$i = "99999999999";
													$j = "99999999999";

												} elseif ($directory_files_list[$j]!="") {

													$j_start = "0";

													$process_file_count++;
													if ($process_file_count>$api_max_files_per_process) {
														$api_max_files_per_process = "1";
													}

													$backup_file_excludes_match = "0";

													if (stristr($directory_files_list[$j],$myrepono_home_file_path)) {
														$backup_file_excludes_match = "1";
													} elseif ($backup_file_excludes_count>0) {
														for ($k=0; $k<$backup_file_excludes_count; $k++) {
															if (isset($backup_file_excludes[$k])) {
																$directory_wildcard = $directory_files_list[$j];
																if ((is_dir($directory_wildcard)) && (substr($directory_wildcard,strlen($directory_wildcard)-1,1)!="/")) {
																	$directory_wildcard .= "/";
																}
																$backup_log .= "Excl2: $directory_wildcard,$backup_file_excludes[$k]\n";
																if (myr_wildcard($directory_wildcard,$backup_file_excludes[$k])) {
																	$backup_file_excludes_match = "1";
																}
															}
														}
													}

													if ($backup_file_excludes_match=="1") {

														$backup_file_excludes_match = "0";
														//$backup_excluded_files[] = $directory_files_list[$j];

														if ($enable_debug_log=="1") {
															$backup_log .= "Excluded: $directory_files_list[$j]\n";
														}

													} else {

														$backup_file_filesize = myr_filesize($directory_files_list[$j]);

														if ($backup_file_filesize<=($api_max_file_filesize * 10000)) {

															$backup_packet_filesize = $backup_packet_filesize + $backup_file_filesize;
															$backup_filesize = $backup_filesize + $backup_file_filesize;

															if ($enable_debug_log=="1") {
																$backup_log .= "Found: $directory_files_list[$j]\n";
															}

															if (!isset($backup_packet[$backup_packet_count])) {
																$backup_packet[$backup_packet_count] = array();
															}

															if ((($backup_packet_filesize>$api_packet_filesize) && ($backup_packet_filesize>$backup_file_filesize)) || (count($backup_packet[$backup_packet_count])>$api_packet_files)) {
																if (count($backup_packet[$backup_packet_count])=="0") {
																} else {

																	//if ($multi_stage_index_end!="1") {
																	//	$backup_packet['MYR-BF'] = $backup_filesize - $backup_file_filesize;
																	//	$backup_packet['MYR-BPF'] = $backup_packet_filesize - $backup_file_filesize;
																	//}

																	$index_packets['total_filesize'] = $backup_filesize;
																	$index_packets['packet_filesizes'][$index_packet_id] = $backup_packet_filesize - $backup_file_filesize;

																	myr_serialize_save($api_data_directory."/".basename("INDEX-".$request_session."-".$index_packet_id.".dat"), $backup_packet);

																	myr_serialize_save($api_data_directory."/".basename("INDEX-$request_session.dat"), $index_packets);

																	$backup_packet_count++;
																	$backup_packet_filesize = $backup_file_filesize;

																	$index_packet_id++;
																	$index_packets['packets_count']++;

																	$backup_packet = array();
																}
															}

															$backup_packet[$backup_packet_count][] = $directory_files_list[$j];

														} else {

															if ($enable_debug_log=="1") {
																$backup_log .= "Individual max filesize exceeded:  $backup_filenames[$i] ($backup_file_filesize bytes)\n";
															}
														}
													}
												}
											}

										} else {
											if ($enable_debug_log=="1") {
												$backup_log .= "Not found: $backup_filenames[$i]\n";
											}
										}
									}

								} else {

									$process_file_count++;
									if ($process_file_count>$api_max_files_per_process) {
										$api_max_files_per_process = "1";
									}

									$backup_file_excludes_match = "0";

									if ((dirname($backup_filenames[$i])==$myrepono_home_file_path) || (stristr($backup_filenames[$i],$myrepono_home_file_path))) {
										$backup_file_excludes_match = "1";
									} elseif ($backup_file_excludes_count>0) {
										for ($k=0; $k<$backup_file_excludes_count; $k++) {
											if (isset($backup_file_excludes[$k])) {
												if (myr_wildcard($backup_filenames[$i],$backup_file_excludes[$k])) {
													$backup_file_excludes_match = "1";
												}
											}
										}
									}

									if ($backup_file_excludes_match=="1") {

										$backup_file_excludes_match = "0";
										//$backup_excluded_files[] = $backup_filenames[$i];

										if ($enable_debug_log=="1") {
											$backup_log .= "Excluded: $backup_filenames[$i]\n";
										}

									} else {

										if ($backup_filenames[$i]!="") {

											if ((file_exists($backup_filenames[$i])) && (is_readable($backup_filenames[$i]))) {

												$backup_file_filesize = myr_filesize($backup_filenames[$i]);

												if ($backup_file_filesize<=($api_max_file_filesize * 10000)) {

													$backup_packet_filesize = $backup_packet_filesize + $backup_file_filesize;
													$backup_filesize = $backup_filesize + $backup_file_filesize;

													if ($enable_debug_log=="1") {
														$backup_log .= "Found: $backup_filenames[$i]\n";
													}

													if (!isset($backup_packet[$backup_packet_count])) {
														$backup_packet[$backup_packet_count] = array();
													}

													if ((($backup_packet_filesize>$api_packet_filesize) && ($backup_packet_filesize>$backup_file_filesize)) || (count($backup_packet[$backup_packet_count])>$api_packet_files)) {
														if (count($backup_packet[$backup_packet_count])=="0") {
														} else {

															//if ($multi_stage_index_end!="1") {
															//	$backup_packet['MYR-BF'] = $backup_filesize - $backup_file_filesize;
															//	$backup_packet['MYR-BPF'] = $backup_packet_filesize - $backup_file_filesize;
															//}

															$index_packets['total_filesize'] = $backup_filesize;
															$index_packets['packet_filesizes'][$index_packet_id] = $backup_packet_filesize - $backup_file_filesize;

															myr_serialize_save($api_data_directory."/".basename("INDEX-".$request_session."-".$index_packet_id.".dat"), $backup_packet);

															myr_serialize_save($api_data_directory."/".basename("INDEX-$request_session.dat"), $index_packets);

															$backup_packet_count++;
															$backup_packet_filesize = $backup_file_filesize;

															$index_packet_id++;
															$index_packets['packets_count']++;

															$backup_packet = array();

														}
													}

													$backup_packet[$backup_packet_count][] = $backup_filenames[$i];

												} else {

													if ($enable_debug_log=="1") {
														$backup_log .= "Individual max filesize exceeded:  $backup_filenames[$i] ($backup_file_filesize bytes)\n";
													}
												}

											} elseif ($enable_debug_log=="1") {
												$backup_log .= "Not found: $backup_filenames[$i]\n";
											}
										}
									}
								}
							}
						}
					}
				}

				//if ($multi_stage_index_end!="1") {
				//	$backup_packet['MYR-BF'] = $backup_filesize;
				//	$backup_packet['MYR-BPF'] = $backup_packet_filesize;
				//}

				$index_packets['total_filesize'] = $backup_filesize;
				$index_packets['packet_filesizes'][$index_packet_id] = $backup_packet_filesize;

				if (!file_exists($api_data_directory."/".basename("SQL-$request_session.dat"))) {
					myr_serialize_save($api_data_directory."/".basename("SQL-$request_session.dat"), $mysql_export_files);
				}

				if (count($backup_packet[$backup_packet_count])>0) {

					myr_serialize_save($api_data_directory."/".basename("INDEX-".$request_session."-".$index_packet_id.".dat"), $backup_packet);

				}

				myr_serialize_save($api_data_directory."/".basename("INDEX-$request_session.dat"), $index_packets);

				$multi_stage_index_prompt = "1";
				if ($multi_stage_index_end=="1") {
					$multi_stage_index_prompt = "0";
				}

				print "BACKUP-$date_strip||\n$backup_filesize||\nPACKETS:".($backup_packet_count+1)."||\n$multi_stage_index_prompt:".$multi_stage_index[0]."/".$multi_stage_index[1]."||\n";

				/*if (count($backup_excluded_files)>0) {

					$backup_excluded_files = serialize($backup_excluded_files);
					$backup_excluded_files = str_replace("\n", "", $backup_excluded_files);
					$backup_excluded_files = str_replace("\r", "", $backup_excluded_files);
					$backup_excluded_files = str_replace("||", "|", $backup_excluded_files);
					$backup_excluded_files = str_replace("||", "|", $backup_excluded_files);

					print "FEXCL:".$backup_excluded_files."||\n";

				}*/

				if (($backup_log!="") && ($enable_debug_log=="1")) {

					$backup_log .= "Memory usage: ".myr_memory_peak_usage()." bytes\n";
					$backup_log_filename = "BACKUP-".$date_strip.".log";
					$fh = fopen($api_data_directory."/".$backup_log_filename, 'a');
					fwrite($fh, $backup_log);
					fclose($fh);

				}

				if ($multi_stage_index_end=="1") {
					if (file_exists($api_data_directory."/".basename("CACHE-$request_session.dat"))) {

						unlink($api_data_directory."/".basename("CACHE-$request_session.dat"));

					}
				}

				exit;
			}
		}
	}

	exit;

}


function encrypt_file($file, $key) {

	global $api_mcrypt_rijndael, $api_data_directory, $api_read_length;

	if (file_exists($file)) {

		$fp_r = fopen($file, 'rb');
		if ($fp_r) {
			$file_contents = fread($fp_r, 16);
			if (substr($file_contents,0,8)=='crypt000') {

				return 0;

			}
			fclose($fp_r);
		} else {
			return 0;
		}

		$encrypt_file = $file."-crypt";

		$encryption_method = "1";
		if ($api_mcrypt_rijndael=="1") {
			if ((function_exists('mcrypt_module_open')) && (function_exists('mcrypt_create_iv')) && (function_exists('mcrypt_generic')) && (function_exists('hash_hmac')) && (function_exists('md5'))) {
				$encryption_method = "3";
			}
		}

		if ($encryption_method=="1") {

			$file_contents = "";

			$fp_r = fopen($file, 'rb');
			$fp_w = fopen($encrypt_file, 'wb');
			fwrite($fp_w, 'crypt0001');
			encrypt_codec($fp_r, $fp_w, $key);
			fclose($fp_r);
			fclose($fp_w);

		} elseif ($encryption_method=="3") {

			$crypt_password = $key;
			$crypt_salt = "D0Ge59cWQVP6SsYIX1fsF54621cS";

			if ($mcrypt_key = mcrypt_key($crypt_password, $crypt_salt, 1000, 32)) {

				if ($mcrypt_cipher = mcrypt_module_open('rijndael-256', '', 'cbc', '')) {

					$mcrypt_iv = md5($crypt_salt);

					mcrypt_generic_init($mcrypt_cipher, $mcrypt_key, $mcrypt_iv);

					if ($fp_r = fopen($file, 'rb')) {

						if ($fp_w = fopen($encrypt_file, 'wb')) {

							fwrite($fp_w, 'crypt0003');

							while (!feof($fp_r)) {

								$buff = fread($fp_r, $api_read_length);
								if ($buff!="") {
									$output = mcrypt_generic($mcrypt_cipher, $buff);
									fwrite($fp_w, $output);
								}
								$output = "";

							}

							fclose($fp_w);

						} else {
							return 0;
						}

						fclose($fp_r);

					} else {
						return 0;
					}
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		}

		return 1;

	} else {

		return 0;

	}
}


function file_dates($file_path) {

	clearstatcache();

	$stat_data = stat($file_path);

	$stats = "";

	if (($stat_data[9]!="") && ($stat_data[9]!="0")) {
		$stats .= date("Y-m-d H:i:s", $stat_data[9]);
	}

	return $stats;

}


function file_owners($file_path) {

	clearstatcache();
	$owner = fileowner($file_path);

	if (function_exists('posix_getpwuid')) {
		$owner = posix_getpwuid($owner);
		$owner = $owner['name'];
	} else {
		$owner = "0";
	}

	if (($owner=="0") || ($owner=="")) {

		$owner = "";

	}

return $owner;

}


function file_permissions($file_path) {

	clearstatcache();
	$perms = fileperms($file_path);

	if (($perms & 0xC000) == 0xC000) {
		$info = 's';
	} elseif (($perms & 0xA000) == 0xA000) {
		$info = 'l';
	} elseif (($perms & 0x8000) == 0x8000) {
		$info = '-';
	} elseif (($perms & 0x6000) == 0x6000) {
		$info = 'b';
	} elseif (($perms & 0x4000) == 0x4000) {
		$info = 'd';
	} elseif (($perms & 0x2000) == 0x2000) {
		$info = 'c';
	} elseif (($perms & 0x1000) == 0x1000) {
		$info = 'p';
	} else {
		$info = 'u';
	}

	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
				(($perms & 0x0800) ? 's' : 'x' ) :
				(($perms & 0x0800) ? 'S' : '-'));

	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
				(($perms & 0x0400) ? 's' : 'x' ) :
				(($perms & 0x0400) ? 'S' : '-'));

	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
				(($perms & 0x0200) ? 't' : 'x' ) :
				(($perms & 0x0200) ? 'T' : '-'));

	$chmod['0'] = "---";
	$chmod['1'] = "--x";
	$chmod['2'] = "-w-";
	$chmod['3'] = "-wx";
	$chmod['4'] = "r--";
	$chmod['5'] = "r-x";
	$chmod['6'] = "rw-";
	$chmod['7'] = "rwx";

	$chmod_string = $info;

	$field['chmod_1'] = $chmod_string[1].$chmod_string[2].$chmod_string[3];
	$field['chmod_2'] = $chmod_string[4].$chmod_string[5].$chmod_string[6];
	$field['chmod_3'] = $chmod_string[7].$chmod_string[8].$chmod_string[9];

	for ($i=0; $i<8; $i++) {

		if ($field['chmod_1']==$chmod[$i]) {
			$field['chmod_1'] = $i;
		}

		if ($field['chmod_2']==$chmod[$i]) {
			$field['chmod_2'] = $i;
		}

		if ($field['chmod_3']==$chmod[$i]) {
			$field['chmod_3'] = $i;
		}
	}

	$info .= "/".$field['chmod_1'].$field['chmod_2'].$field['chmod_3'];

	return $info;

}


function random_string($length,$chars="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {

	$string = "";

	for ($i=0; $i<=$length-1; $i++) {

		$string .= $chars[rand(0,strlen($chars)-1)];

	}

	return $string;

}


function database_error($error_string) {

	print $error_string;
	exit;

}


function directorysize($directory_path, $count = "0") {

	global $api_allow_exec, $api_allow_chmod;

	$directory_path = str_replace('\\','/',$directory_path);
	$directory_path = str_replace('//','/',$directory_path);

	$directorysize[$count] = "0";

	if ($count<100) {

		if ($dsdirectory[$count] = opendir($directory_path)) {

			while (false !== ($dsdirectory_record[$count] = readdir($dsdirectory[$count]))) {

				if (($dsdirectory_record[$count]!="..") && ($dsdirectory_record[$count]!=".")) {

					if (is_dir("$directory_path/$dsdirectory_record[$count]")) {

						$file_size = directorysize("$directory_path$dsdirectory_record[$count]/",($count + 1));

						$directorysize[$count] = $directorysize[$count] + $file_size;

					} elseif (is_file("$directory_path$dsdirectory_record[$count]")) {

						$file_size = myr_filesize("$directory_path$dsdirectory_record[$count]");

						$directorysize[$count] = $directorysize[$count] + $file_size;

					}
				}
			}
		}
	}

	return $directorysize[$count];

}


function directory_files_cache($directory_path, $file_excludes = array()) {

	global $directory_files_list, $api_data_directory;

	$directory_path = str_replace('\\','/',$directory_path);
	$directory_path = str_replace('//','/',$directory_path);

	$cache_filename = $api_data_directory."/CACHE-INDEX-".strtoupper(md5($directory_path)).".dat";

	$directory_files = "0";
	$directory_files_list = array();

	if (file_exists($cache_filename)) {
		if (filemtime($cache_filename)>time()-600) {

			$directory_files = file_get_contents($cache_filename);
			$directory_files_list = unserialize($directory_files);

			$directory_files = count($directory_files_list);
		}
	}

	if (count($directory_files_list)=="0") {

		$directory_files = directory_files_index($directory_path, "0", $file_excludes);

		if (file_exists($cache_filename)) {
			unlink($cache_filename);
		}

		if (count($directory_files_list)<25000) {

			myr_serialize_save($cache_filename, $directory_files_list);

		}
	}

	return $directory_files;

}


function directory_files_index($directory_path, $count = "0", $file_excludes = array()) {

	global $directory_files_list;

	$directory_path = str_replace('\\','/',$directory_path);
	$directory_path = str_replace('//','/',$directory_path);

	$directory_files[$count] = "0";

	$file_excludes_count = count($file_excludes);

	if (@is_readable($directory_path)) {

		if (is_dir($directory_path)) {

			if ($dsdirectory[$count] = opendir($directory_path)) {

				while (false !== ($dsdirectory_record[$count] = readdir($dsdirectory[$count]))) {

					if (($dsdirectory_record[$count]!="..") && ($dsdirectory_record[$count]!=".")) {

						if (@is_readable("$directory_path/$dsdirectory_record[$count]")) {
							if (is_dir("$directory_path/$dsdirectory_record[$count]")) {

								$file_excludes_match = "0";
								if ($file_excludes_count>0) {
									for ($k=0; $k<$file_excludes_count; $k++) {
										if (isset($file_excludes[$k])) {
											if (myr_wildcard($directory_path.$dsdirectory_record[$count]."/",$file_excludes[$k])) {
												$file_excludes_match = "1";
											}
										}
									}
								}

								if ($file_excludes_match=="0") {

									$count1 = $count + 1;

									$file_size = directory_files_index("$directory_path$dsdirectory_record[$count]/", $count1, $file_excludes);

									$directory_files[$count] = $directory_files[$count] + $file_size;

								}

							} elseif (is_file("$directory_path$dsdirectory_record[$count]")) {

								$directory_files_list[] = $directory_path.$dsdirectory_record[$count];

								$directory_files[$count]++;

							}
						}
					}
				}

				closedir($dsdirectory[$count]);
			}
		}
	}

	return $directory_files[$count];

}


function myr_clean_data() {

	global $api_data_directory;

	$myrdir_name = $api_data_directory;

	if (is_dir($myrdir_name)) {
		if ($myrdir = opendir($myrdir_name)) {

			while (false !== ($myrdir_record = readdir($myrdir))) {

				if (($myrdir_record!="..") && ($myrdir_record!=".")) {
					if (is_file($myrdir_name."/".$myrdir_record)) {

						$myrdir_record_explode = explode(".",$myrdir_record);
						$myrdir_record_explode_name = explode("-",$myrdir_record_explode[0]);

						$myrdir_delete_file = "0";

						if (($myrdir_record_explode[1]=="dat") && (($myrdir_record_explode_name[0]=="CACHE") || ($myrdir_record_explode_name[0]=="INDEX"))) {
							$myrdir_record_explode_name_length = strlen($myrdir_record_explode_name[1]);
							if (isset($myrdir_record_explode_name[2])) {
								$myrdir_record_explode_name_length2 = strlen($myrdir_record_explode_name[2]);
								if (($myrdir_record_explode_name[1]=="INDEX") && ($myrdir_record_explode_name_length2>12)) {
									$myrdir_delete_file = "1";
								} elseif (($myrdir_record_explode_name_length>=28) && (is_numeric($myrdir_record_explode_name[2]))) {
									$myrdir_delete_file = "1";
								}
							} elseif (($myrdir_record_explode_name_length>=28) && (!isset($myrdir_record_explode_name[2]))) {
								$myrdir_delete_file = "1";
							}
						}

						if (($myrdir_record_explode_name[0]=="BACKUP") || ($myrdir_record_explode_name[0]=="SBACKUP") || ($myrdir_record_explode_name[0]=="NSBACKUP")) {
							$myrdir_record_explode_name_length = strlen($myrdir_record_explode_name[1]);
							if ((($myrdir_record_explode_name_length>=28) || ($myrdir_record_explode_name_length>=128)) && (isset($myrdir_record_explode_name[2]))) {
								if (is_numeric($myrdir_record_explode_name[2])) {
									$myrdir_delete_file = "1";
								}
							}
						}

						if ($myrdir_record_explode_name[0]=="RESTORE") {
							$myrdir_record_explode_name_length = strlen($myrdir_record_explode_name[1]);
							if ((($myrdir_record_explode_name_length>=28) || ($myrdir_record_explode_name_length>=128)) && (!isset($myrdir_record_explode_name[2]))) {
								$myrdir_delete_file = "1";
							}
						}

						if ((($myrdir_record_explode[1]=="tmp") || ($myrdir_record_explode[1]=="dat")) && ($myrdir_record_explode_name[0]!="")) {
							$myrdir_record_explode_name_length = strlen($myrdir_record_explode_name[0]);
							if ((($myrdir_record_explode_name_length>=28) || ($myrdir_record_explode_name_length>=128)) && (!isset($myrdir_record_explode_name[1]))) {
								$myrdir_delete_file = "1";
							}
						}

						if (($myrdir_record_explode[1]=="sql") && (($myrdir_record_explode_name[0]=="BACKUP") || ($myrdir_record_explode_name[0]=="SBACKUP") || ($myrdir_record_explode_name[0]=="NSBACKUP"))) {
							$myrdir_record_explode_name_length = strlen($myrdir_record_explode_name[1]);
							if (($myrdir_record_explode_name_length>=14) && (isset($myrdir_record_explode_name[2]))) {
								if (is_numeric($myrdir_record_explode_name[2])) {
									$myrdir_record_explode_name_last = count($myrdir_record_explode_name) - 1;
									if (isset($myrdir_record_explode_name[$myrdir_record_explode_name_last])) {
										$myrdir_record_explode_name_last = $myrdir_record_explode_name[$myrdir_record_explode_name_last];
										if (is_numeric($myrdir_record_explode_name_last)) {
											$myrdir_delete_file = "1";
										}
									}
								}
							}
						}

						if (($myrdir_record_explode_name[0]=="CACHE") && ($myrdir_record_explode_name[1]=="IP") && ($myrdir_record_explode[1]=="tmp")) {
							$myrdir_delete_file = "1";
						}

						if (($myrdir_delete_file=="1") && (filemtime($myrdir_name."/".$myrdir_record)<time()-7200)) {

							unlink($myrdir_name."/".$myrdir_record);

						}
					}
				}
			}
		}
	}

	if (file_exists("error_log")) {
		if (filemtime("error_log")<time()-604800) {
			unlink("error_log");
		}
	}

	if (file_exists("error.log")) {
		if (filemtime("error.log")<time()-604800) {
			unlink("error.log");
		}
	}

	if (file_exists($api_data_directory."/"."error_log")) {
		if (filemtime($api_data_directory."/"."error_log")<time()-604800) {
			unlink($api_data_directory."/"."error_log");
		}
	}

	if (file_exists($api_data_directory."/"."error.log")) {
		if (filemtime($api_data_directory."/"."error.log")<time()-604800) {
			unlink($api_data_directory."/"."error.log");
		}
	}

	if (file_exists($api_data_directory."/"."memory.log")) {
		if (filemtime($api_data_directory."/"."memory.log")<time()-604800) {
			unlink($api_data_directory."/"."memory.log");
		}
	}

	return;

}


function mcrypt_key($p, $s, $c, $kl, $a = 'sha256') {

	if (function_exists('hash_hmac')) {

		$hl = strlen(hash($a, null, true));
		$kb = ceil($kl / $hl);
		$dk = '';
		for ($block=1; $block<=$kb; $block++) {
			$ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
			for ($i=1; $i<$c; $i++) {
				$ib ^= ($b = hash_hmac($a, $b, $p, true));
			}
			$dk .= $ib;
		}
		return substr($dk, 0, $kl);

	} else {

		return false;

	}
}


function encrypt_codec($fp_r, $fp_w, $key) {

	global $api_read_length;

	$keysize = encrypt_strsize($key);
	$k = 0;

	while (!feof($fp_r)) {

		$data = fread($fp_r, $api_read_length);
		$data_strsize = encrypt_strsize($data);
		$result = '';

		for ($i=0, $c=$data_strsize; $i<$c; $i++) {

			$result .= $data[$i] ^ $key[$k];
			$k++;
			if ($k==$keysize) {
				$k = 0;
			}
		}

		fwrite($fp_w, $result);

	}
}


function encrypt_strsize($string) {

	if (extension_loaded('mbstring')) {
		return mb_strlen($string, '8bit');
	} else {
		return strlen($string);
	}
}


function restore_stage_1() {

	global $request_restore, $request_session, $api_data_directory, $api_read_length, $ip_authenticated;

	$output = "";

	if ($ip_authenticated!="1") {
		print "0";
		exit;
	}

	if ($request_restore=="1") {

		$restore_session_random_string = random_string(12);

		$fh = fopen($api_data_directory."/".basename("RESTORE-$request_session.tmp"), 'w');
		fwrite($fh, $restore_session_random_string);
		fclose($fh);

		$output = "1|$restore_session_random_string|";

	} else {

		$output = "0";

	}

	return $output;

}


function restore_stage_2() {

	global $request_restore, $request_session, $request_session_string, $api_mcrypt_rijndael, $api_myrepono_https, $api_data_directory, $api_allow_exec, $api_allow_chmod, $api_force_curl, $api_read_length, $ip_authenticated, $api_timeout, $api_allow_mysqli;

	$output = "";

	if ($ip_authenticated!="1") {
		print "0";
		exit;
	}

	if ($request_restore=="2") {

		if (file_exists($api_data_directory."/".basename("RESTORE-$request_session.tmp"))) {

			$request_session_random_string = file_get_contents($api_data_directory."/".basename("RESTORE-$request_session.tmp"));

			if ($request_session_random_string==$request_session_string) {

				$request_packet = "";
				if (isset($_GET["packet"])) {
					$request_packet = myr_safe_string($_GET["packet"]);
				}
				$request_tmp_string = "";
				if (isset($_GET["tmp_string"])) {
					$request_tmp_string = myr_safe_string($_GET["tmp_string"]);
				}
				$request_empty_db_string = "";
				if (isset($_GET["empty_db_string"])) {
					$request_empty_db_string = myr_safe_string($_GET["empty_db_string"]);
				}
				$request_restore_execute_sql = "";
				if (isset($_GET["restore_execute_sql"])) {
					$request_restore_execute_sql = myr_safe_string($_GET["restore_execute_sql"]);
				}
				$request_restore_execure_sql_string = "";
				if (isset($_GET["restore_execure_sql_string"])) {
					$request_restore_execure_sql_string = myr_safe_string($_GET["restore_execure_sql_string"]);
				}

				if (($request_packet=="") && ($request_tmp_string!="") && ($request_empty_db_string!="")) {

					if ($api_myrepono_https=="1") {
						$request_restore_url = "https://myrepono.com/sys/restore_request/?session=$request_session&string=$request_session_random_string&tmp_string=$request_tmp_string&empty_db_string=".$request_empty_db_string;
					} else {
						$request_restore_url = "http://myrepono.com/sys/restore_request/?session=$request_session&string=$request_session_random_string&tmp_string=$request_tmp_string&empty_db_string=".$request_empty_db_string;
					}

					$api_response = myr_connect($request_restore_url);

					if (($api_response!="") && ($api_response!="0")) {
						$api_response_data = $api_response;
						$api_response = explode("||\n",$api_response);

						if (isset($api_response[0])) {

							$api_response_line0 = explode("|",$api_response[0]);

							if (isset($api_response_line0[0])) {

								if (($api_response_line0[0]=="1") && ($api_response_line0[1]!="") && ($api_response_line0[2]!="") && ($api_response_line0[5]!="")) {

									$api_response_line0[2] = myr_safe_string($api_response_line0[2]);
									$api_response_line0[5] = myr_safe_string($api_response_line0[5]);

									if ($api_allow_mysqli=="1") {
										if (myr_test_extension('mysqli')!==true) {
											$api_allow_mysqli = "0";
										}
									}

									if (($api_allow_mysqli=="1") && (@function_exists('mysqli_query'))) {

										$mysqli = mysqli_init();
										if ($api_timeout!="0") {
											mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, $api_timeout);
										}

										$database_host = explode(':', $api_response_line0[1]);
										$database_port = false;

										if ((isset($database_host[1])) && ($database_host[1]!='') && (is_numeric($database_host[1]))) {
											$database_port = $database_host[1];
										}
										$database_host = $database_host[0];
										$api_response_line0[1] = $database_host;

										$mysqli_connect = false;

										if ($database_port===false) {
											$mysqli_connect = mysqli_real_connect($mysqli, $api_response_line0[1], $api_response_line0[3], $api_response_line0[4], $api_response_line0[2]);
										} else {
											$mysqli_connect = mysqli_real_connect($mysqli, $api_response_line0[1], $api_response_line0[3], $api_response_line0[4], $api_response_line0[2], $database_port);
										}

										if ($mysqli_connect!==false) {

											$mysql_charset = "utf8";

											if (function_exists('mysqli_character_set_name')) {
												if ($mysql_charset = mysqli_character_set_name($mysqli)) {
													} else {
														$mysql_charset = "utf8";
												}
											}

											if (function_exists('mysqli_set_charset')) {
												mysqli_set_charset($mysqli, $mysql_charset);
											}

											$query = "TRUNCATE TABLE `$api_response_line0[5]`;";

											if ($mysqli_query = mysqli_query($mysqli, $query)) {

											} else {

												database_error("0|14");

											}

											mysqli_close($mysqli);

										} else {

											database_error("0|13");

										}

									} else {

										@mysql_connect($api_response_line0[1], $api_response_line0[3], $api_response_line0[4]) or database_error("0|13");
										@mysql_select_db($api_response_line0[2]) or database_error("0|14");

										$mysql_charset = "utf8";
										if (function_exists('mysql_client_encoding')) {
											if ($mysql_charset = @mysql_client_encoding()) {
											} else {
												$mysql_charset = "utf8";
											}
										}
										if (function_exists('mysql_set_charset')) {
											@mysql_set_charset($mysql_charset);
										}

										$query = "TRUNCATE TABLE `$api_response_line0[5]`;";
										$result = @mysql_query($query);

										@mysql_close();

									}

									$output = "1|1";

								} else {

									$output = "0|12";

								}
							}
						}
					}

				} elseif (($request_packet=="") || ($request_tmp_string=="")) {

					$output = "0|6";

				} else {

					if ($api_myrepono_https=="1") {
						$request_restore_url = "https://myrepono.com/sys/restore_request/?session=$request_session&string=$request_session_random_string&tmp_string=$request_tmp_string&packet=$request_packet";
					} else {
						$request_restore_url = "http://myrepono.com/sys/restore_request/?session=$request_session&string=$request_session_random_string&tmp_string=$request_tmp_string&packet=$request_packet";
					}

					if ($api_response = myr_connect_save($request_restore_url, $api_data_directory."/".basename("RESTORE-$request_session.rst"), "||\n")) {

						if (($api_response!="") && ($api_response!="0")) {

							$api_response .= "||\n";
							$api_response_data = $api_response;
							$api_response = explode("||\n",$api_response);

							if (isset($api_response[0])) {

								$api_response[1] = "";

								$api_response_line0 = explode("|",$api_response[0]);

								if (isset($api_response_line0[0])) {

									if ($api_response_line0[0]!="1") {

										$output = "0|7";

									} else {

										if (myr_filesize($api_data_directory."/".basename("RESTORE-$request_session.rst"))==$api_response_line0[3]) {

											$file_decrypted = "0";

											if ($api_response_line0[2]=="1") {

												$file_contents = "";
												$fp_r = fopen($api_data_directory."/".basename("RESTORE-$request_session.rst"), 'rb');
												if ($fp_r) {
													$file_contents = fread($fp_r, 16);
													fclose($fp_r);
												}
												if (substr($file_contents,0,8)=='crypt000') {

													if (decrypt_file($api_data_directory."/".basename("RESTORE-$request_session.rst"),$request_session)) {

														rename($api_data_directory."/".basename("RESTORE-$request_session.rst.tmp"),$api_data_directory."/".basename("RESTORE-$request_session.rst"));
														$file_decrypted = "1";

													} else {

														$output = "0|10";

													}
												} else {

													$file_decrypted = "1";

												}
											} else {

												$file_decrypted = "1";

											}

											if ($file_decrypted=="1") {

												if (!file_exists(dirname($api_response_line0[1]))) {
													if (!mkdir(dirname($api_response_line0[1]),0755,true)) {
														if (!mkdir(dirname($api_response_line0[1]),0777,true)) {
															if (!mkdir(dirname($api_response_line0[1]),0755)) {
																mkdir(dirname($api_response_line0[1]),0777);
															}
														}
													}
												}

												$copy_successful = "1";

												if (!copy($api_data_directory."/".basename("RESTORE-$request_session.rst"), $api_response_line0[1])) {

													$copy_successful = "0";

													$tmp_file_permissions = file_permissions($api_response_line0[1]);
													$tmp_file_permissions = explode("/",$tmp_file_permissions);
													$tmp_file_permissions = str_replace(" ","",$tmp_file_permissions[1]);

													if (!is_numeric($tmp_file_permissions)) {
														$tmp_file_permissions = "0755";
													}

													if (is_numeric($tmp_file_permissions)) {

														if ($api_allow_chmod=="1") {
															@chmod($api_response_line0[1],0777);
														}

														if (copy($api_data_directory."/".basename("RESTORE-$request_session.rst"), $api_response_line0[1])) {

															if ($api_allow_chmod=="1") {
																@chmod($api_response_line0[1],$tmp_file_permissions);
															}
															$output = "1|1";
															$copy_successful = "1";

														} else {

															$output = "0|9";

														}
													} else {

														$output = "0|9";

													}
												}

												if ($copy_successful=="1") {

													if (($request_restore_execute_sql=="1") && ($request_restore_execure_sql_string!="")) {

														$output = "0|16";

														if ($api_myrepono_https=="1") {
															$request_restore_url = "https://myrepono.com/sys/restore_request/?session=$request_session&string=$request_session_random_string&tmp_string=$request_tmp_string&empty_db_string=".$request_restore_execure_sql_string;
														} else {
															$request_restore_url = "http://myrepono.com/sys/restore_request/?session=$request_session&string=$request_session_random_string&tmp_string=$request_tmp_string&empty_db_string=".$request_restore_execure_sql_string;
														}

														$api_response2 = myr_connect($request_restore_url);

														if (($api_response2!="") && ($api_response2!="0")) {
															$api_response2_data = $api_response2;

															$api_response2 = explode("||\n",$api_response2);

															if (isset($api_response2[0])) {

																$api_response2_line0 = explode("|",$api_response2[0]);

																if (isset($api_response2_line0[0])) {

																	if (($api_response2_line0[0]=="1") && ($api_response2_line0[1]!="") && ($api_response2_line0[2]!="") && (file_exists($api_response_line0[1]))) {

																		$api_response2_line0[2] = myr_safe_string($api_response2_line0[2]);
																		$api_response2_line0[5] = myr_safe_string($api_response2_line0[5]);

																		if ($api_allow_mysqli=="1") {
																			if (myr_test_extension('mysqli')!==true) {
																				$api_allow_mysqli = "0";
																			}
																		}

																		if (($api_allow_mysqli=="1") && (@function_exists('mysqli_query'))) {

																			$mysqli = mysqli_init();
																			if ($api_timeout!="0") {
																				mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, $api_timeout);
																			}

																			$database_host = explode(':', $api_response2_line0[1]);
																			$database_port = false;

																			if ((isset($database_host[1])) && ($database_host[1]!='') && (is_numeric($database_host[1]))) {
																				$database_port = $database_host[1];
																			}
																			$database_host = $database_host[0];
																			$api_response2_line0[1] = $database_host;

																			$mysqli_connect = false;

																			if ($database_port===false) {
																				$mysqli_connect = mysqli_real_connect($mysqli, $api_response2_line0[1], $api_response2_line0[3], $api_response2_line0[4], $api_response2_line0[2]);
																			} else {
																				$mysqli_connect = mysqli_real_connect($mysqli, $api_response2_line0[1], $api_response2_line0[3], $api_response2_line0[4], $api_response2_line0[2], $database_port);
																			}

																			if ($mysqli_connect!==false) {

																				$restore_sql_dump = file_get_contents($api_response_line0[1]);

																				$restore_sql_dump = str_replace(";\n\r",";\n",$restore_sql_dump);
																				$restore_sql_dump = str_replace(";\r\n",";\n",$restore_sql_dump);

																				if (function_exists('mysqli_character_set_name')) {
																					if ($mysql_charset = mysqli_character_set_name($mysqli)) {
																					} else {
																						$mysql_charset = "utf8";
																					}
																				}

																				if ($mysql_charset!="utf8") {
																					if (stristr($restore_sql_dump, "CHARSET=utf8")) {
																						$mysql_charset = "utf8";
																					} else {

																						$allowed_table_data_charset = array(
																							'big5' => 1,
																							'dec8' => 1,
																							'cp850' => 1,
																							'hp8' => 1,
																							'koi8r' => 1,
																							'latin1' => 1,
																							'latin2' => 1,
																							'swe7' => 1,
																							'ascii' => 1,
																							'ujis' => 1,
																							'sjis' => 1,
																							'hebrew' => 1,
																							'tis620' => 1,
																							'euckr' => 1,
																							'koi8u' => 1,
																							'gb2312' => 1,
																							'greek' => 1,
																							'cp1250' => 1,
																							'gbk' => 1,
																							'latin5' => 1,
																							'armscii8' => 1,
																							'utf8' => 1,
																							'ucs2' => 1,
																							'cp866' => 1,
																							'keybcs2' => 1,
																							'macce' => 1,
																							'macroman' => 1,
																							'cp852' => 1,
																							'latin7' => 1,
																							'cp1251' => 1,
																							'cp1256' => 1,
																							'cp1257' => 1,
																							'binary' => 1,
																							'geostd8' => 1,
																							'cp932' => 1,
																							'eucjpms' => 1
																						);
																						$allowed_table_data_charset = array_keys($allowed_table_data_charset);
																						$allowed_table_data_charset_count = count($allowed_table_data_charset);

																						for ($i=0; $i<$allowed_table_data_charset_count; $i++) {
																							if ($allowed_table_data_charset[$i]!="") {
																								if (stristr($restore_sql_dump, "CHARSET=".$allowed_table_data_charset[$i])) {
																									$mysql_charset = $allowed_table_data_charset[$i];
																									$i = $allowed_table_data_charset_count + 1;
																								}
																							}
																						}
																					}
																				}

																				if (function_exists('mysqli_set_charset')) {
																					mysqli_set_charset($mysqli, $mysql_charset);
																				}

																				$restore_sql_dump = explode(";\n",$restore_sql_dump);

																				$restore_queries_success = "0";
																				$restore_queries_failed = "0";

																				for ($i=0; $i<count($restore_sql_dump); $i++) {

																					$restore_sql_query = $restore_sql_dump[$i];

																					if ($mysqli_query = mysqli_query($mysqli, $restore_sql_query)) {

																						$restory_query_success = "1";

																					} else {

																						$restory_query_success++;

																					}

																					if ($restory_query_success=="1") {

																						$restore_queries_success++;

																					} else {

																						$restore_queries_failed++;

																					}
																				}

																				if ($restore_queries_failed>0) {

																					database_error("0|15");

																				}

																				unlink($api_response_line0[1]);

																				mysqli_close($mysqli);

																			} else {

																				database_error("0|13");

																			}

																		} else {

																			@mysql_connect($api_response2_line0[1], $api_response2_line0[3], $api_response2_line0[4]) or database_error("0|13");
																			@mysql_select_db($api_response2_line0[2]) or database_error("0|14");

																			$restore_sql_dump = file_get_contents($api_response_line0[1]);

																			$restore_sql_dump = str_replace(";\n\r",";\n",$restore_sql_dump);
																			$restore_sql_dump = str_replace(";\r\n",";\n",$restore_sql_dump);

																			if ($mysql_charset = @mysql_client_encoding()) {
																			} else {
																				$mysql_charset = "utf8";
																			}
																			if ($mysql_charset!="utf8") {
																				if (stristr($restore_sql_dump, "CHARSET=utf8")) {
																					$mysql_charset = "utf8";
																				} else {

																					$allowed_table_data_charset = array(
																						'big5' => 1,
																						'dec8' => 1,
																						'cp850' => 1,
																						'hp8' => 1,
																						'koi8r' => 1,
																						'latin1' => 1,
																						'latin2' => 1,
																						'swe7' => 1,
																						'ascii' => 1,
																						'ujis' => 1,
																						'sjis' => 1,
																						'hebrew' => 1,
																						'tis620' => 1,
																						'euckr' => 1,
																						'koi8u' => 1,
																						'gb2312' => 1,
																						'greek' => 1,
																						'cp1250' => 1,
																						'gbk' => 1,
																						'latin5' => 1,
																						'armscii8' => 1,
																						'utf8' => 1,
																						'ucs2' => 1,
																						'cp866' => 1,
																						'keybcs2' => 1,
																						'macce' => 1,
																						'macroman' => 1,
																						'cp852' => 1,
																						'latin7' => 1,
																						'cp1251' => 1,
																						'cp1256' => 1,
																						'cp1257' => 1,
																						'binary' => 1,
																						'geostd8' => 1,
																						'cp932' => 1,
																						'eucjpms' => 1
																					);
																					$allowed_table_data_charset = array_keys($allowed_table_data_charset);
																					$allowed_table_data_charset_count = count($allowed_table_data_charset);

																					for ($i=0; $i<$allowed_table_data_charset_count; $i++) {
																						if ($allowed_table_data_charset[$i]!="") {
																							if (stristr($restore_sql_dump, "CHARSET=".$allowed_table_data_charset[$i])) {
																								$mysql_charset = $allowed_table_data_charset[$i];
																								$i = $allowed_table_data_charset_count + 1;
																							}
																						}
																					}
																				}
																			}
																			if (function_exists('mysql_set_charset')) {
																				@mysql_set_charset($mysql_charset);
																			}

																			$restore_sql_dump = explode(";\n",$restore_sql_dump);

																			$restore_queries_success = "0";
																			$restore_queries_failed = "0";

																			for ($i=0; $i<count($restore_sql_dump); $i++) {

																				$restory_query_success = "1";

																				$restore_sql_query = $restore_sql_dump[$i];

																				$result = @mysql_query($restore_sql_query) or $restory_query_success++;

																				if ($restory_query_success=="1") {

																					$restore_queries_success++;

																				} else {

																					$restore_queries_failed++;

																				}
																			}

																			if ($restore_queries_failed>0) {

																				database_error("0|15");

																			}

																			unlink($api_response_line0[1]);

																			@mysql_close();

																		}

																		$output = "1|1";

																	} else {

																		$output = "0|15";

																	}
																}
															}
														}
													} else {

														$output = "1|1";
													}
												}
											} else {

													$output = "0|11";
											}
										} else {

											$output = "0|8";
										}

										if (file_exists($api_data_directory."/".basename("RESTORE-$request_session.rst"))) {
											unlink($api_data_directory."/".basename("RESTORE-$request_session.rst"));
										}
										if (file_exists($api_data_directory."/".basename("RESTORE-$request_session.rst.tmp"))) {
											unlink($api_data_directory."/".basename("RESTORE-$request_session.rst.tmp"));
										}
									}
								} else {

									$output = "0|7";

								}
							} else {

								$output = "0|7";

							}
						} else {

							$output = "0|7";

						}
					} else {

						if (file_exists($api_data_directory."/".basename("RESTORE-$request_session.rst"))) {
							unlink($api_data_directory."/".basename("RESTORE-$request_session.rst"));
						}

						$output = "0|7";

					}
				}
			} else {

				$output = "0|6";

			}
		} else {

			$output = "0|6";

		}
	} else {

		$output = "0";

	}

	return $output;

}


function restore_stage_3() {

	global $request_restore, $request_session, $request_session_string, $api_data_directory, $api_allow_chmod, $api_allow_exec, $api_force_curl, $api_read_length, $ip_authenticated;

	$output = "";

	if ($ip_authenticated!="1") {
		print "0";
		exit;
	}

	if ($request_restore=="3") {

		if (file_exists($api_data_directory."/".basename("RESTORE-$request_session.tmp"))) {

			$request_session_random_string = file_get_contents($api_data_directory."/".basename("RESTORE-$request_session.tmp"));

			if ($request_session_random_string==$request_session_string) {

				unlink($api_data_directory."/".basename("RESTORE-$request_session.tmp"));

				$output = "1|1";

			} else {

				$output = "0|6";

			}
		} else {

			$output = "0|6";

		}
	} else {

		$output = "0";

	}

	return $output;

}


function decrypt_file($file, $key) {

	global $api_read_length;

	if (file_exists($file)) {

		$fp_r = fopen($file, 'rb');
		if ($fp_r) {
			$file_contents = fread($fp_r, 16);
			if (substr($file_contents,0,8)!='crypt000') {

				return 0;

			}
			fclose($fp_r);
		} else {
			return 0;
		}

		$decrypt_file = $file.".tmp";

		if (substr($file_contents,8,1)=="1") {

			$file_contents = "";

			$fp_r = fopen($file, 'rb');
			fseek($fp_r, 9);
			$fp_w = fopen($decrypt_file, 'wb');
			encrypt_codec($fp_r, $fp_w, $key);
			fclose($fp_r);
			fclose($fp_w);

		} elseif (substr($file_contents,8,1)=="2") {

			$crypt_password = "RK9R7QJ2D5FXSVPV50ZB6WCRDWNBXGAC";
			$crypt_salt = "D0Ge59cWQVP6SsYIX1fsF54621cS";

			$myrepono_mcrypt = new myrepono_mcrypt;
			$crypt_key = $myrepono_mcrypt->pbkdf2($crypt_password, $crypt_salt, 1000, 32);

			$file_contents = file_get_contents($file);
			$file_contents_decrypted = $myrepono_mcrypt->decrypt(substr($file_contents,9,(strlen($file_contents) - 9)), $crypt_key);

			$file_contents = "";

			$fp_w = fopen($decrypt_file, 'wb');
			fwrite($fp_w, $file_contents_decrypted);
			fclose($fp_w);

			$file_contents_decrypted = "";

		} elseif (substr($file_contents,8,1)=="3") {

			$crypt_password = $key;
			$crypt_salt = "D0Ge59cWQVP6SsYIX1fsF54621cS";

			if ($mcrypt_key = mcrypt_key($crypt_password, $crypt_salt, 1000, 32)) {

				if ($mcrypt_cipher = mcrypt_module_open('rijndael-256', '', 'cbc', '')) {

					$mcrypt_iv = md5($crypt_salt);

					mcrypt_generic_init($mcrypt_cipher, $mcrypt_key, $mcrypt_iv);

					if ($fp_r = fopen($file, 'rb')) {

						fseek($fp_r, 9);

						if ($fp_w = fopen($decrypt_file, 'wb')) {

							while (!feof($fp_r)) {

								$buff = fread($fp_r, $api_read_length);
								if ($buff!="") {
									$output = mdecrypt_generic($mcrypt_cipher, $buff);
									fwrite($fp_w, $output);
								}
								$output = "";

							}

							fclose($fp_w);

						} else {
							return 0;
						}

						fclose($fp_r);

					} else {
						return 0;
					}
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		}

		return 1;

	} else {

		return 0;

	}

	return 1;

}


function file_tree_array($files_array = array()) {

	global $api_filetree_base_identifier, $api_filetree_files_identifier;

	$tree_array = array();

	for ($i=0; $i<count($files_array); $i++) {

		$files_array_path = str_replace('//','/', str_replace('\\','/', $files_array[$i]));
		$files_array_path_explode = explode("/", dirname($files_array_path));

		if (!isset($files_array_path_explode[0])) {
			$files_array_path_explode[0] = $api_filetree_base_identifier;
		} else {
			if ($files_array_path_explode[0]=="") {
				$files_array_path_explode[0] = $api_filetree_base_identifier;
			} else {
				$tmp_array = array();
				$tmp_array[] = $api_filetree_base_identifier;
				$files_array_path_explode = array_merge($tmp_array,$files_array_path_explode);
			}
		}

		$files_array_path_filename = basename($files_array[$i]);

		if ($files_array_path_filename!="") {

			$files_array_path_implode = implode("/",$files_array_path_explode);
			$files_array_path_implode .= "/".$api_filetree_files_identifier."/";

			$tree_array = array_position_push($tree_array, $files_array_path_filename, $files_array_path_implode);

		}
	}

	return $tree_array;

}


function myr_wildcard($string = "", $search = "*") {

    $search_i = "0";
    $string_i = "0";
    $search_len = strlen($search);
    $string_len = strlen($string);

    while (($string_i<$string_len) && ($search[$search_i]!='*')) {
        if (($search[$search_i]!=$string[$string_i]) && ($search[$search_i]!='?')) {
            return "0";
        }
        $search_i++;
        $string_i++;
    }

    $mcount = 0;
    $ccount = 0;

    while ($string_i<$string_len) {
        if ($search[$search_i]=='*') {
            if (++$search_i==$search_len) {
                return "1";
            }
            $mcount = $search_i;
            $ccount = $string_i + 1;
        } elseif (($search[$search_i]==$string[$string_i]) || ($search[$search_i]=='?')) {
            $search_i++;
            $string_i++;
        } else {
            $search_i = $mcount;
            $string_i = $ccount++;
        }
    }

    while ((isset($search[$search_i])) && ($search[$search_i]=='*')) {
        $search_i++;
    }

	if ($search_i==$search_len) {
		return "1";
	} else {
		return "0";
	}

}


function array_position_push($array = array(), $value = "", $location = "", $increment = "1", $count = "0") {

	$location_explode[$count] = explode("/",$location);
	$location_count[$count] = count($location_explode[$count]);

	$array_position_0 = "";
	$array_position_1 = "";

	$tmp_array[$count] = $array;

	if (isset($location_explode[$count][0])) {
		$array_position_0 = $location_explode[$count][0];
	}
	if (isset($location_explode[$count][1])) {
		$array_position_1 = $location_explode[$count][1];
	}

	if (!is_array($tmp_array[$count])) {
		$tmp_array[$count] = array();
	}

	if ($location_count[$count]=="1") {

		if ($array_position_0=="") {
			$tmp_array[$count][] = $value;
		} else {
			$tmp_array[$count][$array_position_0] = $value;
		}

		if (is_array($tmp_array[$count])) {
			ksort($tmp_array[$count]);
		}

	} elseif ($location_count[$count]>1) {

		$new_location_explode = $location_explode[$count];
		unset($new_location_explode[0]);
		$new_location = implode("/", $new_location_explode);

		$tmp_array_position_0[$count] = $array_position_0;

		if (!isset($tmp_array[$count][$array_position_0])) {
			$tmp_array[$count][$array_position_0] = "";
		}

		$tmp_value = array_position_push($tmp_array[$count][$array_position_0], $value, $new_location, $increment, ($count + 1));

		$array_position_0 = $tmp_array_position_0[$count];

		if ($array_position_0=="") {
			$tmp_array[$count][] = $tmp_value;
		} else {
			$tmp_array[$count][$array_position_0] = $tmp_value;
		}

		if (is_array($tmp_array[$count])) {
			ksort($tmp_array[$count]);
		}
	}

	return $tmp_array[$count];
}


function myr_safe_string($string = "", $strip_tags = "1") {

	if ($strip_tags=="1") {
		$string = strip_tags($string);
	}

	$search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
	$replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
	$string = str_replace($search, $replace, $string);

	return $string;

}


function myr_connect($request_url = "") {

	global $api_libcurlemu, $api_force_curl;

	if (($api_force_curl!="1") && ($request_response = @file_get_contents($request_url))) {

		return $request_response;

	} else {

		if ($api_libcurlemu!="1") {
			if (file_exists(dirname(__FILE__)."/libcurlemu/libcurlemu.inc.php")) {
				require_once(dirname(__FILE__)."/libcurlemu/libcurlemu.inc.php");
			}
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$request_response = curl_exec($ch);

		curl_close($ch);

		return $request_response;

	}
}


function myr_connect_save($request_url = "", $result_file = "", $result_delimiter = false) {

	global $api_libcurlemu, $api_force_curl;

	if ($api_force_curl!="1") {

		$fr = fopen($request_url, "r");
		if ($fr) {

			$fw = fopen($result_file, 'wb');
			if ($fw) {

				if ($result_delimiter) {

					$delimiter_matched = "0";
					$data = "";

					while (!feof($fr)) {

						if ($delimiter_matched=="0") {

							$data .= fread($fr, 32768);

							if (stristr($data, $result_delimiter)) {
								$data_explode = explode($result_delimiter, $data);
								if (isset($data_explode[1])) {
									$delimiter_matched = "1";
									$delimiter_start = strlen($data_explode[0]);
									$delimiter_end = $delimiter_start + strlen($result_delimiter);
									fwrite($fw, substr($data, $delimiter_end));
									$data = substr($data, 0, $delimiter_start);
								}
								$data_explode = "";
							}

						} else {

							fwrite($fw, fread($fr, 32768));

						}
					}

					fclose($fw);
					fclose($fr);

					return $data;

				} else {

					while (!feof($fr)) {

						fwrite($fw, fread($fr, 32768));

					}

					fclose($fw);
					fclose($fr);

					return true;

				}
			}

			fclose($fr);
		}

	} else {

		if ($api_libcurlemu!="1") {
			if (file_exists(dirname(__FILE__)."/libcurlemu/libcurlemu.inc.php")) {
				require_once(dirname(__FILE__)."/libcurlemu/libcurlemu.inc.php");
			}
		}

		$fw = fopen($result_file, 'wb');
		if ($fw) {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $request_url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FILE, $fw);

			$request_response = curl_exec($ch);

			curl_close($ch);
			fclose($fw);

			if ($result_delimiter) {

				$fr = fopen($result_file, "r");
				if ($fr) {

					$fw = fopen($result_file.".tmp", 'wb');
					if ($fw) {

						$delimiter_matched = "0";
						$data = "";

						while (!feof($fr)) {

							if ($delimiter_matched=="0") {

								$data .= fread($fr, 32768);
								if (stristr($data, $result_delimiter)) {
									$data_explode = explode($result_delimiter, $data);
									if (isset($data_explode[1])) {
										$delimiter_matched = "1";
										$delimiter_start = strlen($data_explode[0]);
										$delimiter_end = $delimiter_start + strlen($result_delimiter);
										fwrite($fw, substr($data, $delimiter_end));
										$data = substr($data, 0, $delimiter_start);
									}
									$data_explode = "";
								}

							} else {

								fwrite($fw, fread($fr, 32768));

							}
						}

						fclose($fw);
						fclose($fr);

						unlink($result_file);
						rename($result_file.".tmp", $result_file);

						return $data;

					}

					fclose($fr);
				}
			} else {

				return true;

			}
		}
	}

	return false;

}


function myr_serialize_save($filename = "", $array = array()) {

	if ((!is_array($array)) || ($filename=="")) {
		return false;
	}
	$fh = fopen($filename, 'w');
	if ($fh) {
		if (!myr_serialize_data($fh, $array)) {
			return false;
		}
		fclose($fh);
		return true;
	}
	return false;

}


function myr_serialize_data($fh, $array = array()) {

	if (!$fh) {
		return false;
	}
	$n = count($array);
	fwrite($fh, "a:".$n.":{");
	$i = 1;
	foreach ($array as $key => $value) {
		fwrite($fh, myr_serialize_value($key).";");
		if (is_array($value)) {
			if (!myr_serialize_data($fh, $value)) {
				return false;
			}
		} else {
			fwrite($fh, myr_serialize_value($value).";");
		}
	}
	fwrite($fh, "}");
	return true;

}


function myr_serialize_value($value) {

	if (is_null($value)) {
		return "N";
	} elseif (is_bool($value)) {
		return $value ? "b:0":"b:1";
	} elseif (is_integer($value)) {
		return "i:".$value;
	} else {
		return "s:".strlen($value).":\"".$value."\"";
	}

}


function myr_serialize_open($filename = "") {

	if (file_exists($filename)) {
		$data = file_get_contents($filename);
		if ($data!="") {
			$value = unserialize($data);
			if (is_array($value)) {
				return $value;
			}
		}
	}

	return array();

}


function myr_filesize($filesize_path) {

	global $api_allow_exec, $api_allow_chmod;

	$myr_filesize = "0";

	if (file_exists($filesize_path)) {

		$myr_filesize = @filesize($filesize_path);
		$myr_filesize_basic = $myr_filesize;

		if (($myr_filesize=="") || ($myr_filesize==false)) {

			$myr_filesize = sprintf("%u", @filesize($filesize_path));

			if (($myr_filesize=="") || ($myr_filesize=="0") || ($myr_filesize==false)) {

				if ($api_allow_exec=="1") {

					$myr_filesize = exec('stat -c %s '.@escapeshellarg($filesize_path));

				} else {

					if ($myr_filesize_basic < 0) {
						$myr_filesize_basic += 2.0 * (PHP_INT_MAX + 1);
					}

					$i = 0;

					$filesize_file = @fopen($filesize_path, "r");

					if (!$filesize_file) {

						$myr_filesize = "2147483649";

					} else {

						while (strlen(fread($filesize_file, 1)) === 1) {
							fseek($myfile, PHP_INT_MAX, SEEK_CUR);
							$i++;
						}

						fclose($filesize_file);

						if ($i % 2 == 1) {
							$i--;
						}

						$myr_filesize = ((float)($i) * (PHP_INT_MAX + 1)) + $myr_filesize_basic;

					}
				}
			}
		}
	}

	return $myr_filesize;

}


function myr_debug_api() {

	global $api_allow_exec, $api_csplit, $api_csplit_path, $api_split, $api_split_path, $api_mysqldump, $api_mysqldump_path, $api_read_length, $api_data_directory;

	$debug_string = random_string(12);

	$debug_output = time().":$debug_string:";

	if (file_exists($api_data_directory)) {
		$debug_output .= "1:";
	} else {
		$debug_output .= "0:";
	}
	if (is_writable($api_data_directory)) {
		$debug_output .= "1:";
	} else {
		$debug_output .= "0:";
	}

	$debug_file_create = "0";

	$fh = fopen($api_data_directory."/debug.txt", 'wb');

	if ($fh) {

		if (fwrite($fh, "DEBUG-".$debug_string)) {

			fclose($fh);

			if (file_exists($api_data_directory."/debug.txt")) {

				if (file_get_contents($api_data_directory."/debug.txt")=="DEBUG-".$debug_string) {

					$debug_file_create = "1";

				} else {

					$debug_file_create = "0/4";

				}
			} else {

				$debug_file_create = "0/3";

			}
		} else {

			$debug_file_create = "0/2";

		}
	} else {

		$debug_file_create = "0/1";

	}

	$debug_output .= $debug_file_create.":";

	$debug_file_remove = "0";

	if ($debug_file_create=="1") {

		if (file_exists($api_data_directory."/debug.txt")) {

			unlink($api_data_directory."/debug.txt");

			if (file_exists($api_data_directory."/debug.txt")) {

				$debug_file_remove = "0/2";

			} else {

				$debug_file_remove = "1";

			}
		} else {

			$debug_file_remove = "0/1";

		}

	}

	$debug_output .= $debug_file_remove.":";

	$debug_output .= myr_test_exec("split",1).":";

	$debug_output .= myr_test_exec("csplit",1).":";

	$debug_output .= myr_test_exec("mysqldump",1).":";

	$debug_timeout = "0";

	if (!ini_get('safe_mode')) {
		if (function_exists('set_time_limit')) {
			$debug_timeout = "1";
		}
	}

	$debug_output .= $debug_timeout.":";

	$debug_output .= "END";

	$php_config = myr_php_config();

	$php_config_serialize = serialize($php_config);
	$php_config_serialize = str_replace("\n","",$php_config_serialize);
	$php_config_serialize = str_replace("\r","",$php_config_serialize);
	$php_config_serialize = str_replace("|","::",$php_config_serialize);

	$debug_output .= "\nSYS|$php_config_serialize|END";

	return $debug_output;

}


function myr_php_config() {

	$phpini_config = array(
		'ver' => array(),
		'ini' => array(),
		'ext' => array(),
		'sys' => array(),
		'mem' => array()
	);

	if (function_exists('phpversion')) {
		$phpini_config['ver']['number'] = phpversion();
		$phpini_config_version = explode("-",$phpini_config['ver']['number']);
		if (!isset($phpini_config_version[1])) {
			$phpini_config_version[1] = "";
		}
		$phpini_config_version_tail = $phpini_config_version[1];
			$phpini_config_version = explode(".",$phpini_config_version[0]);
		if (!isset($phpini_config_version[0])) {
			$phpini_config_version[0] = "";
		}
		if (!isset($phpini_config_version[1])) {
			$phpini_config_version[1] = "";
		}
		if (!isset($phpini_config_version[2])) {
			$phpini_config_version[2] = "";
		}
		$phpini_config['ver']['major'] = $phpini_config_version[0];
		$phpini_config['ver']['minor'] = $phpini_config_version[1];
		$phpini_config['ver']['revision'] = $phpini_config_version[2];
		$phpini_config['ver']['tail'] = $phpini_config_version_tail;
	}

	if (function_exists('ini_get_all')) {

		$phpini_array = ini_get_all();

		if (isset($phpini_array)) {

			$find_ini = array("allow_url_fopen", "date.timezone", "default_socket_timeout", "disable_classes", "disable_functions", "display_errors", "display_startup_errors", "error_log", "error_reporting", "expose_php", "log_errors", "max_execution_time", "max_input_time", "memory_limit", "mysql.connect_timeout", "open_basedir", "safe_mode", "safe_mode_exec_dir");

			for ($i=0; $i<count($find_ini); $i++) {
				if (isset($find_ini[$i])) {
					$find_ini_name = $find_ini[$i];
					if (isset($phpini_array[$find_ini_name])) {

						$phpini_config['ini'][$find_ini_name] = $phpini_array[$find_ini_name];

					}
				}
			}

			ksort($phpini_config['ini']);

		}
	}

	if (function_exists('get_loaded_extensions')) {

		$phpextension_array = get_loaded_extensions();

		if (isset($phpextension_array)) {

			$find_extension = array("curl", "mbstring", "mcrypt", "mongo", "mysql", "mysqli", "openssl", "zip", "zlib");

			for ($i=0; $i<count($phpextension_array); $i++) {

				if (isset($phpextension_array[$i])) {

					$phpextension_name = $phpextension_array[$i];

					for ($j=0; $j<count($find_extension); $j++) {

						if (isset($find_extension[$j])) {

							$find_extension_name = $find_extension[$j];

							if ($find_extension_name==$phpextension_name) {

								$phpini_config['ext'][$find_extension_name]['installed'] = "1";
								$phpini_config['ext'][$find_extension_name]['version'] = phpversion($find_extension_name);

							} elseif (!isset($phpini_config['ext'][$find_extension_name])) {

								$phpini_config['ext'][$find_extension_name]['installed'] = "0";
								$phpini_config['ext'][$find_extension_name]['version'] = "";

							}
						}
					}
				}
			}

			ksort($phpini_config['ext']);

		}
	}

	if (function_exists('php_uname')) {

		$phpini_config['sys']['os'] = php_uname('s');
		$phpini_config['sys']['host'] = php_uname('n');
		$phpini_config['sys']['release'] = php_uname('r');
		$phpini_config['sys']['version'] = php_uname('v');
		$phpini_config['sys']['machine'] = php_uname('m');

	}

	if (function_exists('memory_get_peak_usage')) {

		$phpini_config['mem']['peak'] = memory_get_peak_usage();
		$phpini_config['mem']['peakt'] = memory_get_peak_usage(true);

	}

	if (function_exists('getrusage')) {

		$phpini_config['mem']['rusage'] = getrusage();

	}

	return $phpini_config;

}


function myr_memory_peak_usage() {

	if (function_exists('memory_get_peak_usage')) {
		return memory_get_peak_usage();
	} elseif (function_exists('memory_get_usage')) {
		return memory_get_usage();
	} else {
		return "0";
	}

}


function myr_memory_peak_usage_monitor() {

	global $api_data_directory;

	$memory_peak = "0";

	if (file_exists($api_data_directory."/memory.log")) {
		$memory_peak = file_get_contents($api_data_directory."/memory.log");
		$memory_peak = explode("|",$memory_peak);
		$memory_peak = $memory_peak[0];
		if (!is_numeric($memory_peak)) {
			$memory_peak = "0";
		}
	}

	$memory_peak_now = myr_memory_peak_usage();

	if ((is_numeric($memory_peak_now)) && ($memory_peak_now>$memory_peak)) {
		$fh = fopen($api_data_directory."/memory.log", 'w');
		fwrite($fh, $memory_peak_now."|".time());
		fclose($fh);
	}
}


function myr_test_exec($test_exec = "", $return_value = "0") {

	global $api_allow_exec, $api_csplit, $api_csplit_path, $api_split, $api_split_path, $api_mysqldump, $api_mysqldump_path, $api_read_length;

	if ($test_exec=="csplit") {

		$tmp_api_csplit = "0";

		if ($api_allow_exec=="1") {

			$system_csplit = "";

			exec($api_csplit_path." --help", $system_csplit);

			if ($system_csplit!="") {
				$system_csplit = implode("\n",$system_csplit);
				if ((stristr($system_csplit,"csplit [OPTION]... FILE PATTERN...")) || (stristr($system_csplit,"Output pieces of FILE separated by PATTERN"))) {
					$tmp_api_csplit = "1";
					$tmp_exec_string = @escapeshellarg("ABC123");
					if (($tmp_exec_string=="") || ($tmp_exec_string==false)) {
						$tmp_api_csplit = "0";
					}
				}
			}
		}

		if ($return_value=="1") {

			return $tmp_api_csplit;

		} else {

			$api_csplit = $tmp_api_csplit;

		}

	} elseif ($test_exec=="split") {

		$tmp_api_split = "0";

		if ($api_allow_exec=="1") {

			$system_split = "";

			exec($api_split_path." --help", $system_split);

			if ($system_split!="") {
				$system_split = implode("\n",$system_split);
				if ((stristr($system_split,"Usage: split [OPTION] [INPUT [PREFIX]]")) || (stristr($system_split,"Mandatory arguments to long options are mandatory for short options too."))) {
					$tmp_api_split = "1";
					$tmp_exec_string = @escapeshellarg("ABC123");
					if (($tmp_exec_string=="") || ($tmp_exec_string==false)) {
						$tmp_api_split = "0";
					}
				}
			}
		}

		if ($return_value=="1") {

			return $tmp_api_split;

		} else {

			$api_split = $tmp_api_split;

		}

	} elseif ($test_exec=="mysqldump") {

		$tmp_api_mysqldump = "0";

		if ($api_allow_exec=="1") {

			$system_mysqldump = "";

			exec($api_mysqldump_path." --help", $system_mysqldump);

			if ($system_mysqldump!="") {
				$system_mysqldump = implode("\n",$system_mysqldump);
				if ((stristr($system_mysqldump,"Dumping definition and data mysql database or table")) || (stristr($system_mysqldump,"The following options may be given as the first argument:"))) {
					$tmp_api_mysqldump = "1";
					$tmp_exec_string = @escapeshellarg("ABC123");
					if (($tmp_exec_string=="") || ($tmp_exec_string==false)) {
						$tmp_api_mysqldump = "0";
					}
				}
			}
		}

		if ($return_value=="1") {

			return $tmp_api_mysqldump;

		} else {

			$api_mysqldump = $tmp_api_mysqldump;

		}

	}

	return;

}


function myr_test_extension($find_extension_name = "") {

	if (function_exists('get_loaded_extensions')) {

		$phpextension_array = get_loaded_extensions();

		if (isset($phpextension_array)) {

			for ($i=0; $i<count($phpextension_array); $i++) {

				if (isset($phpextension_array[$i])) {

					$phpextension_name = $phpextension_array[$i];

					if ($find_extension_name==$phpextension_name) {

						return true;

					}
				}
			}
		}
	}

	return false;

}


function myr_api_upgrade() {

	global $client_key, $client_password, $api_myrepono_https, $api_libcurlemu, $api_force_curl, $api_data_directory, $api_allow_chmod, $api_version;

	$upgrade_status = "ERROR";

	$upgrade_request_string = "";
	if (isset($_GET["string"])) {
		$upgrade_request_string = myr_safe_string($_GET["string"]);
	}

	if ($upgrade_request_string=="") {
		$upgrade_status = "ERROR-STRING";
	} else {

		if ($api_myrepono_https=="1") {
			$api = myr_connect("https://myrepono.com/sys/api_upgrade/?string=$upgrade_request_string&version=".$api_version);
		} else {
			$api = myr_connect("http://myrepono.com/sys/api_upgrade/?string=$upgrade_request_string&version=".$api_version);
		}

		if ($api=="") {
			$upgrade_status = "ERROR-CONNECT";
		} elseif ($api=="0") {
			$upgrade_status = "ERROR-AUTH";
		} else {

			if ((stristr($api, "MYREPONO"."API"."KEY")) && (stristr($api, "MYREPONO"."API"."PASSWORD"))) {

				$api = str_replace('"MYREPONO'.'API'.'KEY"', '"'.$client_key.'"', $api);
				$api = str_replace('"MYREPONO'.'API'.'PASSWORD"', '"'.$client_password.'"', $api);

			}

			if ((stristr($api, $client_key)) && (stristr($api, $client_password))) {

				$api_path = str_replace('//','/', str_replace('\\','/',__FILE__));

				copy($api_path, $api_data_directory."/UPGRADE-BACKUP-".date("YmdHis")."-".basename($api_path));

				$fh = fopen($api_path, 'wb');

				$api_chmod = "0";
				if ($api_allow_chmod=="1") {
					if (!$fh) {
						@chmod($api_path,0777);
						$api_chmod = "1";
						$fh = fopen($api_path, 'wb');
						if (!$fh) {
							@chmod($api_path,0755);
						}
					}
				}

				if ($fh) {

					fwrite($fh, $api);
					fclose($fh);

					if ($api_chmod=="1") {
						@chmod($api_path,0755);
					}

					$upgrade_status = "SUCCESS";

				} else {

					$upgrade_status = "ERROR-FILE";

				}
			} else {

				$upgrade_status = "ERROR-REPLACE";

			}
		}
	}

	$upgrade_status = "UPGRADE:".$upgrade_status."\n";

	return $upgrade_status;

}


function myr_timeout() {

	global $api_timeout;

	if ($api_timeout!="0") {
		if ($api_timeout>180) {
			$api_timeout = "180";
		}
		$api_timeout = $api_timeout * 60;
		if (!ini_get('safe_mode')) {
			if (function_exists('set_time_limit')) {
				set_time_limit($api_timeout);
			}
		}
		if (function_exists('ini_set')) {
			@ini_set('mysql.connect_timeout', $api_timeout);
			@ini_set('default_socket_timeout', $api_timeout);
		}
	}

	return;

}


function myr_memory() {

	global $api_memory;

	if (function_exists('ini_set')) {
		if (($api_memory!="0") && ($api_memory!="")) {
			if (is_numeric($api_memory)) {
				$api_memory = round($api_memory);
				if ($api_memory>=32) {
					$php_memory = ini_get('memory_limit');
					if (stristr($php_memory,"K")) {
						$php_memory = str_replace("K","",$php_memory);
						if (is_numeric($php_memory)) {
							$php_memory = $php_memory / 1024;
							$php_memory = round($php_memory);
						} else {
							$php_memory = "";
						}
					} elseif (stristr($php_memory,"M")) {
						$php_memory = str_replace("M","",$php_memory);
						if (is_numeric($php_memory)) {
							$php_memory = round($php_memory);
						} else {
							$php_memory = "";
						}
					} elseif (stristr($php_memory,"G")) {
						$php_memory = str_replace("G","",$php_memory);
						if (is_numeric($php_memory)) {
							$php_memory = $php_memory * 1024;
							$php_memory = round($php_memory);
						} else {
							$php_memory = "";
						}
					} elseif (is_numeric($php_memory)) {
						$php_memory = ($php_memory / 1024) / 1024;
						$php_memory = round($php_memory);
					} else {
						$php_memory = "";
					}
					if ($php_memory=="") {
						$api_memory .= "M";
						ini_set('memory_limit', $api_memory);
					} elseif ($php_memory<$api_memory) {
						$api_memory .= "M";
						ini_set('memory_limit', $api_memory);
					}
				}
			}
		}
	}

	return;

}


class myrepono_mcrypt {

	function encrypt($msg, $k, $base64 = false) {

		if ((function_exists('mcrypt_create_iv')) && (function_exists('mcrypt_generic'))) {

			if (!$td = mcrypt_module_open('rijndael-256', '', 'ctr', '')) {
				return false;
			}

			$msg = serialize($msg);
			$iv  = mcrypt_create_iv(32, MCRYPT_RAND);

			if (mcrypt_generic_init($td, $k, $iv)!==0) {
				return false;
			}

			$msg  = mcrypt_generic($td, $msg);
			$msg  = $iv . $msg;
			$mac  = $this->pbkdf2($msg, $k, 1000, 32);
			$msg .= $mac;

			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);

			if ($base64) {
				$msg = base64_encode($msg);
			}

			return $msg;

		}
	}

	function decrypt($msg, $k, $base64 = false) {

		if ((function_exists('mcrypt_module_open')) && (function_exists('mdecrypt_generic'))) {

			if ($base64) {
				$msg = base64_decode($msg);
			}

			if (!$td = mcrypt_module_open('rijndael-256', '', 'ctr', '')) {
				return false;
			}

			$iv  = substr($msg, 0, 32);
			$mo  = strlen($msg) - 32;
			$em  = substr($msg, $mo);
			$msg = substr($msg, 32, strlen($msg)-64);
			$mac = $this->pbkdf2($iv . $msg, $k, 1000, 32);

			if ($em!==$mac) {
				return false;
			}

			if (mcrypt_generic_init($td, $k, $iv)!==0) {
				return false;
			}

			$msg = mdecrypt_generic($td, $msg);
			$msg = unserialize($msg);

			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);

			return $msg;

		}
	}

	function pbkdf2( $p, $s, $c, $kl, $a = 'sha256' ) {

		if (function_exists('hash_hmac')) {

			$hl = strlen(hash($a, null, true));
			$kb = ceil($kl / $hl);
			$dk = '';

			for ($block=1; $block<=$kb; $block++) {

				$ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);

				for ($i=1; $i<$c; $i++) {

					$ib ^= ($b = hash_hmac($a, $b, $p, true));

				}

				$dk .= $ib;
			}

			return substr($dk, 0, $kl);

		}
	}

}


class dZip{

	# Author: Alexandre Tedeschi (d)

	var $filename;
	var $overwrite;

	var $zipSignature = "\x50\x4b\x03\x04";
	var $dirSignature = "\x50\x4b\x01\x02";
	var $dirSignatureE= "\x50\x4b\x05\x06";
	var $files_count  = 0;
	var $fh;

	Function __construct($filename, $overwrite=true){
		$this->filename  = $filename;
		$this->overwrite = $overwrite;
	}
	Function addDir($dirname, $fileComments=''){
		if(substr($dirname, -1) != '/')
			$dirname .= '/';
		$this->addFile(false, $dirname, $fileComments);
	}
	Function addFile($filename, $cfilename, $fileComments='', $data=false){
		if(!($fh = &$this->fh))
			$fh = fopen($this->filename, $this->overwrite?'wb':'a+b');

		if(substr($cfilename, -1)=='/'){
			$details['uncsize'] = 0;
			$data = '';
		} elseif(file_exists($filename)){
			$details['uncsize'] = filesize($filename);
			$data = file_get_contents($filename);
		} elseif($filename){
			return false;
		} else{
			$details['uncsize'] = strlen($filename);
		}

		if($details['uncsize'] < 256){
			$details['comsize'] = $details['uncsize'];
			$details['vneeded'] = 10;
		$details['cmethod'] = 0;
			$zdata = &$data;
		} else{
			$zdata = gzcompress($data);
			$zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
			$details['comsize'] = strlen($zdata);
			$details['vneeded'] = 10;
			$details['cmethod'] = 8;
		}

		$details['bitflag'] = 0;
		$details['crc_32']  = crc32($data);

		$lastmod_timeS  = str_pad(decbin(date('s')>=32?date('s')-32:date('s')), 5, '0', STR_PAD_LEFT);
		$lastmod_timeM  = str_pad(decbin(date('i')), 6, '0', STR_PAD_LEFT);
		$lastmod_timeH  = str_pad(decbin(date('H')), 5, '0', STR_PAD_LEFT);
		$lastmod_dateD  = str_pad(decbin(date('d')), 5, '0', STR_PAD_LEFT);
		$lastmod_dateM  = str_pad(decbin(date('m')), 4, '0', STR_PAD_LEFT);
		$lastmod_dateY  = str_pad(decbin(date('Y')-1980), 7, '0', STR_PAD_LEFT);

		$details['modtime'] = bindec("$lastmod_timeH$lastmod_timeM$lastmod_timeS");
		$details['moddate'] = bindec("$lastmod_dateY$lastmod_dateM$lastmod_dateD");

		$details['offset'] = ftell($fh);
		fwrite($fh, $this->zipSignature);
		fwrite($fh, pack('s', $details['vneeded']));
		fwrite($fh, pack('s', $details['bitflag']));
		fwrite($fh, pack('s', $details['cmethod']));
		fwrite($fh, pack('s', $details['modtime']));
		fwrite($fh, pack('s', $details['moddate']));
		fwrite($fh, pack('V', $details['crc_32']));
		fwrite($fh, pack('I', $details['comsize']));
		fwrite($fh, pack('I', $details['uncsize']));
		fwrite($fh, pack('s', strlen($cfilename)));
		fwrite($fh, pack('s', 0));
		fwrite($fh, $cfilename);
		fwrite($fh, $zdata);

		$details['external_attributes']  = (substr($cfilename, -1)=='/'&&!$zdata)?16:32;
		$details['comments']             = $fileComments;
		$this->appendCentralDir($cfilename, $details);
		$this->files_count++;
	}
	Function setExtra($filename, $property, $value){
		$this->centraldirs[$filename][$property] = $value;
	}
	Function save($zipComments=''){
		if(!($fh = &$this->fh))
			$fh = fopen($this->filename, $this->overwrite?'w':'a+');

		$cdrec = "";
		foreach($this->centraldirs as $filename=>$cd){
			$cdrec .= $this->dirSignature;
			$cdrec .= "\x0\x0";
			$cdrec .= pack('v', $cd['vneeded']);
			$cdrec .= "\x0\x0";
			$cdrec .= pack('v', $cd['cmethod']);
			$cdrec .= pack('v', $cd['modtime']);
			$cdrec .= pack('v', $cd['moddate']);
			$cdrec .= pack('V', $cd['crc_32']);
			$cdrec .= pack('V', $cd['comsize']);
			$cdrec .= pack('V', $cd['uncsize']);
			$cdrec .= pack('v', strlen($filename));
			$cdrec .= pack('v', 0);
			$cdrec .= pack('v', strlen($cd['comments']));
			$cdrec .= pack('v', 0);
			$cdrec .= pack('v', 0);
			$cdrec .= pack('V', $cd['external_attributes']);
			$cdrec .= pack('V', $cd['offset']);
			$cdrec .= $filename;
			$cdrec .= $cd['comments'];
		}
		$before_cd = ftell($fh);
		fwrite($fh, $cdrec);

		fwrite($fh, $this->dirSignatureE);
		fwrite($fh, pack('v', 0));
		fwrite($fh, pack('v', 0));
		fwrite($fh, pack('v', $this->files_count));
		fwrite($fh, pack('v', $this->files_count));
		fwrite($fh, pack('V', strlen($cdrec)));
		fwrite($fh, pack('V', $before_cd));
		fwrite($fh, pack('v', strlen($zipComments)));
		fwrite($fh, $zipComments);

		fclose($fh);
	}

	Function appendCentralDir($filename,$properties){
		$this->centraldirs[$filename] = $properties;
	}
}


// END myRepono Backup API

// Copyright 2017 ionix Limited

// See header for additional copyright and license information.

?>