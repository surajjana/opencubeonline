<?php 

	/* 
		This function might produce bugs
		on Windows(R) servers
		P.S: Try uncommenting the commented line
	*/
	function get_domain()
	{
		$http_host = $_SERVER['HTTP_HOST'];
		$DOC_ROOT = $_SERVER['DOCUMENT_ROOT'];
		$site_root = str_replace($DOC_ROOT, "", SITE_ROOT);
		// $win = str_replace("/","\\",$DOC_ROOT);
		$site_root = str_replace($DOC_ROOT,"",SITE_ROOT);
		$http_chunk = "$http_host/$site_root/";
		return "http://".str_replace("//", "/", $http_chunk);
	}

	function get_title(){
		if(defined('SITE_TITLE')){
			return SITE_TITLE;
		}else{
			return 'OpenCube';
		}
	}

	function get_slogan(){
		if(defined('SITE_SLOGAN')){
			return SITE_SLOGAN;
		}else{
			return 'Inspiring and Promoting technologies';
		}
	}

	function get_events($value='')
	{
		global $db_obj;
		$query = "SELECT * FROM `EVENTS` ORDER BY `EVENT_ID` DESC LIMIT 5";
		$ret = $db_obj->query($query);
		if($ret){
			$vals = $db_obj->fetchrowset();
			return $vals;
		}else{			
			return array("No announcements available");
		}
	}

	function get_logo(){
		$v = get_domain()."/styles/logo.png";
		return $v;
	}

	function ellipsize($string,$max_len)
	{
		if (strlen($string) <= $max_len) {
			return $string;			
		}else{
			return substr($string,0,$max_len)."..."	;	
		}
	}

	function register_user($username,$email,$institution){
		# TODO: Implement a registration method
	}

	function is_loggedin(){
		session_start();
		if (isset($_SESSION['user']['type'])) {
			if($_SESSION['user']){
				return true;
			}			
		}
		return false;
	}

