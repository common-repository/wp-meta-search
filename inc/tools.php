<?php

//get unique meta values
function yks_wpms_get_meta_values($key){
	global $wpdb;
	if($key){
		$res = $wpdb->get_results(
			$wpdb->prepare("
				SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s
				 GROUP BY meta_value" , $key) , ARRAY_N
			);
		return $res;
	}
}