<?php
namespace App\Common\Auth;
use Firebase\JWT\JWT;
/**
 * 
 */
class JwtAuth
{
	private static $token;
	private static $instance;
	private $iss = 'http://laravel.com';
	private $aud = 'http://laravel.com';
	private $key = 'example_key';	//密匙
	private $uid ;
	private $aig =  'HS256';		//加密算法
	private $enToken ;		//加密算法
	public static function getInstance()
	{
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function setUid($uid)
	{	
		$this->uid = $uid;
		return $this;
	}
	public function encode()
	{
		$time = time();
		$token = array(
		    "iss" => $this->iss,
		    "aud" => $this->aud,
		    "iat" => $time-1,
		    "exp" => $time + 600,
		);
		self::$token = JWT::encode($token, $this->key,$this->aig);
		//$res = JWT::decode(self::$token, $this->key, array('HS256'));
		return $this;
	}
	public function decode($token)
	{

		$this->isToken = JWT::decode($token, $this->key,array($this->aig));
		return $this;
	}
	//验证
	public function isToken()
	{
		$info = (array)$this->isToken;
		if($info['iss'] != $this->iss) ajaxreturn('','iss错误');
		if($info['aud'] != $this->aud) ajaxreturn('','aud错误');
	}
	public function getToken()
	{
		return (string)self::$token;
	}
	public function setToken($token)
	{
		$this->token = $token;
		return $this;
	}
	private function __construct()
	{
		# code...
	}
	private function __clone()
	{

	}
}