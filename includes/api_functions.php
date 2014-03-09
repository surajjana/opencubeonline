<?php 

	function api_get_title(){
		$val = array('title'=>get_title());
		return json_encode($val);
	}

	function api_get_slogan(){
		$val = array('slogan'=>get_slogan());
		return json_encode($val);	
	}

	