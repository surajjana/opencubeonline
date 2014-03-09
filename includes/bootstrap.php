<?php 
	
	
	# Important line, DO NOT TAMPER WITH IT !
	define('SITE_ROOT', dirname(dirname(__FILE__))); 
	
	require_once SITE_ROOT.'/includes/config.php';    # Defines configurations
	require_once SITE_ROOT.'/includes/functions.php'; # Defines core functions 
	require_once SITE_ROOT.'/includes/pluginh.php';   # For plugin comaptibility
	require_once SITE_ROOT.'/includes/mysqllib.php';   # For plugin comaptibility
	include_once SITE_ROOT.'/contents/plugins/plugin_registry.php';



	exec_action('BOOTSTRAP_S'); # Bootstrapping strarts

	$db_obj = new dbAccess(DBHOST,DBUSER,DBPASSWD,DBNAME);
	if(!$db_obj){
		die("E: Could not complete your request because of an unrecoverable error");
	}

	exec_action('BOOTSTRAP_E'); # Bootstrapping ends





?>