<?php
/**
 * Helper functions to increase productivity
 *
 *  @package DP Framework
 * @subpackage Functions
 */

function dp_dir_path( $file = '' ) {
	$dir = dirname($file) ;
	$dir = str_replace('\\','/',$dir); // sanitize for Win32 installs
	$dir = trailingslashit($dir);
	
	return $dir;
}

function dp_dir_url( $file = '' ) {
	$dir = dp_dir_path( $file );

	$root = $_SERVER['DOCUMENT_ROOT'];
	$root = str_replace('\\\\', '/', $root);
	
	$url = substr( $dir, strlen($root) );
	
	$scheme = is_ssl() && !is_admin() ? 'https://' : 'http://';
	$url = $scheme . trailingslashit($_SERVER['HTTP_HOST'].'/') . $url;
	
	return $url;
}

function is_robot(){
    static $is_robot = null;

    if(null == $is_robot){
    $is_robot = false;
    $robotlist = 'bot|spider|crawl|nutch|lycos|robozilla|slurp|search|seek|archive';
    if( isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/{$robotlist}/i", $_SERVER['HTTP_USER_AGENT']) ){
    $is_robot = true;
    }
    }

    return $is_robot;
} 
 
function wp_mshot($url = '', $width = 250) {

	if ($url != '') {
		return 'http://s.wordpress.com/mshots/v1/' . urlencode(clean_url($url)) . '?w=' . $width;
	} else {
		return '';
	}

} 
 
/**
 * Check a string within a string, this function will be useful 
 * when strpos() doesn't determine correctly.
 *
 * @since 1.0
 * @param string $a Finding in this string.
 * @param string $b Finding this string.
 */
function in_str($string, $find) {
	$check = explode($find, $string);
	return (count($check) > 1);
}

function is_url($url) {
    $info = parse_url($url);
    return ($info['scheme']=='http' || $info['scheme']=='https') && $info['host'] != "";
} 

function is_image($handler) {
    $ext = preg_match('/\.([^.]+)$/', $file, $matches) ? strtolower($matches[1]) : false;
	$image_exts = array('jpg', 'jpeg', 'gif', 'png');
	
	return in_array($ext, $image_exts);
}  

/**
 * Check if an array is associative.
 *
 * @since 1.0
 * @param array $arr
 * @return bool
 */
