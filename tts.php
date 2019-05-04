<?php
require_once(dirname(__FILE__).'/TTS.class.php');
if ($argc <= 1) exit('缺少必须参数。请在命令行执行本程序！');

// 这里预置了测试的AK/SK，仅供调试，QPS并不高。最后一个参数是调试模式，如果要关闭调试模式请注意配置服务器的安全证书。
$tts = new TTSUtils('4E1BG9lTnlSeIf1NQFlrSq6h', '544ca4657ba8002e3dea3ac2f5fdd241', true);

$style = !empty($argv[2]) ? $argv[2] : 4;
$spdval = !empty($argv[3]) ? $argv[3] : 5;
$pitvalue = !empty($argv[4]) ? $argv[4] : 5;
$volumn = !empty($argv[5]) ? $argv[5] : 5;

$text = $tts->getSound($argv[1], $style, $spdval, $pitvalue, $volumn);
echo $text;

// 直接启动Windows Media Player播放
if (file_exists(dirname(__FILE__).'/result.mp3')) {
	shell_exec('"%programfiles%\windows media player\wmplayer.exe" '.dirname(__FILE__).'/result.mp3');
}