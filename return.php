<?php
//登录回调文件
$nosession=true;
include './includes/common.php';

include SYSTEM_ROOT.'txprotect.php';

if($_GET['code']){
	$code = $_GET['code'];
}elseif($_GET['auth_code']){
	$code = $_GET['auth_code'];
}elseif($_GET['authCode']){
	$code = $_GET['authCode'];
}elseif($_GET['error'] && $_GET['error_description']){
	sysmsg('['.htmlspecialchars($_GET['error']).']'.htmlspecialchars($_GET['error_description']));
}else{
	exit;
}
$array = explode('||||',authcode2($_GET['state'], 'DECODE', SYS_KEY));
$type = $array[0];
$logid = $array[1];
if(!$type || !$logid)exit('Error');
$row = $DB->getRow("SELECT * FROM pre_logs WHERE id=:id LIMIT 1", [":id"=>$logid]);
if(!$row)exit('No Logs');
if(strtotime($row['addtime'])<time()-60*10)exit('Expired');

$DB->exec("UPDATE `pre_logs` SET `ucode`=:ucode,`ip`=:ip WHERE id=:id", [':ucode'=>$code, ':ip'=>real_ip(), ':id'=>$logid]);

// 微信公众号登录方式标记
if($type == 'wx' && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')){
	$DB->exec("UPDATE `pre_logs` SET `mode`=1 WHERE id=:id", [':id'=>$logid]);
}

// 非客户端内打开的登录请求，登录完成后不跳转
if($row['client']==1 && (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))){
	include PAGE_ROOT.'ok.php';exit;
}

$redirect_uri = '';
if(strpos($row['redirect'], '?')!==false){
	$redirect_uri .= $row['redirect'].'&';
}else{
	$redirect_uri .= $row['redirect'].'?';
}
$redirect_uri .= 'type='.$type.'&code='.urlencode($row['code']).'&state='.urlencode($row['state']);
header('Location: '.$redirect_uri);
exit;
