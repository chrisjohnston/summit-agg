<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Facebook auth interaction
 */
class Fl_Facebook {

	/**
	 * Get facebook cookie
	 * 
	 * @return array
	 */
	public static function get_facebook_cookie()
	{
		// get the fb app id, and secret from CI config source
		$app_id             = Fl_Config::get('fb','app_id');
		$application_secret = Fl_Config::get('fb','secret');
		
		if(isset($_COOKIE['fbs_'.$app_id]))
		{
			$args = array();
			parse_str(trim($_COOKIE['fbs_'.$app_id], '\\"'), $args);
			ksort($args);
			$payload = '';
			
			foreach($args as $key=>$value)
			{
				if($key != 'sig')
				{
					$payload .= $key.'='.$value;
				}
			}
			if(md5($payload.$application_secret) != $args['sig'])
			{
				return NULL;
			}
			return $args;
		}
		return NULL;
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Get user
	 * 
	 * Get the user from the facebook cookie
	 * 
	 * @return type
	 */
	public static function getUser()
	{
		$cookie = self::get_facebook_cookie();
		$user = @json_decode(file_get_contents('https://graph.facebook.com/me?access_token='.$cookie['access_token']), true);
		return $user;
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Get picture
	 * 
	 * @return type
	 */
	public static function getPicture()
	{
		$cookie = self::get_facebook_cookie();
		$user = @json_decode(file_get_contents('https://graph.facebook.com/me/picture?access_token='.$cookie['access_token']), true);
		return $user;
	}

	// -------------------------------------------------------------------------
	
	// get any user
	public static function get_any_user($user_id)
	{
		$cookie = self::get_facebook_cookie();
		$user = @json_decode(file_get_contents('https://graph.facebook.com/'.$user_id.'?access_token='.$cookie['access_token']), true);
		return $user;
	}

	// -------------------------------------------------------------------------
	
	public static function getFriendIds($include_self = TRUE)
	{
		$cookie = self::get_facebook_cookie();
		$friends = @json_decode(file_get_contents(
					'https://graph.facebook.com/me/friends?access_token='.
					$cookie['access_token']), true);
		$friend_ids = array();
		foreach($friends['data'] as $friend)
		{
			$friend_ids[] = $friend['id'];
		}
		if($include_self == TRUE)
		{
			$friend_ids[] = $cookie['uid'];
		}
		return $friend_ids;
	}
	
	// -------------------------------------------------------------------------

	public static function getFriends($include_self = TRUE)
	{
		$cookie = self::get_facebook_cookie();
		$friends = @json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token='.$cookie['access_token']), true);
		if($include_self == TRUE)
		{
			$friends['data'][] = array('name'=>'You', 'id'=>$cookie['uid']);
		}
		return $friends['data'];
	}
	
	// -------------------------------------------------------------------------

	public static function getFriendsFriends($friend, $include_self = TRUE)
	{
		$cookie = self::get_facebook_cookie();
		$friends = @json_decode(file_get_contents('https://graph.facebook.com/'.$friend.'/friends?access_token='.$cookie['access_token']), true);
		echo $cookie['access_token'];
		if($include_self == TRUE)
		{
			$friends['data'][] = array('name'=>'You', 'id'=>$cookie['uid']);
		}
		return $friends['data'];
	}

	// -------------------------------------------------------------------------
	
	public static function getFriendArray($include_self = TRUE)
	{
		$cookie = self::get_facebook_cookie();
		$friendlist = @json_decode(file_get_contents(
					'https://graph.facebook.com/me/friends?access_token='.
					$cookie['access_token']), true);
		$friends = array();
		foreach($friendlist['data'] as $friend)
		{
			$friends[$friend['id']] = $friend['name'];
		}
		if($include_self == TRUE)
		{
			$friends[$cookie['uid']] = 'You';
		}
		return $friends;
	}

}