function is_assoc($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * Sanitize a string for filed name.
 *
 * Keys are used as internal identifiers. Lowercase alphanumeric characters and underscores are allowed.
 *
 * @since 1.0
 *
 * @param string $name String name
 * @return string Sanitized name
 */
function sanitize_field_name($name) {
	$raw_name = $name;
	$name = strtolower( $name );
	$name = preg_replace( '/[^a-z0-9_\[\]]/', '_', $name );
	return apply_filters( 'sanitize_field_name', $name, $raw_name );
}

function sanitize_field_value($value) {
	$raw_value = $value;
	$value = strtolower( $value );
	$value = preg_replace( '/[^a-z0-9_]/', '_', $value );
	return apply_filters( 'sanitize_field_value', $value, $raw_value );
}

/**
 * Sanitize a string for field id or class.
 *
 * Keys are used as internal identifiers. Lowercase alphanumeric characters and dashes are allowed.
 *
 * @since 1.0
 *
 * @param string $id String id
 * @return string Sanitized id
 */
function sanitize_field_id($id) {
	$raw_id = $id;
	$id = strtolower( $id );
	$id = str_replace(']', '', $id);
	$id = preg_replace( '/[^a-z0-9\-]/', '-', $id );
	return apply_filters( 'sanitize_field_id', $id, $raw_id );
}

function pre($r, $callback = 'print_r') {
	echo '<pre>'.call_user_func('print_r', $r, 1).'</pre>';
}

function pre_print_r($r, $clean = true) {
	$r =  print_r($r, 1);
	if($clean)
		$r =  esc_html($r);
			
	echo '<pre>'.$r.'</pre>';
}

function pre_var_export($r, $clean = true) {
	$r =  var_export($r, true);
	if($clean)
		$r =  esc_html($r);
			
	echo '<pre>'.$r.'</pre>';
}

/**
 * Sort an two-dimension array by the level two values use array_multisort() function.
 *
 */
function mdsort() {
	$args = func_get_args();
	
	$array = array_shift($args);
	
	if (!is_array($array))
		return false;
		
	$params = array();
	
	foreach ($args as $arg) {
		if (is_string($arg)) {
			$sort = array();
			foreach ($array as $key => $row)
				$sort[$key] = $row[$arg];
		
			$params[] = $sort;
		} else
			$params[] = $arg;
	}
	
	$params[] = &$array;
	
	call_user_func_array("array_multisort", $params);
	
	return $array;
}

function dp_dir2url( $path = '', $dir = '' ) {
	if( !$dir )
		$dir = __FILE__;

	$dir = dirname(plugin_basename($dir));
	
	$url = substr( $dir, strlen($_SERVER['DOCUMENT_ROOT']) );
	$scheme = is_ssl() && !is_admin() ? 'https://' : 'http://';
	$url = $scheme . $_SERVER['HTTP_HOST'] . $url;
	$url = trailingslashit($url);
	
	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= '/' . ltrim($path, '/');
		
	return $url;
}

function strip_empty_tags($content = '') {
	if($content == '')
		return;

	return preg_replace("#<[^>/]+>\s</[^>/]+>#", '', $content);
}

function get_current_url($query_string = true) {
	$scheme = !empty($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on" ? 'https://' : 'http://';
	
	$url = $_SERVER['REQUEST_URI'];
	if(!$query_string)
		$url = str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
	
	$url = $scheme . $_SERVER['HTTP_HOST'] . $url;
	
	return $url;
}

function get_ip() {
	$ip = '';
	
	if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP"); 
	elseif(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR"); 
	elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR']; 

	return $ip;
}

/**
 * Shorten long numbers to K/M/B (Thousand, Million and Billion)
 *
 * @param int $number The number to shorten.
 * @param int $decimals Precision of the number of decimal places.
 * @param string $suffix A string displays as the number suffix.
 */
if(!function_exists('short_number')) {
function short_number($n, $decimals = 2, $suffix = '') {
	if(!$suffix)
		$suffix = 'K,M,B';
	$suffix = explode(',', $suffix);

    if ($n < 1000) { // any number less than a Thousand
        $shorted = number_format($n);
    } elseif ($n < 1000000) { // any number less than a million
        $shorted = number_format($n/1000, $decimals).$suffix[0];
    } elseif ($n < 1000000000) { // any number less than a billion
        $shorted = number_format($n/1000000, $decimals).$suffix[1];
    } else { // at least a billion
        $shorted = number_format($n/1000000000, $decimals).$suffix[2];
    }

    return $shorted;
}
}

/**
 * Determines the difference between two timestamps.
 *
 * The difference is returned in a human readable format such as "1 hour",
 * "5 mins", "2 days".
 *
 * @param int $from Unix timestamp from which the difference begins.
 * @param int|string $to Optional. Unix timestamp to end the time difference, or time type either 'mysql' or 'timestamp'. Default becomes current_time('timestamp') if not set.
 * @param int $limit Optional. The number of unit types to display (i.e. the accuracy). Defaults to 1. 
 * @return string Human readable time difference.
 */
function human_time( $from, $to = '', $limit = 1 ) {
	// Since all months/years aren't the same, these values are what Google's calculator says
	$units = apply_filters( 'time_units', array(
			31556926 => array( __('%s year', 'dp'),  __('%s years', 'dp') ),
			2629744  => array( __('%s month', 'dp'), __('%s months', 'dp') ),
			604800   => array( __('%s week', 'dp'),  __('%s weeks', 'dp') ),
			86400    => array( __('%s day', 'dp'),   __('%s days', 'dp') ),
			3600     => array( __('%s hour', 'dp'),  __('%s hours', 'dp') ),
			60       => array( __('%s min', 'dp'),   __('%s mins', 'dp') )
	) );

	if($to == 'mysql')
		$to = current_time('mysql');
	elseif(empty($to) )
		$to = current_time('timestamp');

	$from = (int) $from;
	$to   = (int) $to;
	$diff = (int) abs( $to - $from );

	$items = 0;
	$output = array();

	foreach ( $units as $unitsec => $unitnames ) {
		if ( $items >= $limit )
			break;

		if ( $diff < $unitsec )
			continue;

		$numthisunits = floor( $diff / $unitsec );
		$diff = $diff - ( $numthisunits * $unitsec );
		$items++;

		if ( $numthisunits > 0 )
			$output[] = sprintf( _n( $unitnames[0], $unitnames[1], $numthisunits ), $numthisunits );
	}

	// translators: The seperator for human_time_diff() which seperates the years, months, etc.
	$seperator = _x( ', ', 'human_time_diff', 'dp' );

	if ( !empty($output) ) {
		return implode( $seperator, $output );
	} else {
		$smallest = array_pop( $units );
		return sprintf( $smallest[0], 1 );
	}
}

if(!function_exists('mb_strimwidth')) {
function mb_strimwidth($str ,$start , $width ,$trimmarker='' ){
    $output = preg_replace('/^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$start.'}((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$width.'}).*/s','\1',$str);
    return $output.$trimmarker;
}
}