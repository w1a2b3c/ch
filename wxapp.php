<?php
/**
 * 微信小程序登录接口
**/
$nosession=true;
include("./includes/common.php");

@header('Content-Type: application/json; charset=UTF-8');

if(!isset($_POST['state']))exit;

$array = explode('||||',authcode2($_POST['state'], 'DECODE', SYS_KEY));
$type = $array[0];
$logid = $array[1];
if(!$type || !$logid)exit(json_encode(['code'=>-1,'msg'=>'state参数校验失败']));
$row = $DB->getRow("select * from pre_logs where id=:id limit 1", ["id"=>$logid]);
if(!$row)exit(json_encode(['code'=>-1,'msg'=>'记录不存在']));

$typeinfo = $DB->getColumn("select config from pre_type where name='wx' limit 1");
if(!$typeinfo)exit(json_encode(['code'=>-1,'msg'=>'未配置密钥']));
$config = json_decode($typeinfo, true);
if($config['mplogintype']!=2)exit(json_encode(['code'=>-1,'msg'=>'未开启微信小程序登录']));

$apptype = $DB->getColumn("select type from pre_apps where appid='{$row['appid']}' limit 1"); //是否获取用户信息

if(isset($_POST['code'])){
	try{
		$wxqrcode = new \lib\WechatQrcode($config['mpappid'], $config['mpsecret']);
		$sessions = $wxqrcode->wxa_jscode2session($_POST['code']);
	}catch(Exception $e){
		exit(json_encode(['code'=>-1,'msg'=>$e->getMessage()]));
	}
	$openid = !empty($config['openappid']) ? $sessions['unionid'] : $sessions['openid'];
	$session_key = $sessions['session_key'];

	$account = $DB->getRow("SELECT * FROM pre_accounts WHERE appid=:appid AND type=:type AND openid=:openid LIMIT 1", [":appid"=>$row['appid'], ":type"=>$row['type'], ":openid"=>$openid]);
	if($account){
		$DB->exec("UPDATE `pre_accounts` SET `token`=:token,`ip`=:ip,`lasttime`=NOW() WHERE id=:id", [':token'=>$session_key, ':ip'=>$row['ip'], ':id'=>$account['id']]);
	}else{
		$DB->exec("INSERT INTO `pre_accounts` (`uid`, `appid`, `type`, `openid`, `token` ,`ip`, `addtime`, `lasttime`, `status`) VALUES (:uid, :appid, :type, :openid, :token, :ip, NOW(), NOW(), 1)", [':uid'=>$row['uid'], ':appid'=>$row['appid'], ':type'=>$row['type'], ':openid'=>$openid, ':token'=>$session_key, ':ip'=>$row['ip']]);
	}
	if($apptype==0){
		$DB->exec("UPDATE `pre_logs` SET `ucode`=:ucode,`openid`=:openid,`endtime`=NOW(),`status`=1 WHERE id=:id", [':ucode'=>'wxminiapp', ':openid'=>$openid, ':id'=>$row['id']]);
	}else{
		$DB->exec("UPDATE `pre_logs` SET `openid`=:openid WHERE id=:id", [':openid'=>$openid, ':id'=>$row['id']]);
	}
	$sessionkey = authcode("wxminiapp\t{$row['id']}", 'ENCODE', SYS_KEY);
	$result = ['code'=>0, 'openid'=>$openid, 'getUserInfo'=>$apptype==1, 'sessionkey'=>$sessionkey];
	exit(json_encode($result));
}
elseif(isset($_POST['rawData']) && isset($_POST['signature'])){

	if(!$row['openid'])exit(json_encode(['code'=>-1, 'msg'=>'用户Openid不存在']));

	$account = $DB->getRow("SELECT * FROM pre_accounts WHERE appid=:appid AND type=:type AND openid=:openid LIMIT 1", [":appid"=>$row['appid'], ":type"=>$row['type'], ":openid"=>$row['openid']]);
	if($account){
		$session_key = $account['token'];
		if(empty($_POST['signature']) || empty($_POST['rawData']) || sha1($_POST['rawData'].$session_key)!=$_POST['signature']){
			exit(json_encode(['code'=>-1, 'msg'=>'数据签名校验失败']));
		}
		$userinfo = json_decode($_POST['rawData'], true);
		$sex = '';
		if($userinfo['gender'] == 2)$sex = '女';
		elseif($userinfo['gender'] == 1)$sex = '男';
		$location = $userinfo['province'].$userinfo['city'];

		$DB->exec("UPDATE `pre_accounts` SET `nickname`=:nickname,`faceimg`=:faceimg,`location`=:location,`gender`=:gender WHERE id=:id", [':nickname'=>$userinfo['nickName'], ':faceimg'=>$userinfo['avatarUrl'], ':location'=>$location, ':gender'=>$sex, ':id'=>$account['id']]);

		$DB->exec("UPDATE `pre_logs` SET `ucode`=:ucode,`endtime`=NOW(),`status`=1 WHERE id=:id", [':ucode'=>'wxminiapp', ':id'=>$row['id']]);

		exit(json_encode(['code'=>0, 'msg'=>'succ']));
	}else{
		exit(json_encode(['code'=>-1, 'msg'=>'用户数据不存在']));
	}
}
elseif(isset($_POST['sessionkey']) && isset($_POST['nickname']) && isset($_FILES['avatar'])){

	if(!$row['openid'])exit(json_encode(['code'=>-1, 'msg'=>'用户Openid不存在']));
	if($row['status'] != 0)exit(json_encode(['code'=>-1, 'msg'=>'请勿重复请求']));

	$sessionkey = explode("\t", authcode($_POST['sessionkey'], 'DECODE', SYS_KEY));
	if(!$sessionkey || $sessionkey[0]!='wxminiapp' || $sessionkey[1]!=$row['id'] || strpos($_SERVER['HTTP_REFERER'],'/servicewechat.com/')===false)exit(json_encode(['code'=>-1, 'msg'=>'身份校验失败']));

	$account = $DB->getRow("SELECT * FROM pre_accounts WHERE appid=:appid AND type=:type AND openid=:openid LIMIT 1", [":appid"=>$row['appid'], ":type"=>$row['type'], ":openid"=>$row['openid']]);
	if($account){

		$nickname = htmlspecialchars(strip_tags(trim($_POST['nickname'])));
		if(empty($nickname))exit(json_encode(['code'=>-1, 'msg'=>'昵称不能为空']));
		if(strlen($nickname)>60)exit(json_encode(['code'=>-1, 'msg'=>'昵称过长']));

		$tmp_name = $_FILES['avatar']['tmp_name'];

		$size = filesize($tmp_name);
		if(!$size || $size<10)exit(json_encode(['code'=>-1, 'msg'=>'图片太小，请重新选择']));
		if($size>1024*1024*4)exit(json_encode(['code'=>-1, 'msg'=>'图片最大限制4MB，请重新选择']));
		if(!getimagesize($tmp_name))exit(json_encode(['code'=>-1, 'msg'=>'图片格式错误，请重新选择']));

		$hash = md5_file($tmp_name);
		$faceimg = ROOT.'assets/face/'.$hash.'.png';
		if(!move_uploaded_file($tmp_name, $faceimg))exit(json_encode(['code'=>-1, 'msg'=>'上传失败，可能无文件写入权限']));
		$faceurl = $siteurl.'assets/face/'.$hash.'.png';

		$DB->exec("UPDATE `pre_accounts` SET `nickname`=:nickname,`faceimg`=:faceimg WHERE id=:id", [':nickname'=>$nickname, ':faceimg'=>$faceurl, ':id'=>$account['id']]);

		$DB->exec("UPDATE `pre_logs` SET `ucode`=:ucode,`endtime`=NOW(),`status`=1 WHERE id=:id", [':ucode'=>'wxminiapp', ':id'=>$row['id']]);

		exit(json_encode(['code'=>0, 'msg'=>'succ']));
	}else{
		exit(json_encode(['code'=>-1, 'msg'=>'用户数据不存在']));
	}
}
else{
	exit(json_encode(['code'=>-1, 'msg'=>'参数不完整']));
}