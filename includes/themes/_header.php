<?php
	if ( !defined('THEME_LOAD') ) { die ( header('Location: /404') ); }
?>
<!DOCTYPE html>
<html>
	<head>

		<!-- Mobile Specific
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

		<!-- CSS
		================================================== -->
		<link rel="stylesheet" href="/assets/theme/css/master.css">

		<!--Morris Chart CSS -->
		<link rel="stylesheet" href="/assets/plugins/morris/morris.css">

		<!-- Plugins css -->
		<link href="/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="/assets/plugins/mjolnic-bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
		<link href="/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link href="/assets/plugins/clockpicker/bootstrap-clockpicker.min.css" rel="stylesheet">
		<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
		<link href="/assets/plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css"/>

		<!-- DataTables -->
		<link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
		<link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
		<!-- Responsive datatable examples -->
		<link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
		<!-- Multi Item Selection examples -->
		<link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />


		<!-- Bootstrap CSS -->
		<link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

		<!-- App CSS -->
		<link href="/assets/css/style.css" rel="stylesheet" type="text/css" />

		<!-- Switchery css -->
		<link href="/assets/plugins/switchery/switchery.min.css" rel="stylesheet" />

		<!--Form Wizard-->
		<link href="/assets/css/smart_wizard.css" rel="stylesheet" type="text/css" />
		<link href="/assets/css/smart_wizard_theme_dots.css" rel="stylesheet" type="text/css" />

		<?php
			if( !empty($array['favicon_folder']) ) {
		?>
		<!-- Favicons
		https://www.iconfinder.com/icons/2123889/app_check_essential_list_ui_icon
		https://realfavicongenerator.net
		================================================== -->
		<link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/<?php echo $array['favicon_folder']; ?>/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon/<?php echo $array['favicon_folder']; ?>/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon/<?php echo $array['favicon_folder']; ?>/favicon-16x16.png">
		<link rel="manifest" href="/assets/favicon/<?php echo $array['favicon_folder']; ?>/site.webmanifest">
		<link rel="mask-icon" href="/assets/favicon/<?php echo $array['favicon_folder']; ?>/safari-pinned-tab.svg" color="<?php echo $array['mask-icon-color']; ?>">
		<link rel="shortcut icon" href="/assets/favicon/<?php echo $array['favicon_folder']; ?>/favicon.ico">
		<meta name="msapplication-TileColor" content="<?php echo $array['TileColor']; ?>">
		<meta name="msapplication-config" content="/assets/favicon/<?php echo $array['favicon_folder']; ?>/browserconfig.xml">
		<meta name="theme-color" content="<?php echo $array['theme-color']; ?>">
		<?php
			}
		?>

		<title><?php get_title($array); ?></title>

		<!-- Modernizr js -->
		<script src="/assets/js/modernizr.min.js"></script>

		<style type="text/css">
			@media(min-width:992px){
				.fix-dropdown {
					height: 300px !important;
				}
			}
		</style>

</head>

<body class="fixed-left">

	<!-- Begin page -->
	<div id="wrapper">

		<?php require '_topbar.php'; ?>

		<?php require '_leftbar.php'; ?>
