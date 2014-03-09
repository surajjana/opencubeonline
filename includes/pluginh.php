<?php 
	
	global $filters,$actions;
	$filters = array();
	$actions = array();


	#*********************	FILTERS STARTS HERE ***************************

	function add_filter($tag, $callback_name, $priority=10)
	{	
		global $filters;
		if (isset($tag) && !empty($tag)) {
			if (function_exists($callback_name)) {
				$filters[$tag][$priority][] = $callback_name;
				return true;
			}
		}
		return 0;
	}

	function has_filter($tag,$function_name = null)
	{
		global $filters;
		$has = !empty($filters[$tag]);
		if (isset($function_name)) {
			foreach ($filters[$tag] as $pkey => $pvalue) {
				foreach ($filters[$tag][$pkey] as $key => $value) {
					if ($value == $function_name) {
						$has = $pkey;
						return $has;
					}
				}
			}
		}
		if (!$has) {
			return 0;
		}
		return $has;
	}

	// args can be provided after the $tag.
	// Returns an array of resuts.
	// The retval[0] will be the parameter after applying te filter 
	// and the retval[1] will be the actual parameters passed while
	// calling the filter
	function apply_filter($tag)
	{
		global $filters;

		$num_args = func_num_args();
		$args = func_get_args();
		$tag = array_shift($args);
		$arg_ = $args;
		if (has_filter($tag)){
			foreach ($filters[$tag] as $pkey => $pvalue) {
				foreach ($filters[$tag][$pkey] as $key => $value) {
					$args[1] = $arg_;
					$args = (array) call_user_func_array($value, $args); 
				}
			}
		}
		return $args;		
	}

	function remove_filter($tag, $function_name , $priority = 10)
	{
		global $filters;
		$isset = isset($filters[$tag][$priority][$function_name]); 
		if ($isset) {
			unset($filters[$tag][$priority][$function_name]);
			if (empty($filters[$tag][$priority])) {
				unset($filters[$tag][$priority]);
			}
		}
		return $isset;
	}

	function remove_all_filters($tag,$priority = false)
	{
		global $filters;
		if (false !== $priority && isset($filters[$tag][$priority])) {
			unset($filters[$tag]);
		}else{
			unset($filters[$tag]);
		}
		return 1;
	}


	#*********************	ACTIONS STARTS HERE ***************************


	function add_action($event, $callback_name, $priority=10)
	{	
		global $actions;
		if (isset($event) && !empty($event)) {
			if (function_exists($callback_name)) {
				$actions[$event][$priority][] = $callback_name;
				return true;
			}
		}
		return 0;
	}

	function has_callback($event,$function_name = null)
	{
		global $actions;
		$has = !empty($actions[$event]);
		if (isset($function_name)) {
			foreach ($actions[$event] as $pkey => $pvalue) {
				foreach ($actions[$event][$pkey] as $key => $value) {
					if ($value == $function_name) {
						$has = $pkey;
						return $has;
					}
				}
			}
		}
		if (!$has) {
			return 0;
		}
		return $has;
	}

	// args can be provided after the $tag.
	function exec_action($event)
	{
		global $actions;

		$num_args = func_num_args();
		$args = func_get_args();

		$tag = array_shift($args);
		if (has_callback($event)){
			foreach ($actions[$event] as $pkey => $pvalue) {
				foreach ($actions[$event][$pkey] as $key => $value) {
					$args = call_user_func_array($value, $args); 
					if ($args == null) {
						$args = array();
					}
				}
			}
		}
		return $args;		
	}

	function remove_action($event, $function_name , $priority = 10)
	{
		global $actions;
		$isset = isset($actions[$event][$priority][$function_name]); 
		if ($isset) {
			unset($actions[$event][$priority][$function_name]);
			if (empty($actions[$event][$priority])) {
				unset($actions[$event][$priority]);
			}
		}
		return $isset;
	}

	function remove_all_actions($event,$priority = false)
	{
		global $actions;
		if (false !== $priority && isset($actions[$event][$priority])) {
			unset($actions[$event]);
		}else{
			unset($actions[$event]);
		}
		return 1;
	}


?>