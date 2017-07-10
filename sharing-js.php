<?php

require_once 'wechat.php';

$referPage = filter_var($_GET['refurl'], FILTER_VALIDATE_URL);

$weChat = Bosima_WeChat::getInstance();
$signPackage = $weChat->getSign($referPage);

?>

wx.config({
    debug: false,
    appId: '<?php echo $signPackage['appId']; ?>',
    timestamp: <?php echo $signPackage['timestamp']; ?>,
    nonceStr: '<?php echo $signPackage['nonceStr']; ?>',
    signature: '<?php echo $signPackage['signature']; ?>',
    jsApiList: [
    'onMenuShareTimeline',
		'onMenuShareAppMessage',
		'onMenuShareQQ',
		'onMenuShareWeibo',
		'onMenuShareQZone',
    ]
});