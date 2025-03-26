<?php
/**
 * 支付宝/微信中转登录页面
**/
$nosession=true;
include("./includes/common.php");

include SYSTEM_ROOT.'txprotect.php';

$array = explode('||||',authcode2($_GET['state'], 'DECODE', SYS_KEY));
$type = $array[0];
$logid = $array[1];
if(!$type || !$logid)sysmsg('参数校验失败');
$row = $DB->getRow("select * from pre_logs where id=:id limit 1", ["id"=>$logid]);
if(!$row)sysmsg('记录不存在');

$typeinfo = $DB->getColumn("select config from pre_type where name=:name limit 1", ["name"=>$type]);
if(!$typeinfo)sysmsg('当前登录方式未配置密钥');
$config = json_decode($typeinfo, true);

$apptype = $DB->getColumn("select type from pre_apps where appid='{$row['appid']}' limit 1"); //是否获取用户信息

// 非客户端内打开的登录请求，加入登录完成后不跳转的标记
if(isset($_GET['client']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))){
	$DB->exec("UPDATE pre_logs SET client=1 WHERE id=:id", ["id"=>$logid]);
}

$return = api_call($type, $config, 'jump', [$_GET['state'], $apptype]);

$qrcode_url = $siteurl.'jump.php?state='.urlencode($_GET['state']).'&client=1';

if($return){
	include $return;
}
