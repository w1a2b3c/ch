<?php
$nosession=true;
include("./includes/common.php");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

@header('Content-Type: application/json; charset=UTF-8');

if(!checkRefererHost())exit('{"code":403}');

switch ($act) {
case 'login':
	$state = isset($_GET['state'])?$_GET['state']:exit('{"code":-2,"msg":"No state"}');
	$array = explode('||||',authcode2($state, 'DECODE', SYS_KEY));
	$type = $array[0];
	$logid = $array[1];
	if(!$type || !$logid)exit('{"code":-1,"msg":"参数校验失败"}');
	$row = $DB->getRow("select * from pre_logs where id=:id limit 1", ["id"=>$logid]);
	if($row && !empty($row['ucode'])){
		if(strtotime($row['addtime'])<time()-60*10)exit(json_encode(['code'=>-1, 'msg'=>'登录超时，请返回重试']));
		$redirect_uri = '';
		if(strpos($row['redirect'], '?')!==false){
			$redirect_uri .= $row['redirect'].'&';
		}else{
			$redirect_uri .= $row['redirect'].'?';
		}
		$redirect_uri .= 'type='.$type.'&code='.urlencode($row['code']).'&state='.urlencode($row['state']);
		$result=array("code"=>0,"msg"=>"登录成功！正在跳转...","url"=>$redirect_uri);
	}else{
		$result=array("code"=>1);
	}
	exit(json_encode($result));
break;
default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}