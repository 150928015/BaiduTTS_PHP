<?php
/**
 * 百度AI语音在线合成开放能力封装（TTS联网版）
 * 引用时请直接实例化本类库为对象进行操作
 * @package DingStudio/AI
 * @subpackage AI/TextToSound
 * @author David Ding
 * @copyright 2012-2019 DingStudio Technology All Rights Reserved
 */

class TTSUtils {

	// 定义AppKey，缺省Demo测试Key：4E1BG9lTnlSeIf1NQFlrSq6h
	protected $apiKey = '4E1BG9lTnlSeIf1NQFlrSq6h';
	// 定义SecretKey，缺省Demo测试Key：544ca4657ba8002e3dea3ac2f5fdd241
	protected $secretKey = '544ca4657ba8002e3dea3ac2f5fdd241';
	// 定义Oauth2认证中心地址，此处采用百度开放平台接入点
	protected $auth_url = 'https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id=%s&client_secret=%s';
	
	// 定义TTS开放接口进入点路由处理地址
	protected $tts_url = 'http://tsn.baidu.com/text2audio';
	// TTS合成结果标识（成功置为false，否则保持true）
	private $g_has_error = true;
	
	// 开发调试模式标识变量
	private $isDebug = false;

	// Oauth2后获取到的访问令牌
	private $accessToken = '';

	/**
	 * 构造函数
	 * @param string $ak 应用ID
	 * @param string $sk 通讯私钥
	 * @param boolean $debug 调试模式
	 */
	public function __construct($ak, $sk, $debug = false) {
		if (!function_exists('curl_init')) exit('Please enable the http curl extension.');
		$this->apiKey = $ak;
		$this->secretKey = $sk;
		$this->isDebug = $debug;
	}

	/**
	 * 调用接口Oauth2授权
	 * @return string
	 */
	private function authorize() {
		$ch = curl_init();
		$ch_options = array(
			CURLOPT_URL	=>	sprintf($this->auth_url, $this->apiKey, $this->secretKey),
			CURLOPT_RETURNTRANSFER	=>	1,
			CURLOPT_CONNECTTIMEOUT	=>	5,
			CURLOPT_SSL_VERIFYPEER	=>	!$this->isDebug,
			CURLOPT_SSL_VERIFYHOST	=>	0,
			CURLOPT_VERBOSE	=>	$this->isDebug
		);
		curl_setopt_array($ch, $ch_options);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			$err = curl_error($ch);
			curl_close($ch);
			return $err;
		}
		$result = json_decode($result, true);
		if (!isset($result['access_token'])){
			return "ERROR TO OBTAIN TOKEN";
		}
		if (!isset($result['scope'])){
			return "ERROR TO OBTAIN scopes";
		}
		if (!in_array('audio_tts_post',explode(" ", $result['scope']))){
			return "DO NOT have tts permission";
			// 请至网页上应用内开通语音合成权限
		}
		$this->accessToken = $result['access_token'];
		return 'OK';
	}

	/**
	 * 获取TTS语音合成结果
	 * @param string $text 所需合成的文本
	 * @param integer $person 合成音色：0为普通女声，1为普通男声，3为男声情感合成，4为女声情感合成，默认为3
	 * @param integer $speed 合成语速：取值区间0-15，默认为5，中语速
	 * @param integer $pit 合成语调：取值区间0-15，默认为5，中语调
	 * @param integer $vol 合成音量：取值区间0-9，默认为5，中音量
	 * @param string $aue 合成类型：3-mp3,4-pcm，5-pcm，6-wav，默认3
	 * @param string $format 合成扩展名：支持mp3、pcm以及wav
	 * @return mixed
	 */
	public function getSound($text, $person = 3, $speed = 5, $pit = 5, $vol = 5, $aue = 3, $format = 'mp3') {
		$login = $this->authorize();
		if ($login == 'OK') {
			$text2 = iconv("UTF-8", "GBK", $text);
			$params = array(
				'tex'	=>	urlencode($text),
				'per'	=>	$person,
				'spd'	=>	$speed,
				'pit'	=>	$pit,
				'vol'	=>	$vol,
				'aue'	=>	$aue,
				'cuid'	=>	'654321PHP',
				'tok'	=>	$this->accessToken,
				'lan'	=>	'zh',
				'ctp'	=>	1
			);
			$ch = curl_init();
			$ch_options = array(
				CURLOPT_URL	=>	$this->tts_url,
				CURLOPT_RETURNTRANSFER	=>	true,
				CURLOPT_CONNECTTIMEOUT	=>	5,
				CURLOPT_SSL_VERIFYPEER	=>	!$this->isDebug,
				CURLOPT_SSL_VERIFYHOST	=>	0,
				CURLOPT_POST	=>	1,
				CURLOPT_POSTFIELDS	=>	http_build_query($params),
				CURLOPT_HEADERFUNCTION	=>	function($ch, $header) {
					$comps = explode(":", $header);
					// 正常返回的头部 Content-Type: audio/*
					// 有错误的如 Content-Type: application/json
					if (count($comps) >= 2){
						if (strcasecmp(trim($comps[0]), "Content-Type") == 0){
							if (strpos($comps[1], "audio/") > 0 ){
								$this->g_has_error = false;
							}else{
								echo $header ." , has error \n";
							}
						}
					}
					return strlen($header);
				},
				CURLOPT_VERBOSE	=>	$this->isDebug
			);
			curl_setopt_array($ch, $ch_options);
			$result = curl_exec($ch);
			if (curl_errno($ch)) {
				$err = curl_error($ch);
				curl_close($ch);
				return $err;
			}
			curl_close($ch);
			$file = $this->g_has_error ? 'error.json' : 'result.'.$format;
			$obj = file_put_contents($file, $result);
			return $obj;
		} else {
			return $login;
		}
	}
}