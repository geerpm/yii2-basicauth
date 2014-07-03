<?php
/** 
 * yii2 BasicAuth component
 * 
 * In controller
 * ~~~
 * class AdminController extends Controller
 * {
 * 	public function behaviors()
 * 	{
 * 		return [
 * 			'basicAuth' => [
 * 				'class' => "app\components\BasicAuth"
 * 			],
 * 		];
 * 	}
 * }
 * ~~~
 * 
 * In params.php
 * ~~~
 * return [
 *     'basicAuth'  => [
 * 		'username' => 'password',
 * 	],
 * ];
 * ~~~
 * 
 * @author geerpm
 */
namespace app\components;

use Yii;
use yii\base\Behavior;

class BasicAuth extends Behavior
{
	/** 
	 * key
	 * @var string
	 */
	const SESSKEY = '___basicauth_yii2_authentication__v1__';
	
	
	/** 
	 * init
	 * @param string	$identifier
	 * @param string	$password
	 * @return void
	 */
	public function __construct()
	{
		if (! self::isAuthorized() && ! self::authenticate()) {
			self::returnUnauthenticatedState();
		}
	}


	/** 
	 * authenticate
	 * @return bool
	 */
	public static function authenticate()
	{
		$username = static::env('PHP_AUTH_USER');
		$pass = static::env('PHP_AUTH_PW');
		if (empty($username) || empty($pass)) {
			return false;
		}
		$pairs = Yii::$app->params['basicAuth'];
		if (empty($pairs) || ! is_array($pairs)) {
			return false;
		}

		foreach ($pairs as $u => $p) {
			if ($username === $u && $pass === $p) {
				Yii::$app->session[self::SESSKEY] = time();
				return true;
			}
		}
		return false;
	}
	
	
	/** 
	 * isAuthorized
	 * @return bool
	 */
	public static function isAuthorized()
	{
		return (Yii::$app->session[self::SESSKEY] > 0);
	}
	
	
	/** 
	 * isAuthRequest
	 * @return bool
	 */
	public static function isAuthRequest()
	{
		return (strlen(static::env('PHP_AUTH_USER')) > 0);
	}


	/** 
	 * require authenticated request
	 * @return void
	 */
	public static function returnUnauthenticatedState()
	{
		while (@ob_end_clean());
		header(sprintf('WWW-Authenticate: Basic realm="%s"', static::env('SERVER_NAME')));
		header('HTTP/1.0 401 Unauthorized');
		echo "401 Unauthorized.";
		exit;
	}
	
	
	/** 
	 * get env
	 * @param string	$key
	 * @return string
	 */
	public static function env($key)
	{
		return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
	}
}