<?php
/**
 * 微信消息回调
**/
$nosession=true;
include("./includes/common.php");

if(!$conf['wechat_token'])exit();

$wxmsg = new \lib\WechatMsg($conf['wechat_token']);

$typeinfo = $DB->getRow("select * from pre_type where name='wx' limit 1");
if($typeinfo['status']==0)exit();
if(!$typeinfo || !$typeinfo['config'])exit();
$config = json_decode($typeinfo['config'], true);
if($config['mplogintype']!=1)exit();

$scene_str = $wxmsg->getSceneStr();
if($scene_str){
	$is_subscribe = $wxmsg->getRequest('Event') == 'subscribe';
	$openid = $wxmsg->getRequest('FromUserName');
	$logid = intval($scene_str);
	$row = $DB->getRow("SELECT * FROM pre_logs WHERE id=:id LIMIT 1", [":id"=>$logid]);
	if(!$row)exit('No Logs');
	if(strtotime($row['addtime'])<time()-60*30)exit('Expired');

	//查询用户信息
	$wxqrcode = new \lib\WechatQrcode($config['mpappid'], $config['mpsecret']);
	try{
		$userinfo = $wxqrcode->getUserInfo($openid);
		$sex = '';
		if($userinfo['sex'] == 2)$sex = '女';
		elseif($userinfo['sex'] == 1)$sex = '男';
		$location = $userinfo['province'].$userinfo['city'];
		if(!empty($config['openappid']) && !empty($userinfo['unionid'])) $openid = $userinfo['unionid'];
	}catch(Exception $ex){
		file_put_contents ( SYSTEM_ROOT."error.log", date ( "Y-m-d H:i:s" ) . "  " . $ex->getMessage() . "\r\n", FILE_APPEND );
		exit;
	}

	//显示回复消息内容
	if($is_subscribe){
		$message = $conf['wechat_message'] ? $conf['wechat_message'] : '登录成功';
	}else{
		$message = $conf['wechat_message2'] ? $conf['wechat_message2'] : '登录成功';
	}
	$wxmsg->responseText($message);

	$account = $DB->getRow("SELECT * FROM pre_accounts WHERE appid=:appid AND type=:type AND openid=:openid LIMIT 1", [":appid"=>$row['appid'], ":type"=>$row['type'], ":openid"=>$openid]);
	if($account){
		$DB->exec("UPDATE `pre_accounts` SET `nickname`=:nickname,`faceimg`=:faceimg,`location`=:location,`gender`=:gender,`ip`=:ip,`lasttime`=NOW() WHERE id=:id", [':nickname'=>$userinfo['nickname'], ':faceimg'=>$userinfo['headimgurl'], ':location'=>$location, ':gender'=>$sex, ':ip'=>$row['ip'], ':id'=>$account['id']]);
	}else{
		$DB->exec("INSERT INTO `pre_accounts` (`uid`, `appid`, `type`, `openid`, `token`, `nickname` ,`faceimg` ,`location` ,`gender` ,`ip`, `addtime`, `lasttime`, `status`) VALUES (:uid, :appid, :type, :openid, :token, :nickname, :faceimg, :location, :gender, :ip, NOW(), NOW(), 1)", [':uid'=>$row['uid'], ':appid'=>$row['appid'], ':type'=>$row['type'], ':openid'=>$openid, ':token'=>'wxtoken', ':nickname'=>$userinfo['nickname'], ':faceimg'=>$userinfo['headimgurl'], ':location'=>$location, ':gender'=>$sex, ':ip'=>$row['ip']]);
	}

	$DB->exec("UPDATE `pre_logs` SET `ucode`=:ucode,`openid`=:openid,`endtime`=NOW(),`status`=1 WHERE id=:id", [':ucode'=>'wxqrcode', ':openid'=>$openid, ':id'=>$row['id']]);
}
