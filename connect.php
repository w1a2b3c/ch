<?php
/**
 * 快捷登录RESTAPI接口
**/
$nosession=true;
include './includes/common.php';

@header('Content-Type: application/json; charset=UTF-8');

$act=isset($_GET['act'])?$_GET['act']:exit('{"code":-1,"errcode":101,"msg":"no act"}');
$appid=isset($_GET['appid'])?$_GET['appid']:exit('{"code":-1,"errcode":101,"msg":"no appid"}');
$appkey = isset($_GET['appkey'])?$_GET['appkey']:exit('{"code":-1,"errcode":101,"msg":"no appkey"}');
$type=isset($_GET['type'])?$_GET['type']:'qq';

$approw = $DB->getRow("select * from pre_apps where appid=:appid limit 1", [':appid'=>$appid]);
if(!$approw)exit('{"code":-1,"errcode":102,"msg":"应用appid不存在"}');
if($approw['status']==0)exit('{"code":-1,"errcode":102,"msg":"应用已关闭"}');
if($approw['status']==2)exit('{"code":-1,"errcode":102,"msg":"当前应用正在审核中"}');
if($approw['status']==3)exit('{"code":-1,"errcode":102,"msg":"当前应用审核未通过"}');
if($appkey!=$approw['appkey'])exit('{"code":-1,"errcode":103,"msg":"appkey不正确"}');

if($act=='login')
{
	if($approw['uid']>0){
		$userrow=$DB->getRow("SELECT * FROM pre_user WHERE uid='{$approw['uid']}'");
		if($userrow && $userrow['status']==0)exit('{"code":-1,"errcode":103,"msg":"应用所属用户被封禁"}');
		if($userrow['gexpire']!=null && strtotime($userrow['gexpire'])<time()){
			$DB->exec("UPDATE pre_user SET gid=0,gexpire=NULL WHERE uid='{$userrow['uid']}'");
			$userrow['gid']=0;
		}
		$group = $DB->getRow("SELECT * FROM pre_group WHERE gid='{$userrow['gid']}'");
		if($group){
			$groupinfo = json_decode($group['info'], true);
			if($groupinfo && isset($groupinfo[$type]) && $groupinfo[$type]=='0')exit('{"code":-1,"errcode":103,"msg":"该登录方式无法使用"}');
			if($group['ucount']>0){
				$ucount=$DB->getColumn("SELECT count(*) FROM pre_accounts WHERE uid='{$approw['uid']}'");
				if($ucount >= $group['ucount'])exit('{"code":-1,"errcode":103,"msg":"已达到账号数量上限'.$group['ucount'].'，请购买或升级会员！"}');
			}
		}
	}
	$redirect_uri=isset($_GET['redirect_uri'])?$_GET['redirect_uri']:exit('{"code":-1,"errcode":101,"msg":"no redirect_uri"}');
	$urlarr = parse_url($redirect_uri);
    if ($conf['domainlimit']==1 && $approw['limit']==1) {
		if($conf['domaincheck']==1){
			$domainrow=$DB->getAll("select domain from pre_appdomain where appid=:appid and (domain=:domain or domain=:domain2)", [':appid'=>$appid, ':domain'=>$urlarr['host'], ':domain2'=>get_host($urlarr['host'])]);
		}else{
			$domainrow=$DB->getAll("select domain from pre_appdomain where appid=:appid and domain=:domain", [':appid'=>$appid, ':domain'=>$urlarr['host']]);
		}
		if(!$domainrow)exit('{"code":-1,"errcode":103,"msg":"回调域名未授权"}');
    }
	$state = $_GET['state'];
	$connect = new \lib\Connect($appid, $approw['uid']);
	$result = $connect->login($type, $redirect_uri, $state);
	exit(json_encode($result));
}
elseif($act=='callback')
{
	$code = isset($_GET['code'])?trim($_GET['code']):exit('{"code":-1,"errcode":101,"msg":"no code"}');
	$connect = new \lib\Connect($appid, $approw['uid']);
	$result = $connect->callback($code, $approw['type']);
	exit(json_encode($result));
}
elseif($act=='query')
{
	$social_uid = isset($_GET['social_uid'])?trim($_GET['social_uid']):exit('{"code":-1,"errcode":101,"msg":"social_uid不能为空"}');
	$connect = new \lib\Connect($appid, $approw['uid']);
	$result = $connect->query($type, $social_uid);
	exit(json_encode($result));
}