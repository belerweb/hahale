<?php
/**
 * Fetch URL class
 *
 * Fetch a page by url
 *
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2011 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package fetch_url
 * @version 1.1 - 2011-02-23
 */
class Fetch_url {
	public $header;
	public $source;
	public $error;
	
	function __construct($url, $posts=null, $save_cookie_path=null, $new_session=null, $user_agent=null) {
		// nollställer header
		$this->header = '';
		$this->error = '';

		if ($save_cookie_path) {
			// Skapar en cookie fil om den inte existerar
			if (!file_exists($save_cookie_path)) {
				$handle = @fopen($save_cookie_path, 'w');
				if (!$handle) {
					$this->error = 'Cannot create cookie file';
					return;
				}
				fclose($handle);
				chmod($save_cookie_path, 0777);
			}
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); // set url to get 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable 
		curl_setopt($ch, CURLOPT_TIMEOUT, 9); // times out after 9s 
		curl_setopt($ch, CURLOPT_HEADER, 0); // output header

		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(__class__,'header_callback'));
		
		if ($user_agent) {
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		}
		
		if ($new_session) {
			curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
		}
		
		// if $_POST
		if ($posts) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);
		} 
		if ($save_cookie_path) {
			curl_setopt($ch, CURLOPT_COOKIEJAR, $save_cookie_path); // spara
			curl_setopt($ch, CURLOPT_COOKIEFILE, $save_cookie_path); // hämta
		}
		$this->source = curl_exec($ch);
		
		if ($curl_error = curl_error($ch)) {
			$this->error = 'CURL error : ' . $curl_error;
			return;
		}
		
		if ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
			$this->error = 'HTTP code is ' . $http_code;
		}
		
		curl_close($ch);
	}
	
	function header_callback($ch, $data) {
		$this->header .= $data;
		return strlen($data);
	}
	
	public function get_header($type) {
		if (!strstr($this->header,$type)) return false;
		$header = nl2br(strstr($this->header,$type));
		$header = explode("<br />",$header);
		return $header[0];
	}
}


/**
 * Smush.it API
 *
 * API for smush.it
 * Optimizes image
 * GIF are converted to PNG at Smush.it
 *
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2011 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package smush.it API
 * @version 1.0 - 2011-02-23
 */

defined('SMUSH_VERSION') or define('SMUSH_VERSION', 1);
defined('SMUSH_USER_AGENT') or define('SMUSH_USER_AGENT', 'SmushAPI/'.SMUSH_VERSION.' (+https://github.com/codler/Smush.it-API)');

function smush_file($optimize_file, $optimized_file=null) {
	if (!$optimized_file) $optimized_file = $optimize_file;
	
	// send file
	$obj = new Fetch_url('http://www.smushit.com/ysmush.it/ws.php', array('files[]' => '@'.$optimize_file), null, null, SMUSH_USER_AGENT);
	
	return _smush($obj, $optimized_file);
}

/*
function smush_url($url, $optimized_file) {
	
	// send file
	$obj = new Fetch_url('http://www.smushit.com/ysmush.it/ws.php?img='.urlencode($url), null, null, null, SMUSH_USER_AGENT);
	
	return _smush($obj, $optimized_file);
}
*/

function _smush($obj, $dest) {
	
	if ($obj->error) {
		return array(
			'error' => 'Send error: ' . $obj->error
		);
		return false;
	}
	
	if (strpos(trim($obj->source), '{') != 0) {
		return array(
			'error' => 'Response error'
		);
		return false;
	}
	
	$s = json_decode($obj->source);
	
	if ( -1 === intval($s->dest_size)) {
		return array(
			'error' => 'Smush error1: ' . print_r($s, true)
		);
		return false;
	}
	
	if (!$s->dest) {
		return array(
			'error' => 'Smush error2: ' . print_r($s, true)
		);
		return false;
	}
	
	$url = $s->dest;
	$percent = $s->percent;
	$saving = $s->src_size - $s->dest_size;
	
	
	
	// Save file
	$handle = fopen($dest, 'w');

	/*
	if (!$handle) {
		return array(
			'error' => 'Save error',
			'path'	=> $dest
		);
		return false;
	}
	*/
	
	$obj = new Fetch_url($url, null, null, null, 'SmushAPI/'.SMUSH_VERSION);
	
	if ($obj->error) {
		fclose($handle);
		return array(
			'error' => 'Fetch error'
		);
		return false;
	}
	
	$content=$obj->source;
	fwrite($handle, $content);
	fclose($handle);
	return array(
		'percent' => $percent,
		'saving' => $saving
	);
}
?>