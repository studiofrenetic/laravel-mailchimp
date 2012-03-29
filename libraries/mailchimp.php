<?php

/**
 * A LaravelPHP Package for working w/ Mailchimp.
 *
 * @package    Mailchimp
 * @author     Scott Travis <scott.w.travis@gmail.com>
 * @link       http://github.com/swt83
 * @license    MIT License
 */

class Mailchimp
{
	public static function __callStatic($method, $args)
	{
		// load api key
		$api_key = Config::get('mailchimp::mailchimp.api_key');
		
		// determine endpoint
		list($ignore, $server) = explode('-', $api_key);
		$endpoint = 'https://'.$server.'.api.mailchimp.com/1.3/';
		
		// build query
		$params = array(
			'output' => 'json',
			'method' => $method,
		);
		$arguments = isset($args[0]) ? $args[0] : array();
		$query = http_build_query($params + array('apikey'=>$api_key) + $arguments);
		
		// setup curl request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint.'?'.$query);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		$response = curl_exec($ch);

		// catch errors
		if (curl_errno($ch))
		{
			$errors = curl_error($ch);
			curl_close($ch);
			
			// return false
			return false;
		}
		else
		{
			curl_close($ch);
			
			// return array
			return json_decode($response);
		}
	}
}