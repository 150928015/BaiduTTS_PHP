## 仓库简介

TTS语音合成

- php 下需要开启 curl 扩展，以便程序与在线API平台实现通信互动。



## 测试流程

### 修改tts.php

从网页中申请的应用获取appKey和appSecret

```php
# 填写网页上申请的appkey 如 $apiKey="g8eBUMSokVB1BHGmgxxxxxx"
$apiKey = "4E1BG9lTnlSeIf1NQFlrSq6h";
# 填写网页上申请的APP SECRET 如 $secretKey="94dc99566550d87f8fa8ece112xxxxx"
$secretKey = "544ca4657ba8002e3dea3ac2f5fdd241";
```


## 运行 tts.php，进行合成

命令为 php tts.php [需要合成的文本] [音色] [语速] [语调] [音量]

执行过程中，第一个参数必须传入，其他可选。具体参数格式详见“合成参数备注”，此处不再赘述。

结果在result.mp3，如果遇见错误，结果在error.json

其中

- Content-Type: audio/mp3，表示合成成功，可以播放MP3 result.mp3
- Content-Type: application/json 表示错误   result.txt打开可以看到错误信息的json

### 合成参数备注

```php

#发音人选择, 0为普通女声，1为普通男生，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女声
$per = 0;
#语速，取值0-9，默认为5中语速
$spd = 5;
#音调，取值0-9，默认为5中语调
$pit = 5;
#音量，取值0-9，默认为5中音量
$vol = 5;

```

Powered By DingStudio Technology &copy;2012-2019 All Rights Reserved.